@extends('layouts.app')

@section('title', 'Edit '.$user->name)

@section('content')
<x-page-header :title="'Edit '.$user->name">
    <x-slot:breadcrumb><a href="{{ route('users.index') }}">System users</a> / {{ $user->name }}</x-slot:breadcrumb>
</x-page-header>

<form method="POST" action="{{ route('users.update', $user) }}">
    @csrf
    @method('PUT')
    <x-card title="Account">
        @include('users._form')
        <x-slot:footer>
            <div class="form-actions">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </x-slot:footer>
    </x-card>
</form>
@endsection
