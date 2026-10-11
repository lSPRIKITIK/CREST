<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function create()
    {
        $clients = Client::orderBy('client_name')->get();
        return view('orders.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'required|string|max:255',
            'client_address' => 'required|string',
            'po_number' => 'required|string|unique:orders,po_number',
            'due_date' => 'required|date|after_or_equal:today',
            'item_type' => 'required|string|in:Medal,Trophy,Plaque',
            'specifications' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
// Change firstOrCreate to updateOrCreate
            $client = Client::updateOrCreate(
                ['client_name' => $validated['client_name']],
                [
                    'contact_person' => $validated['contact_person'],
                    'contact_number' => $validated['contact_number'],
                    'client_address' => $validated['client_address'],
                ]
            );

            // 2. Calculate Total Amount
            $totalAmount = $validated['quantity'] * $validated['unit_price'];

            // 3. Create the Order
            $order = Order::create([
                'client_id' => $client->client_id,
                'po_number' => $validated['po_number'],
                'order_date' => Carbon::today(),
                'due_date' => $validated['due_date'],
                'total_amount' => $totalAmount,
                'order_status' => 'Pending Downpayment',
            ]);

            // 4. Add the Order Item
            OrderItem::create([
                'order_id' => $order->order_id,
                'item_type' => $validated['item_type'],
                'specifications' => $validated['specifications'],
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
            ]);
        });

        return back()->with('success', 'Order created successfully. Ready for 50% downpayment.');
    }
}
