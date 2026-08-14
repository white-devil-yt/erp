<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'company_name' => config('app.name', 'My Business'),
            'company_tagline' => 'Business Suite',
            'company_logo' => '',
            'company_address' => '123 Business Park, Main Street, Mumbai, India',
            'company_phone' => '+91 98765 43210',
            'company_email' => 'info@company.com',
            'company_gst' => '27AABCU9603R1ZM',
            'currency_symbol' => '₹',
            'invoice_prefix' => 'INV-',
            'invoice_footer' => 'Thank you for your business!',
            'invoice_terms' => 'Goods once sold will not be taken back. Subject to local jurisdiction.',
            'invoice_default_tax_rate' => '18',
            'invoice_show_gst' => '1',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
