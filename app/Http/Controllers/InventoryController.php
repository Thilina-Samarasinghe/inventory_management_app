<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\InventoryTransaction;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\DeductInventoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class InventoryController extends Controller
{
    /**
     * Display a listing of items with search.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $unitType = $request->input('unit_type');

        $items = Item::query()
            ->search($search)
            ->byUnitType($unitType)
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Items/Index', [
            'items' => $items,
            'filters' => [
                'search' => $search,
                'unit_type' => $unitType,
            ],
        ]);
    }

    /**
     * Show the form for creating new items.
     */
    public function create()
    {
        return Inertia::render('Items/Create');
    }

    /**
     * Store new items (single or bulk).
     */
    public function store(StoreInventoryRequest $request)
    {
        try {
            $items = DB::transaction(function () use ($request) {
                $batchId = count($request->items) > 1 ? 'batch-' . Str::uuid() : null;
                $createdItems = [];

                foreach ($request->items as $itemData) {
                    // Create item
                    $item = Item::create([
                        'name' => $itemData['name'],
                        'description' => $itemData['description'] ?? null,
                        'unit_type' => $itemData['unit_type'],
                        'current_quantity' => $itemData['quantity'],
                        'minimum_quantity' => $itemData['minimum_quantity'] ?? null,
                    ]);

                    // Record transaction
                    InventoryTransaction::create([
                        'item_id' => $item->id,
                        'transaction_type' => 'addition',
                        'quantity' => $itemData['quantity'],
                        'previous_quantity' => 0,
                        'new_quantity' => $itemData['quantity'],
                        'notes' => 'Initial stock',
                        'batch_id' => $batchId,
                        'user_id' => auth()->id(),
                    ]);

                    $createdItems[] = $item;
                }

                return $createdItems;
            });

            $message = count($items) === 1 
                ? 'Item added successfully!' 
                : count($items) . ' items added successfully!';

            return redirect()->route('items.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add items: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item)
    {
        $item->load(['transactions' => function ($query) {
            $query->with('user')->orderByDesc('created_at');
        }]);

        return Inertia::render('Items/Show', [
            'item' => $item,
        ]);
    }

    /**
     * Update the specified item.
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit_type' => ['required', 'in:kg,m,cm,units'],
            'minimum_quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        $item->update($request->only(['name', 'description', 'unit_type', 'minimum_quantity']));

        return back()->with('success', 'Item updated successfully!');
    }

    /**
     * Remove the specified item (soft delete).
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully!');
    }

    /**
     * Show the form for deducting items.
     */
    public function createDeduct()
    {
        $items = Item::orderBy('name')->get();
        
        return Inertia::render('Items/Deduct', [
            'items' => $items,
        ]);
    }

    /**
     * Deduct quantities from items.
     */
    public function deduct(DeductInventoryRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $batchId = count($request->deductions) > 1 ? 'batch-' . Str::uuid() : null;

                foreach ($request->deductions as $deduction) {
                    $item = Item::findOrFail($deduction['id']);
                    $previousQuantity = $item->current_quantity;
                    $newQuantity = $previousQuantity - $deduction['quantity'];

                    // Update item quantity
                    $item->update(['current_quantity' => $newQuantity]);

                    // Record transaction
                    InventoryTransaction::create([
                        'item_id' => $item->id,
                        'transaction_type' => 'deduction',
                        'quantity' => $deduction['quantity'],
                        'previous_quantity' => $previousQuantity,
                        'new_quantity' => $newQuantity,
                        'notes' => $deduction['notes'] ?? null,
                        'batch_id' => $batchId,
                        'user_id' => auth()->id(),
                    ]);
                }
            });

            $message = count($request->deductions) === 1 
                ? 'Item deducted successfully!' 
                : count($request->deductions) . ' items deducted successfully!';

            return redirect()->route('items.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to deduct items: ' . $e->getMessage());
        }
    }

    /**
     * Display transaction history for an item.
     */
    public function history(Item $item)
    {
        $transactions = $item->transactions()
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Items/History', [
            'item' => $item,
            'transactions' => $transactions,
        ]);
    }
}