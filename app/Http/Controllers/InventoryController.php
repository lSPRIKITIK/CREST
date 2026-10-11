<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index()
    {
        // Fetch all materials, ordered by category then name
        $inventory = Inventory::orderBy('category')->orderBy('item_name')->get();
        return view('inventory.index', compact('inventory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|string|in:Metal,Acrylic,Wood,Chemicals,Hardware,Other',
            'unit' => 'required|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'low_stock_threshold' => 'required|numeric|min:0',
        ]);

        Inventory::create($validated);

        return back()->with('success', 'New material successfully added to the system.');
    }

    public function adjust(Request $request, $id)
    {
        $validated = $request->validate([
            'adjustment_type' => 'required|string|in:add,deduct',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $item = Inventory::findOrFail($id);

        if ($validated['adjustment_type'] === 'add') {
            $item->increment('quantity', $validated['amount']);
            $message = "Successfully added {$validated['amount']} {$item->unit} to {$item->item_name}.";
        } else {
            // Prevent deducting more than what is available
            if ($item->quantity < $validated['amount']) {
                return back()->withErrors(['error' => "Cannot deduct {$validated['amount']}. Only {$item->quantity} {$item->unit} left in stock for {$item->item_name}."]);
            }
            $item->decrement('quantity', $validated['amount']);
            $message = "Successfully deducted {$validated['amount']} {$item->unit} from {$item->item_name}.";
        }

        return back()->with('success', $message);
    }

    public function destroy($id)
    {
        $item = Inventory::findOrFail($id);
        $item->delete();

        return back()->with('success', 'Material removed from inventory.');
    }
}
