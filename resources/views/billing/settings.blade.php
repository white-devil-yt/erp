@extends('layouts.app')

@section('title', 'Billing & Invoice Settings')
@section('page-title', 'Billing & Invoice Settings')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-sliders me-2 text-primary"></i>Customize Billing & Invoices</h4>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('billing.settings.save') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-building me-2 text-primary"></i>Company / Business</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Business Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', setting('company_name')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tagline</label>
                            <input type="text" name="company_tagline" class="form-control" value="{{ old('company_tagline', setting('company_tagline')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="company_address" rows="2" class="form-control">{{ old('company_address', setting('company_address')) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', setting('company_phone')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="company_email" class="form-control" value="{{ old('company_email', setting('company_email')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GST / Tax Number</label>
                            <input type="text" name="company_gst" class="form-control" value="{{ old('company_gst', setting('company_gst')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Currency Symbol</label>
                            <input type="text" name="currency_symbol" class="form-control" maxlength="10" value="{{ old('currency_symbol', setting('currency_symbol', '₹')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Logo</label>
                            @if (setting('company_logo'))
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . setting('company_logo')) }}" alt="Logo" style="max-height:60px;" class="border rounded p-1 bg-white">
                                </div>
                            @endif
                            <input type="file" name="company_logo" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Invoice Customization</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Invoice Number Prefix</label>
                            <input type="text" name="invoice_prefix" class="form-control" value="{{ old('invoice_prefix', setting('invoice_prefix', 'INV-')) }}">
                            <div class="form-text">Next invoice number is generated automatically.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Tax Rate (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="invoice_default_tax_rate" class="form-control" value="{{ old('invoice_default_tax_rate', setting('invoice_default_tax_rate', '18')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Invoice Footer Message</label>
                            <input type="text" name="invoice_footer" class="form-control" value="{{ old('invoice_footer', setting('invoice_footer')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Terms & Conditions</label>
                            <textarea name="invoice_terms" rows="3" class="form-control">{{ old('invoice_terms', setting('invoice_terms')) }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="invoice_show_gst" value="1" id="showGst" {{ setting('invoice_show_gst', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="showGst">Show GST / Tax Number on invoices</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        These settings appear on printed sales invoices and payslips.
                    </div>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection