@extends('layouts.app')

@section('title', 'Business settings')

@section('content')
<x-page-header title="Business settings" subtitle="These details appear across the system and on printed invoices." />

<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-main-side">
        <div>
            <x-card title="Business profile">
                <div class="form-grid">
                    <x-form.input name="name" label="Business name" :value="$settings->name" required maxlength="255" wrapper-class="span-2" />
                    <x-form.input name="tagline" label="Tagline" :value="$settings->tagline" maxlength="255" placeholder="e.g. Wholesale Footwear Distributors" wrapper-class="span-2" />
                    <x-form.textarea name="address" label="Address" :value="$settings->address" maxlength="500" rows="2" wrapper-class="span-2" />
                    <x-form.input name="phone" label="Phone" type="tel" :value="$settings->phone" maxlength="50" />
                    <x-form.input name="mobile" label="Mobile" type="tel" :value="$settings->mobile" maxlength="50" />
                    <x-form.input name="email" label="Email" type="email" :value="$settings->email" maxlength="255" />
                    <x-form.input name="website" label="Website" type="url" :value="$settings->website" maxlength="255" placeholder="https://" />
                    <x-form.input name="registration_no" label="Business registration no." :value="$settings->registration_no" maxlength="100" />
                    <x-form.input name="tax_no" label="Tax / VAT no." :value="$settings->tax_no" maxlength="100" />
                </div>
            </x-card>

            <x-card title="Invoicing">
                <div class="form-grid form-grid--3">
                    <x-form.input name="currency_symbol" label="Currency symbol" :value="$settings->currency_symbol" required maxlength="10" />
                    <x-form.input name="invoice_prefix" label="Invoice number prefix" :value="$settings->invoice_prefix" required maxlength="20"
                                  :hint="'Next invoice: '.$settings->invoice_prefix.str_pad((string) max(1, (int) $settings->invoice_next_number), 5, '0', STR_PAD_LEFT)" />
                    <x-form.input name="default_due_days" label="Default credit days" type="number" min="0" max="365" :value="$settings->default_due_days" required />
                </div>
            </x-card>
        </div>

        <div>
            <x-card title="Logo" subtitle="PNG, JPG or WEBP up to 2 MB.">
                <div class="logo-preview mb-2">
                    @if ($settings->logoUrl())
                        <img src="{{ $settings->logoUrl() }}" alt="Current logo">
                    @else
                        <span class="sidebar__brand-mark">{{ mb_strtoupper(mb_substr($settings->name, 0, 1)) }}</span>
                        <span class="text-muted">No logo uploaded</span>
                    @endif
                </div>
                <x-form.input name="logo" type="file" label="Upload new logo" accept="image/png,image/jpeg,image/webp" />
                @if ($settings->logo_path)
                    <x-form.checkbox name="remove_logo" label="Remove current logo" wrapper-class="mt-1" />
                @endif
            </x-card>

            <x-card>
                <button type="submit" class="btn btn-primary btn-block"><x-icon name="check" /> Save settings</button>
            </x-card>
        </div>
    </div>
</form>
@endsection
