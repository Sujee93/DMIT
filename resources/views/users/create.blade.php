@extends('layouts.app')

@section('title', 'Add user')

@section('content')
<x-page-header title="Add user">
    <x-slot:breadcrumb><a href="{{ route('users.index') }}">System users</a> / New</x-slot:breadcrumb>
</x-page-header>

<form method="POST" action="{{ route('users.store') }}">
    @csrf
    <x-card title="Account">
        @include('users._form')
        <x-slot:footer>
            <div class="form-actions">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create user</button>
            </div>
        </x-slot:footer>
    </x-card>
</form>
@endsection
