<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class ProductionController extends Controller
{
    public function index()
    {
        // Fetch BOTH prototype and full production orders
        $orders = Order::with(['client', 'items'])
            ->whereIn('order_status', ['Prototype Approval', 'Full Production'])
            ->orderBy('updated_at', 'asc') // Oldest first (FIFO)
            ->get();

        return view('production.index', compact('orders'));
    }

    public function update(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);

        // Stage 1 -> Stage 2
        if ($order->order_status === 'Prototype Approval') {
            $order->update(['order_status' => 'Full Production']);
            $message = 'Prototype approved! Order ' . $order->po_number . ' is now in Full Production.';
        }
        // Stage 2 -> Stage 3
        elseif ($order->order_status === 'Full Production') {
            $order->update(['order_status' => 'Ready for Delivery']);
            $message = 'Order ' . $order->po_number . ' marked as Ready for Delivery.';
        }

        return back()->with('success', $message ?? 'Status updated.');
    }
}
