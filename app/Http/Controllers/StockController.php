<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('status')) {
            if ($request->status === 'low') {
                $query->whereColumn('stock_quantity', '<=', 'low_stock_alert');
            } elseif ($request->status === 'out') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        $products = $query->orderBy('stock_quantity')->paginate(10)->withQueryString();

        $recentMovements = StockMovement::with('product')->latest()->limit(10)->get();

        return view('stock.index', compact('products', 'recentMovements'));
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|numeric|gt:0',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($data['type'] === 'in') {
            $product->increment('stock_quantity', $data['quantity']);
        } elseif ($data['type'] === 'out') {
            if ($product->stock_quantity < $data['quantity']) {
                return back()->with('error', 'Insufficient stock to remove ' . $data['quantity'] . ' ' . $product->unit . '.');
            }
            $product->decrement('stock_quantity', $data['quantity']);
        } else {
            $product->update(['stock_quantity' => $data['quantity']]);
        }

        StockMovement::create([
            'product_id' => $product->id,
            'type' => $data['type'],
            'quantity' => $data['type'] === 'adjustment' ? $data['quantity'] : $data['quantity'],
            'notes' => $data['notes'] ?? ($data['type'] === 'adjustment' ? 'Manual stock adjustment' : 'Manual stock ' . $data['type']),
        ]);

        return back()->with('success', 'Stock updated successfully.');
    }
}