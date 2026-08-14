<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('customer')->latest();

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $sales = $query->paginate(10)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $productOptions = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'price' => (float) $p->sale_price,
                'stock' => (float) $p->stock_quantity,
                'unit' => $p->unit,
            ];
        })->values();

        return view('sales.create', compact('customers', 'products', 'productOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'discount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $lines = [];
        $subtotal = 0;

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->stock_quantity < $item['quantity']) {
                return back()->withInput()->with('error', "Insufficient stock for {$product->name} (available: {$product->stock_quantity} {$product->unit}).");
            }
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $subtotal += $lineTotal;
            $lines[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $lineTotal,
            ];
        }

        $taxAmount = $subtotal * $data['tax_rate'] / 100;
        $total = $subtotal + $taxAmount - $data['discount'];
        $paid = $data['payment_status'] === 'paid' ? $total : ($data['payment_status'] === 'partial' ? $total / 2 : 0);

        $sale = Sale::create([
            'invoice_number' => nextDocumentNumber('invoice_prefix', Sale::class, 'invoice_number'),
            'customer_id' => $data['customer_id'],
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $subtotal,
            'tax_rate' => $data['tax_rate'],
            'tax_amount' => $taxAmount,
            'discount' => $data['discount'],
            'total' => $total,
            'amount_paid' => $paid,
            'balance_due' => $total - $paid,
            'payment_status' => $data['payment_status'],
            'payment_method' => $data['payment_method'],
            'notes' => $data['notes'],
            'status' => 'completed',
        ]);

        foreach ($lines as $line) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $line['product']->id,
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'total' => $line['total'],
            ]);

            $line['product']->decrement('stock_quantity', $line['quantity']);
            StockMovement::create([
                'product_id' => $line['product']->id,
                'type' => 'out',
                'quantity' => $line['quantity'],
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'notes' => 'Sale '.$sale->invoice_number,
            ]);
        }

        app(AccountingService::class)->postSale($sale);

        return redirect()->route('sales.show', $sale)->with('success', 'Invoice '.$sale->invoice_number.' created successfully.');
    }

    public function show(Sale $sale)
    {
        $sale->load('customer', 'items.product');

        return view('sales.show', compact('sale'));
    }

    public function print(Sale $sale)
    {
        $sale->load('customer', 'items.product');

        return view('sales.print', compact('sale'));
    }

    public function recordPayment(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|gt:0',
            'payment_method' => 'required|string',
        ]);

        $newPaid = $sale->amount_paid + $data['amount'];
        if ($newPaid > $sale->total) {
            return back()->with('error', 'Payment amount exceeds the invoice total.');
        }

        $sale->update([
            'amount_paid' => $newPaid,
            'balance_due' => $sale->total - $newPaid,
            'payment_status' => $newPaid >= $sale->total ? 'paid' : 'partial',
            'payment_method' => $data['payment_method'],
        ]);

        app(AccountingService::class)->postSalePayment($sale, $data['amount'], $data['payment_method']);

        return back()->with('success', 'Payment of ₹'.number_format($data['amount'], 2).' recorded.');
    }

    public function destroy(Sale $sale)
    {
        foreach ($sale->items as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Invoice deleted and stock restored.');
    }
}
