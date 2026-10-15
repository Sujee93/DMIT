@extends('layouts.app')

@section('title', 'New GRN')

@section('content')
<x-page-header title="New GRN (goods received)" subtitle="Record stock received from a supplier and their bill value.">
    <x-slot:breadcrumb><a href="{{ route('grns.index') }}">GRN</a> / New</x-slot:breadcrumb>
</x-page-header>

<form method="POST" action="{{ route('grns.store') }}" data-submit-once>
    @csrf
    @include('grns._form')
    <div class="form-actions mt-3">
        <a href="{{ route('grns.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><x-icon name="check" /> Save GRN &amp; add stock</button>
    </div>
</form>
@endsection
