<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\InventoryTransaction;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_items' => Item::count(),
            'low_stock_items' => Item::lowStock()->count(),
            'out_of_stock_items' => Item::where('current_quantity', '<=', 0)->count(),
            'total_transactions' => InventoryTransaction::count(),
        ];

        $recentTransactions = InventoryTransaction::with(['item', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $lowStockItems = Item::lowStock()->take(5)->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'lowStockItems' => $lowStockItems,
        ]);
    }
}