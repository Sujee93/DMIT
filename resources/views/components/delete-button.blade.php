@props(['action', 'confirm' => 'Are you sure? This cannot be undone.', 'label' => 'Delete', 'size' => 'sm', 'iconOnly' => false])
<form method="POST" action="{{ $action }}" class="inline-form" data-confirm="{{ $confirm }}">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger {{ $size === 'sm' ? 'btn-sm' : '' }} {{ $iconOnly ? 'btn-icon' : '' }}" title="{{ $label }}">
        <x-icon name="trash" />@unless ($iconOnly)<span>{{ $label }}</span>@else<span class="sr-only">{{ $label }}</span>@endunless
    </button>
</form>
