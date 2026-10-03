@extends('layouts.app')

@section('title', 'Customers & Suppliers')

@section('content')
<x-page-header title="Customers & Suppliers" subtitle="Everyone you buy from and sell to, in one place.">
    <x-slot:actions>
        <a href="{{ route('contacts.create', ['type' => 'supplier']) }}" class="btn btn-secondary"><x-icon name="truck" /> Add supplier</a>
        <a href="{{ route('contacts.create', ['type' => 'customer']) }}" class="btn btn-primary"><x-icon name="plus" /> Add customer</a>
    </x-slot:actions>
</x-page-header>

<x-card :flush="true">
    <div class="filters card-toolbar">
        <nav class="tabs" aria-label="Contact type">
            <a href="{{ route('contacts.index', array_filter(['q' => $search])) }}" @class(['is-active' => $type === null])>
                All <span class="count">{{ $counts->sum() }}</span>
            </a>
            @foreach (\App\Enums\ContactType::cases() as $case)
                <a href="{{ route('contacts.index', array_filter(['type' => $case->value, 'q' => $search])) }}" @class(['is-active' => $type === $case])>
                    {{ $case->plural() }} <span class="count">{{ $counts[$case->value] ?? 0 }}</span>
                </a>
            @endforeach
        </nav>
        <form method="GET" class="filters push-right">
            @if ($type)<input type="hidden" name="type" value="{{ $type->value }}">@endif
            <div class="search-input">
                <x-icon name="search" />
                <input type="search" name="q" value="{{ $search }}" class="input" placeholder="Search name, code, company, phone" maxlength="100">
            </div>
        </form>
    </div>

    @if ($contacts->isEmpty())
        <x-empty-state title="No contacts found" icon="users">Add your customers and suppliers to start invoicing.</x-empty-state>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Name</th><th>Type</th><th>Phone</th><th>Email</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach ($contacts as $contact)
                    <tr>
                        <td>
                            <a href="{{ route('contacts.show', $contact) }}" class="cell-title">{{ $contact->name }}</a>
                            @if ($contact->code)<span class="badge badge-muted mono">{{ $contact->code }}</span>@endif
                            @if ($contact->company && $contact->company !== $contact->name)<div class="cell-sub">{{ $contact->company }}</div>@endif
                        </td>
                        <td><x-badge :tone="$contact->isCustomer() ? 'info' : 'warning'">{{ $contact->type->label() }}</x-badge></td>
                        <td class="nowrap">{{ $contact->phone ?: '—' }}</td>
                        <td>{{ $contact->email ?: '—' }}</td>
                        <td>@if ($contact->is_active)<x-badge tone="success">Active</x-badge>@else<x-badge>Inactive</x-badge>@endif</td>
                        <td>
                            <div class="actions">
                                @if ($contact->isSupplier())
                                    <a href="{{ route('supplier-payments.create', ['supplier_id' => $contact->id]) }}" class="btn btn-soft btn-sm"><x-icon name="cash" /> Pay</a>
                                @else
                                    <a href="{{ route('invoices.create', ['customer_id' => $contact->id]) }}" class="btn btn-soft btn-sm"><x-icon name="document" /> Invoice</a>
                                @endif
                                <a href="{{ route('contacts.show', $contact) }}" class="btn btn-ghost btn-sm btn-icon" title="View"><x-icon name="eye" /><span class="sr-only">View</span></a>
                                <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-ghost btn-sm btn-icon" title="Edit"><x-icon name="pencil" /><span class="sr-only">Edit</span></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $contacts->links() }}
    @endif
</x-card>
@endsection
