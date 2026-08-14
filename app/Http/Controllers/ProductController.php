<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('low_stock')) {
            $query->whereColumn('stock_quantity', '<=', 'low_stock_alert');
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.form', ['product' => new Product, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'sku' => 'required|string|max:50|unique:products,sku',
            'category_id' => 'nullable|exists:categories,id',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'low_stock_alert' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $product = Product::create($data);

        if ($product->stock_quantity > 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $product->stock_quantity,
                'notes' => 'Opening stock',
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $movements = $product->stockMovements()->latest()->limit(20)->get();
        return view('products.show', compact('product', 'movements'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'low_stock_alert' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}