<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function settings()
    {
        return view('billing.settings');
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:150',
            'company_tagline' => 'nullable|string|max:150',
            'company_address' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:30',
            'company_email' => 'nullable|email|max:150',
            'company_gst' => 'nullable|string|max:50',
            'currency_symbol' => 'required|string|max:10',
            'invoice_prefix' => 'required|string|max:20',
            'invoice_footer' => 'nullable|string|max:255',
            'invoice_terms' => 'nullable|string',
            'invoice_default_tax_rate' => 'required|numeric|min:0|max:100',
            'invoice_show_gst' => 'boolean',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('logos', 'public');
            Setting::set('company_logo', $path);
        }

        return back()->with('success', 'Billing & company settings saved successfully.');
    }
}
