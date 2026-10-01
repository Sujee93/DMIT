@extends('layouts.app')

@section('title', 'Add product')

@section('content')
<x-page-header title="Add product">
    <x-slot:breadcrumb><a href="{{ route('products.index') }}">Products</a> / New</x-slot:breadcrumb>
</x-page-header>

<form method="POST" action="{{ route('products.store') }}">
    @csrf
    <x-card title="Product details">
        @include('products._form')
        <x-slot:footer>
            <div class="form-actions">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save product</button>
            </div>
        </x-slot:footer>
    </x-card>
</form>
@endsection
