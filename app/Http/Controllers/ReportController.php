<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Payslip;
use App\Models\Sale;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $from = $request->filled('from') ? Carbon::parse($request->from) : Carbon::now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to) : Carbon::now();

        $sales = Sale::with('customer')
            ->whereBetween('invoice_date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->orderBy('invoice_date')
            ->get();

        $summary = [
            'count' => $sales->count(),
            'subtotal' => $sales->sum('subtotal'),
            'tax' => $sales->sum('tax_amount'),
            'discount' => $sales->sum('discount'),
            'total' => $sales->sum('total'),
            'paid' => $sales->sum('amount_paid'),
            'due' => $sales->sum('balance_due'),
        ];

        $daily = $sales->groupBy(fn ($s) => Carbon::parse($s->invoice_date)->format('Y-m-d'))
            ->map(fn ($group) => $group->sum('total'))
            ->sortKeys();

        $topCustomers = $sales->groupBy('customer_id')
            ->map(fn ($group) => [
                'name' => $group->first()->customer->name ?? 'Walk-in',
                'total' => $group->sum('total'),
            ])
            ->sortByDesc('total')
            ->take(5);

        return view('reports.sales', compact('sales', 'from', 'to', 'summary', 'daily', 'topCustomers'));
    }

    public function stock(Request $request)
    {
        $products = Product::with('category')->get();
        $lowStock = $products->filter(fn ($p) => $p->isLowStock());

        $movements = StockMovement::with('product')
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->latest()
            ->limit(100)
            ->get();

        $stockValue = $products->sum(fn ($p) => $p->stock_quantity * $p->purchase_price);
        $saleValue = $products->sum(fn ($p) => $p->stock_quantity * $p->sale_price);

        return view('reports.stock', compact('products', 'lowStock', 'movements', 'stockValue', 'saleValue'));
    }

    public function payroll(Request $request)
    {
        $months = Payslip::select('month')->distinct()->orderByDesc('month')->pluck('month');
        $month = $request->filled('month') ? $request->month : Carbon::now()->format('Y-m');

        $payslips = Payslip::with('employee.department')->where('month', $month)->get();

        $summary = [
            'count' => $payslips->count(),
            'basic' => $payslips->sum('basic_salary'),
            'allowances' => $payslips->sum('allowances'),
            'deductions' => $payslips->sum('deductions'),
            'overtime' => $payslips->sum('overtime_amount'),
            'bonus' => $payslips->sum('bonus'),
            'net' => $payslips->sum('net_salary'),
        ];

        $byDepartment = $payslips->groupBy(fn ($p) => $p->employee->department->name ?? 'No Department')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'total' => $group->sum('net_salary'),
            ]);

        return view('reports.payroll', compact('payslips', 'months', 'month', 'summary', 'byDepartment'));
    }
}