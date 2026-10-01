@extends('layouts.app')

@section('title', 'New invoice')

@section('content')
<x-page-header title="New wholesale invoice">
    <x-slot:breadcrumb><a href="{{ route('invoices.index') }}">Invoices</a> / New</x-slot:breadcrumb>
</x-page-header>

<form method="POST" action="{{ route('invoices.store') }}" data-submit-once>
    @csrf
    @include('invoices._form')
    <div class="form-actions mt-3">
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><x-icon name="check" /> Create invoice</button>
    </div>
</form>
@endsection
