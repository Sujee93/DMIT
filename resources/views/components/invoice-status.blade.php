@props(['invoice'])
@if ($invoice->isOverdue())
    <x-badge tone="danger">Overdue</x-badge>
@else
    <x-badge :tone="$invoice->status->tone()">{{ $invoice->status->label() }}</x-badge>
@endif
