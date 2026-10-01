@extends('layouts.app')

@section('title', 'Edit '.$product->code)

@section('content')
<x-page-header :title="'Edit '.$product->code">
    <x-slot:breadcrumb><a href="{{ route('products.index') }}">Products</a> / {{ $product->code }}</x-slot:breadcrumb>
</x-page-header>

<form method="POST" action="{{ route('products.update', $product) }}">
    @csrf
    @method('PUT')
    <x-card title="Product details">
        @include('products._form')
        <x-slot:footer>
            <div class="form-actions">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </x-slot:footer>
    </x-card>
</form>
@endsection
