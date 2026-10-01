@extends('layouts.app')

@section('title', 'Add '.$contact->type->label())

@section('content')
<x-page-header :title="'Add '.mb_strtolower($contact->type->label())">
    <x-slot:breadcrumb><a href="{{ route('contacts.index') }}">Customers & Suppliers</a> / New</x-slot:breadcrumb>
</x-page-header>

<form method="POST" action="{{ route('contacts.store') }}">
    @csrf
    <x-card title="Contact details">
        @include('contacts._form')
        <x-slot:footer>
            <div class="form-actions">
                <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </x-slot:footer>
    </x-card>
</form>
@endsection
