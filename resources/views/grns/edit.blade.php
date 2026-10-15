@extends('layouts.app')

@section('title', 'Edit '.$grn->grn_no)

@section('content')
<x-page-header :title="'Edit '.$grn->grn_no">
    <x-slot:breadcrumb><a href="{{ route('grns.index') }}">GRN</a> / <a href="{{ route('grns.show', $grn) }}">{{ $grn->grn_no }}</a> / Edit</x-slot:breadcrumb>
</x-page-header>

<div class="alert alert-info"><x-icon name="alert" /><div>Stock is corrected automatically. You cannot reduce a quantity below what has already been invoiced.</div></div>

<form method="POST" action="{{ route('grns.update', $grn) }}" data-submit-once>
    @csrf
    @method('PUT')
    @include('grns._form')
    <div class="form-actions mt-3">
        <a href="{{ route('grns.show', $grn) }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><x-icon name="check" /> Save GRN</button>
    </div>
</form>
@endsection
