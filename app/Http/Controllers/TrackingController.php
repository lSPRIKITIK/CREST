<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class TrackingController extends Controller
{
    public function index()
    {
        // Fetch ALL orders, their clients, and their full payment history
        $orders = Order::with(['client', 'payments'])
            ->withSum('payments', 'amount')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tracking.index', compact('orders'));
}
}
