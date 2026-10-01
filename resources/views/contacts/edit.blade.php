@extends('layouts.app')

@section('title', 'Edit '.$contact->name)

@section('content')
<x-page-header :title="'Edit '.$contact->name">
    <x-slot:breadcrumb><a href="{{ route('contacts.index') }}">Customers & Suppliers</a> / <a href="{{ route('contacts.show', $contact) }}">{{ $contact->name }}</a> / Edit</x-slot:breadcrumb>
</x-page-header>

<form method="POST" action="{{ route('contacts.update', $contact) }}">
    @csrf
    @method('PUT')
    <x-card title="Contact details">
        @include('contacts._form')
        <x-slot:footer>
            <div class="form-actions">
                <a href="{{ route('contacts.show', $contact) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </x-slot:footer>
    </x-card>
</form>

@can('admin')
    <x-card title="Danger zone" subtitle="Contacts with invoices or payments cannot be deleted - mark them inactive instead.">
        <x-delete-button :action="route('contacts.destroy', $contact)" label="Delete contact" size="md" confirm="Delete {{ $contact->name }}?" />
    </x-card>
@endcan
@endsection
