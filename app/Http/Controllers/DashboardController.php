<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Payslip;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $month = Carbon::now()->format('Y-m');

        $stats = [
            'total_products' => Product::count(),
            'total_customers' => Customer::count(),
            'total_suppliers' => Supplier::count(),
            'total_employees' => Employee::count(),
            'low_stock' => Product::whereColumn('stock_quantity', '<=', 'low_stock_alert')->count(),
            'monthly_sales' => Sale::whereBetween('invoice_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total'),
            'monthly_purchases' => Purchase::whereBetween('purchase_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total'),
            'monthly_payroll' => Payslip::where('month', $month)->sum('net_salary'),
            'unpaid_invoices' => Sale::where('payment_status', '!=', 'paid')->sum('balance_due'),
        ];

        $last6Months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $label = $m->format('M Y');
            $last6Months->push([
                'label' => $label,
                'sales' => Sale::whereBetween('invoice_date', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->sum('total'),
                'purchases' => Purchase::whereBetween('purchase_date', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->sum('total'),
            ]);
        }

        $lowStockProducts = Product::with('category')
            ->whereColumn('stock_quantity', '<=', 'low_stock_alert')
            ->orderBy('stock_quantity')
            ->limit(5)
            ->get();

        $recentSales = Sale::with('customer')
            ->latest()
            ->limit(6)
            ->get();

        $recentPayslips = Payslip::with('employee')
            ->where('month', $month)
            ->orderByDesc('net_salary')
            ->limit(6)
            ->get();

        $pendingPayslips = Payslip::where('month', $month)->where('status', 'generated')->count();

        $categorySales = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(sale_items.total) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'stats', 'last6Months', 'lowStockProducts', 'recentSales',
            'recentPayslips', 'pendingPayslips', 'categorySales'
        ));
    }
}