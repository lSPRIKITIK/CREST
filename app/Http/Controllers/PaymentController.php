<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function create()
    {
        $orders = \App\Models\Order::with('client')
            ->withSum('payments', 'amount')
            ->where('order_status', '!=', 'Completed') // Fetch ALL active orders
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payments.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'amount_paid' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:Cash,Online Payment,Bank Transfer,Cheque',
            'online_provider' => 'nullable|string',
            'bank_provider' => 'nullable|string',
            'custom_provider' => 'nullable|string',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        // STRICT SECURITY: Prevent bypassing the disabled dropdown
        if (!in_array($order->order_status, ['Pending Downpayment', 'Delivered'])) {
            return back()->withErrors(['amount_paid' => 'Error: This order is currently ' . $order->order_status . ' and is locked from receiving payments.']);
        }

        // Calculate exact expected amounts
        $totalPaid = \App\Models\Payment::where('order_id', $order->order_id)->sum('amount');
        $remainingBalance = round($order->total_amount - $totalPaid, 2);
        $exactDownpayment = round($order->total_amount / 2, 2);
        $inputAmount = round($validated['amount_paid'], 2);

        // STRICT PAYMENT VALIDATION: Force exact 50% splits only
        if ($order->order_status === 'Pending Downpayment') {
            // Client MUST pay exactly 50%
            if ($inputAmount !== $exactDownpayment) {
                return back()->withErrors(['amount_paid' => 'Error: You must pay exactly the 50% downpayment (₱' . number_format($exactDownpayment, 2) . ').']);
            }
        } elseif ($order->order_status === 'Delivered') {
            // Client MUST pay the exact remaining balance
            if ($inputAmount !== $remainingBalance) {
                return back()->withErrors(['amount_paid' => 'Error: You must pay the exact remaining final balance of ₱' . number_format($remainingBalance, 2) . '.']);
            }
        }

// ... (Keep the strict payment validation checks above this) ...

        // 1. Generate the receipt number BEFORE the transaction loop so we can use it in the success message
        $receiptNumber = 'REC-' . strtoupper(uniqid());

        DB::transaction(function () use ($validated, $order, $totalPaid, $inputAmount, $receiptNumber) {

            $finalPaymentMethod = $validated['payment_method'];
            if ($finalPaymentMethod === 'Online Payment') {
                $provider = $validated['online_provider'] === 'Others' ? $validated['custom_provider'] : $validated['online_provider'];
                if (!empty($provider)) $finalPaymentMethod .= ' - ' . $provider;
            } elseif ($finalPaymentMethod === 'Bank Transfer') {
                $provider = $validated['bank_provider'] === 'Others' ? $validated['custom_provider'] : $validated['bank_provider'];
                if (!empty($provider)) $finalPaymentMethod .= ' - ' . $provider;
            }

            $paymentType = ($totalPaid == 0) ? 'Downpayment (50%)' : 'Final (50%)';

            $payment = \App\Models\Payment::create([
                'order_id' => $order->order_id,
                'employee_id' => \Illuminate\Support\Facades\Auth::user()->employee->employee_id,
                'payment_date' => $validated['transaction_date'],
                'amount' => $inputAmount,
                'payment_method' => $finalPaymentMethod,
                'payment_type' => $paymentType,
                'reference_number' => $validated['reference_number'],
            ]);

            // 2. Save the generated receipt to the transactions table
            \App\Models\Transaction::create([
                'payment_id' => $payment->payment_id,
                'transaction_date' => $validated['transaction_date'],
                'receipt_number' => $receiptNumber,
            ]);

            // 3. Update workflow status to include Prototype Approval
            if ($order->order_status === 'Pending Downpayment') {
                $order->update(['order_status' => 'Prototype Approval']);
            } elseif ($order->order_status === 'Delivered') {
                $order->update(['order_status' => 'Completed']);
            }
        });

        // 4. Show the receipt number directly in the success message
        $newStatus = $order->fresh()->order_status;
        $message = $newStatus === 'Completed'
            ? 'Final payment recorded successfully! (Receipt #: ' . $receiptNumber . '). Order is now officially Completed.'
            : 'Downpayment recorded successfully! (Receipt #: ' . $receiptNumber . '). Order is now pending Prototype Approval.';

        return back()->with('success', $message);
    }
}
