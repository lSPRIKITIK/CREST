<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class ProductionController extends Controller
{
    public function index()
    {
        // Fetch orders that are currently on the workshop floor, including their item details
        $orders = Order::with(['client', 'items'])
            ->where('order_status', 'In Production')
            ->orderBy('updated_at', 'asc') // Oldest first (FIFO)
            ->get();

        return view('production.index', compact('orders'));
    }

    public function update(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);

        // Push the order to the next workflow stage
        $order->update([
            'order_status' => 'Ready for Delivery'
        ]);

        return back()->with('success', 'Order ' . $order->po_number . ' marked as Ready for Delivery.');
    }
}
