<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier')->latest();

        if ($request->filled('search')) {
            $query->where('purchase_number', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $purchases = $query->paginate(10)->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $productOptions = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'cost' => (float) $p->purchase_price,
            ];
        })->values();

        return view('purchases.create', compact('suppliers', 'products', 'productOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'discount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $lines = [];
        $subtotal = 0;

        foreach ($data['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_cost'];
            $subtotal += $lineTotal;
            $lines[] = [
                'product' => Product::findOrFail($item['product_id']),
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'total' => $lineTotal,
            ];
        }

        $taxAmount = $subtotal * $data['tax_rate'] / 100;
        $total = $subtotal + $taxAmount - $data['discount'];
        $paid = $data['payment_status'] === 'paid' ? $total : ($data['payment_status'] === 'partial' ? $total / 2 : 0);

        $purchase = Purchase::create([
            'purchase_number' => 'PUR-'.str_pad((string) (Purchase::max('id') + 5001), 5, '0', STR_PAD_LEFT),
            'supplier_id' => $data['supplier_id'],
            'purchase_date' => $data['purchase_date'],
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
            'status' => 'received',
        ]);

        foreach ($lines as $line) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $line['product']->id,
                'quantity' => $line['quantity'],
                'unit_cost' => $line['unit_cost'],
                'total' => $line['total'],
            ]);

            $line['product']->increment('stock_quantity', $line['quantity']);
            $line['product']->update(['purchase_price' => $line['unit_cost']]);
            StockMovement::create([
                'product_id' => $line['product']->id,
                'type' => 'in',
                'quantity' => $line['quantity'],
                'reference_type' => Purchase::class,
                'reference_id' => $purchase->id,
                'notes' => 'Purchase '.$purchase->purchase_number,
            ]);
        }

        app(AccountingService::class)->postPurchase($purchase);

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase '.$purchase->purchase_number.' recorded successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.product');

        return view('purchases.show', compact('purchase'));
    }

    public function recordPayment(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|gt:0',
            'payment_method' => 'required|string',
        ]);

        $newPaid = $purchase->amount_paid + $data['amount'];
        if ($newPaid > $purchase->total) {
            return back()->with('error', 'Payment amount exceeds the purchase total.');
        }

        $purchase->update([
            'amount_paid' => $newPaid,
            'balance_due' => $purchase->total - $newPaid,
            'payment_status' => $newPaid >= $purchase->total ? 'paid' : 'partial',
            'payment_method' => $data['payment_method'],
        ]);

        app(AccountingService::class)->postPurchasePayment($purchase, $data['amount'], $data['payment_method']);

        return back()->with('success', 'Payment of ₹'.number_format($data['amount'], 2).' recorded.');
    }

    public function destroy(Purchase $purchase)
    {
        foreach ($purchase->items as $item) {
            $item->product->decrement('stock_quantity', $item->quantity);
        }
        $purchase->delete();

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted and stock deducted.');
    }
}
