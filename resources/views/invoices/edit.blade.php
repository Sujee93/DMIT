@extends('layouts.app')

@section('title', 'Edit '.$invoice->invoice_no)

@section('content')
<x-page-header :title="'Edit invoice '.$invoice->invoice_no">
    <x-slot:breadcrumb><a href="{{ route('invoices.index') }}">Invoices</a> / <a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_no }}</a> / Edit</x-slot:breadcrumb>
</x-page-header>

<form method="POST" action="{{ route('invoices.update', $invoice) }}" data-submit-once>
    @csrf
    @method('PUT')
    @include('invoices._form')
    <div class="form-actions mt-3">
        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><x-icon name="check" /> Save invoice</button>
    </div>
</form>
@endsection
