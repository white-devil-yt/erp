<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payslip;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ChartOfAccountsSeeder::class,
            SettingsSeeder::class,
        ]);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@company.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Furniture', 'slug' => 'furniture', 'description' => 'Office and home furniture'],
            ['name' => 'Stationery', 'slug' => 'stationery', 'description' => 'Office stationery supplies'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Apparel and textiles'],
        ];
        foreach ($categories as $c) {
            Category::create($c);
        }

        $products = [
            ['name' => 'Wireless Mouse', 'sku' => 'PRD-001', 'category' => 'electronics', 'unit' => 'pcs', 'purchase_price' => 350, 'sale_price' => 549, 'stock' => 120, 'alert' => 20],
            ['name' => 'Mechanical Keyboard', 'sku' => 'PRD-002', 'category' => 'electronics', 'unit' => 'pcs', 'purchase_price' => 1800, 'sale_price' => 2499, 'stock' => 45, 'alert' => 10],
            ['name' => '27" Monitor', 'sku' => 'PRD-003', 'category' => 'electronics', 'unit' => 'pcs', 'purchase_price' => 9500, 'sale_price' => 12999, 'stock' => 8, 'alert' => 5],
            ['name' => 'Office Chair', 'sku' => 'PRD-004', 'category' => 'furniture', 'unit' => 'pcs', 'purchase_price' => 3200, 'sale_price' => 4599, 'stock' => 30, 'alert' => 8],
            ['name' => 'Standing Desk', 'sku' => 'PRD-005', 'category' => 'furniture', 'unit' => 'pcs', 'purchase_price' => 14000, 'sale_price' => 18500, 'stock' => 12, 'alert' => 4],
            ['name' => 'A4 Paper (Ream)', 'sku' => 'PRD-006', 'category' => 'stationery', 'unit' => 'ream', 'purchase_price' => 280, 'sale_price' => 399, 'stock' => 400, 'alert' => 50],
            ['name' => 'Ballpoint Pens (Box)', 'sku' => 'PRD-007', 'category' => 'stationery', 'unit' => 'box', 'purchase_price' => 90, 'sale_price' => 150, 'stock' => 250, 'alert' => 40],
            ['name' => 'Polo T-Shirt', 'sku' => 'PRD-008', 'category' => 'clothing', 'unit' => 'pcs', 'purchase_price' => 350, 'sale_price' => 599, 'stock' => 3, 'alert' => 10],
            ['name' => 'Formal Shirt', 'sku' => 'PRD-009', 'category' => 'clothing', 'unit' => 'pcs', 'purchase_price' => 600, 'sale_price' => 999, 'stock' => 60, 'alert' => 15],
            ['name' => 'USB-C Cable', 'sku' => 'PRD-010', 'category' => 'electronics', 'unit' => 'pcs', 'purchase_price' => 120, 'sale_price' => 249, 'stock' => 180, 'alert' => 30],
        ];
        foreach ($products as $p) {
            Product::create([
                'category_id' => Category::where('slug', $p['category'])->first()->id,
                'name' => $p['name'],
                'sku' => $p['sku'],
                'unit' => $p['unit'],
                'purchase_price' => $p['purchase_price'],
                'sale_price' => $p['sale_price'],
                'stock_quantity' => $p['stock'],
                'low_stock_alert' => $p['alert'],
            ]);
        }

        $customers = [
            ['name' => 'Rahul Sharma', 'email' => 'rahul@gmail.com', 'phone' => '9876543210', 'company' => 'RS Traders', 'address' => '12 MG Road, Mumbai'],
            ['name' => 'Priya Patel', 'email' => 'priya@outlook.com', 'phone' => '9876501234', 'company' => 'Patel Enterprises', 'address' => '45 Ring Road, Surat'],
            ['name' => 'Amit Verma', 'email' => 'amit@yahoo.com', 'phone' => '9812345678', 'company' => 'Verma & Sons', 'address' => '8 Civil Lines, Delhi'],
            ['name' => 'Sneha Reddy', 'email' => 'sneha@gmail.com', 'phone' => '9765432109', 'company' => 'Reddy Retail', 'address' => '23 Jubilee Hills, Hyderabad'],
        ];
        foreach ($customers as $c) {
            Customer::create($c);
        }

        $suppliers = [
            ['name' => 'TechSource India', 'email' => 'sales@techsource.in', 'phone' => '9999000011', 'company' => 'TechSource Pvt Ltd', 'address' => 'IT Park, Pune'],
            ['name' => 'FurniMart', 'email' => 'orders@furnimart.in', 'phone' => '9999000022', 'company' => 'FurniMart Ltd', 'address' => 'Industrial Area, Ahmedabad'],
            ['name' => 'PaperPlus', 'email' => 'contact@paperplus.in', 'phone' => '9999000033', 'company' => 'PaperPlus Distributors', 'address' => 'Wholesale Market, Delhi'],
            ['name' => 'GarmentHub', 'email' => 'support@garmenthub.in', 'phone' => '9999000044', 'company' => 'GarmentHub Exports', 'address' => 'Textile Park, Ludhiana'],
        ];
        foreach ($suppliers as $s) {
            Supplier::create($s);
        }

        $departments = [
            ['name' => 'Management', 'description' => 'Leadership and administration'],
            ['name' => 'Sales', 'description' => 'Sales and business development'],
            ['name' => 'IT', 'description' => 'Information technology and support'],
            ['name' => 'HR', 'description' => 'Human resources and payroll'],
            ['name' => 'Finance', 'description' => 'Accounts and finance'],
        ];
        foreach ($departments as $d) {
            Department::create($d);
        }

        $employees = [
            ['code' => 'EMP-001', 'name' => 'Rohit Kumar', 'dept' => 'Management', 'position' => 'CEO', 'basic' => 120000, 'allow' => 25000, 'deduct' => 8000],
            ['code' => 'EMP-002', 'name' => 'Anjali Singh', 'dept' => 'Sales', 'position' => 'Sales Manager', 'basic' => 65000, 'allow' => 12000, 'deduct' => 4000],
            ['code' => 'EMP-003', 'name' => 'Vikram Joshi', 'dept' => 'IT', 'position' => 'Software Engineer', 'basic' => 55000, 'allow' => 10000, 'deduct' => 3500],
            ['code' => 'EMP-004', 'name' => 'Pooja Nair', 'dept' => 'HR', 'position' => 'HR Executive', 'basic' => 42000, 'allow' => 8000, 'deduct' => 2500],
            ['code' => 'EMP-005', 'name' => 'Sanjay Gupta', 'dept' => 'Finance', 'position' => 'Accountant', 'basic' => 48000, 'allow' => 9000, 'deduct' => 3000],
            ['code' => 'EMP-006', 'name' => 'Neha Kapoor', 'dept' => 'Sales', 'position' => 'Sales Executive', 'basic' => 32000, 'allow' => 5000, 'deduct' => 2000],
            ['code' => 'EMP-007', 'name' => 'Arjun Mehta', 'dept' => 'IT', 'position' => 'Support Engineer', 'basic' => 38000, 'allow' => 6000, 'deduct' => 2200],
        ];
        foreach ($employees as $e) {
            $dept = Department::where('name', $e['dept'])->first();
            Employee::create([
                'department_id' => $dept->id,
                'employee_code' => $e['code'],
                'name' => $e['name'],
                'email' => strtolower(str_replace(' ', '.', $e['name'])) . '@company.com',
                'phone' => '98' . random_int(10000000, 99999999),
                'position' => $e['position'],
                'joining_date' => Carbon::now()->subMonths(random_int(6, 36)),
                'basic_salary' => $e['basic'],
                'allowances' => $e['allow'],
                'deductions' => $e['deduct'],
                'bank_name' => 'HDFC Bank',
                'account_number' => (string) random_int(1000000000, 9999999999),
                'ifsc_code' => 'HDFC0001234',
                'status' => 'active',
            ]);
        }

        $now = Carbon::now();
        for ($i = 0; $i < 30; $i++) {
            $date = $now->copy()->subDays($i);
            foreach (Employee::all() as $emp) {
                $roll = random_int(0, 100);
                $status = $roll < 85 ? 'present' : ($roll < 92 ? 'half_day' : ($roll < 97 ? 'leave' : 'absent'));
                Attendance::create([
                    'employee_id' => $emp->id,
                    'date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'check_in' => $status === 'present' || $status === 'half_day' ? Carbon::createFromTime(9, random_int(0, 45))->format('H:i:s') : null,
                    'check_out' => $status === 'present' ? Carbon::createFromTime(18, random_int(0, 30))->format('H:i:s') : null,
                ]);
            }
        }

        $customerIds = Customer::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();

        for ($i = 0; $i < 20; $i++) {
            $date = $now->copy()->subDays(random_int(0, 60));
            $itemCount = random_int(1, 4);
            $subtotal = 0;
            $lines = [];
            $selected = collect($productIds)->random(min($itemCount, count($productIds)))->toArray();
            foreach ($selected as $pid) {
                $qty = random_int(1, 10);
                $price = Product::find($pid)->sale_price;
                $total = $qty * $price;
                $subtotal += $total;
                $lines[] = ['product_id' => $pid, 'quantity' => $qty, 'unit_price' => $price, 'total' => $total];
            }
            $taxRate = 18;
            $tax = $subtotal * $taxRate / 100;
            $discount = random_int(0, 2) == 0 ? $subtotal * 0.05 : 0;
            $grand = $subtotal + $tax - $discount;
            $paid = random_int(0, 2) == 0 ? $grand : (random_int(0, 1) == 0 ? $grand / 2 : 0);

            $sale = Sale::create([
                'invoice_number' => 'INV-' . str_pad((string) (1001 + $i), 5, '0', STR_PAD_LEFT),
                'customer_id' => $customerIds[array_rand($customerIds)],
                'invoice_date' => $date->format('Y-m-d'),
                'due_date' => $date->copy()->addDays(15)->format('Y-m-d'),
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $tax,
                'discount' => $discount,
                'total' => $grand,
                'amount_paid' => $paid,
                'balance_due' => $grand - $paid,
                'payment_status' => $paid >= $grand ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                'payment_method' => $paid > 0 ? ['cash', 'bank transfer', 'UPI'][random_int(0, 2)] : null,
                'status' => 'completed',
            ]);
            foreach ($lines as $line) {
                SaleItem::create(array_merge($line, ['sale_id' => $sale->id]));
                $product = Product::find($line['product_id']);
                $product->decrement('stock_quantity', $line['quantity']);
            }
        }

        $supplierIds = Supplier::pluck('id')->toArray();
        for ($i = 0; $i < 8; $i++) {
            $date = $now->copy()->subDays(random_int(0, 60));
            $itemCount = random_int(1, 3);
            $subtotal = 0;
            $lines = [];
            $selected = collect($productIds)->random(min($itemCount, count($productIds)))->toArray();
            foreach ($selected as $pid) {
                $qty = random_int(10, 50);
                $cost = Product::find($pid)->purchase_price;
                $total = $qty * $cost;
                $subtotal += $total;
                $lines[] = ['product_id' => $pid, 'quantity' => $qty, 'unit_cost' => $cost, 'total' => $total];
            }
            $taxRate = 18;
            $tax = $subtotal * $taxRate / 100;
            $grand = $subtotal + $tax;
            $paid = random_int(0, 1) == 0 ? $grand : $grand / 2;

            $purchase = Purchase::create([
                'purchase_number' => 'PUR-' . str_pad((string) (5001 + $i), 5, '0', STR_PAD_LEFT),
                'supplier_id' => $supplierIds[array_rand($supplierIds)],
                'purchase_date' => $date->format('Y-m-d'),
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $tax,
                'discount' => 0,
                'total' => $grand,
                'amount_paid' => $paid,
                'balance_due' => $grand - $paid,
                'payment_status' => $paid >= $grand ? 'paid' : 'partial',
                'payment_method' => ['cash', 'bank transfer'][random_int(0, 1)],
                'status' => 'received',
            ]);
            foreach ($lines as $line) {
                PurchaseItem::create(array_merge($line, ['purchase_id' => $purchase->id]));
                Product::find($line['product_id'])->increment('stock_quantity', $line['quantity']);
            }
        }

        $month = $now->format('Y-m');
        foreach (Employee::all() as $emp) {
            $days = Carbon::createFromFormat('Y-m', $month)->daysInMonth;
            $present = $emp->attendance()
                ->whereBetween('date', [$monthDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth(), Carbon::createFromFormat('Y-m', $month)->endOfMonth()])
                ->whereIn('status', ['present', 'half_day'])
                ->count();
            Payslip::create([
                'employee_id' => $emp->id,
                'month' => $month,
                'present_days' => $present,
                'total_working_days' => $days,
                'basic_salary' => $emp->basic_salary,
                'allowances' => $emp->allowances,
                'deductions' => $emp->deductions,
                'overtime_amount' => random_int(0, 3) == 0 ? random_int(1000, 5000) : 0,
                'bonus' => random_int(0, 2) == 0 ? random_int(2000, 10000) : 0,
                'net_salary' => $emp->basic_salary + $emp->allowances - $emp->deductions,
                'status' => random_int(0, 1) == 0 ? 'paid' : 'generated',
                'paid_date' => random_int(0, 1) == 0 ? $now->copy()->subDays(random_int(0, 10)) : null,
            ]);
        }
    }
}