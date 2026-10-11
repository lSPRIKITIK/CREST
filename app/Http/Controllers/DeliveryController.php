<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class DeliveryController extends Controller
{
    public function index()
    {
        // Fetch orders waiting to be dispatched, loading the client data for the address
        $orders = Order::with('client')
            ->where('order_status', 'Ready for Delivery')
            ->orderBy('updated_at', 'asc') // Oldest waiting orders first
            ->get();

        return view('delivery.index', compact('orders'));
    }

    public function update(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);

        // Push the order to the next phase so Sales knows they can collect the final payment
        $order->update([
            'order_status' => 'Delivered'
        ]);

        return back()->with('success', 'Order ' . $order->po_number . ' has been successfully delivered.');
    }
}
