<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '1000', 'name' => 'Cash in Hand', 'type' => 'asset', 'group' => 'cash', 'is_system' => true, 'description' => 'Physical cash on hand'],
            ['code' => '1010', 'name' => 'Bank Account', 'type' => 'asset', 'group' => 'cash', 'is_system' => true, 'description' => 'Main business bank account'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset', 'group' => 'receivable', 'is_system' => true, 'description' => 'Money owed by customers'],
            ['code' => '1200', 'name' => 'Inventory', 'type' => 'asset', 'group' => 'inventory', 'is_system' => true, 'description' => 'Value of products in stock'],
            ['code' => '1210', 'name' => 'GST Input Credit', 'type' => 'asset', 'group' => 'tax', 'is_system' => true, 'description' => 'Input tax credit receivable'],
            ['code' => '1300', 'name' => 'Other Assets', 'type' => 'asset', 'group' => 'other', 'description' => 'Prepaid expenses and other assets'],

            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'group' => 'payable', 'is_system' => true, 'description' => 'Money owed to suppliers'],
            ['code' => '2100', 'name' => 'GST Output Payable', 'type' => 'liability', 'group' => 'tax', 'is_system' => true, 'description' => 'Output tax collected on sales'],
            ['code' => '2300', 'name' => 'Other Liabilities', 'type' => 'liability', 'group' => 'liability', 'description' => 'Other liabilities and accruals'],

            ['code' => '3000', 'name' => "Owner's Capital", 'type' => 'equity', 'group' => 'capital', 'is_system' => true, 'description' => 'Capital invested by the owner'],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity', 'group' => 'capital', 'description' => 'Accumulated profits'],

            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'income', 'group' => 'revenue', 'is_system' => true, 'description' => 'Revenue from product sales'],
            ['code' => '4100', 'name' => 'Other Income', 'type' => 'income', 'group' => 'revenue', 'description' => 'Interest and miscellaneous income'],

            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'group' => 'cogs', 'is_system' => true, 'description' => 'Direct cost of products sold'],
            ['code' => '5100', 'name' => 'Salaries & Wages', 'type' => 'expense', 'group' => 'expense', 'is_system' => true, 'description' => 'Employee salaries and wages'],
            ['code' => '5200', 'name' => 'Rent Expense', 'type' => 'expense', 'group' => 'expense', 'description' => 'Office and shop rent'],
            ['code' => '5300', 'name' => 'Utilities Expense', 'type' => 'expense', 'group' => 'expense', 'description' => 'Electricity, water and internet'],
            ['code' => '5400', 'name' => 'Office & Admin Expense', 'type' => 'expense', 'group' => 'expense', 'description' => 'General office expenses'],
            ['code' => '5500', 'name' => 'Miscellaneous Expense', 'type' => 'expense', 'group' => 'expense', 'description' => 'Sundry operating expenses'],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(['code' => $account['code']], $account);
        }
    }
}
