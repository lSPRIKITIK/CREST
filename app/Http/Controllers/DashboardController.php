<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Revenue (sum of all received payments)
        $totalRevenue = Payment::sum('amount');

        $monthlyRevenue = Payment::whereBetween('created_at', [
                now()->startOfMonth(), now()->endOfMonth()
            ])->sum('amount');

        $yearlyRevenue = Payment::whereBetween('created_at', [
                now()->startOfYear(), now()->endOfYear()
            ])->sum('amount');

// 2. Active Production (Trophies currently being crafted)
        $activeProduction = Order::where('order_status', 'In Production')->count();

        // 3. Logistics Queue (Trophies waiting for a driver)
        $pendingDeliveries = Order::where('order_status', 'Ready for Delivery')->count();

        // 4. Accounts Receivable (Waiting for downpayment or final balance)
        $pendingPayments = Order::whereIn('order_status', ['Pending Downpayment', 'Delivered'])->count();

        // 5. Recent Activity (Last 5 orders)
        $recentOrders = Order::with('client')
            ->withSum('payments', 'amount')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
        'totalRevenue',
        'monthlyRevenue',
        'yearlyRevenue',
        'activeProduction',
        'pendingDeliveries',
        'pendingPayments',
        'recentOrders'
        ));
    }
}
