@extends('layouts.app')

@section('title', 'System users')

@section('content')
<x-page-header title="System users" subtitle="People who can sign in to this system.">
    <x-slot:actions>
        <a href="{{ route('users.create') }}" class="btn btn-primary"><x-icon name="plus" /> Add user</a>
    </x-slot:actions>
</x-page-header>

<x-card :flush="true">
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last login</th><th></th></tr></thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>
                        <div class="actions actions--start">
                            <span class="avatar">{{ $user->initials() }}</span>
                            <span class="cell-title">{{ $user->name }}</span>
                            @if (auth()->user()->is($user))<x-badge tone="info">You</x-badge>@endif
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td><x-badge :tone="$user->isAdmin() ? 'warning' : 'muted'">{{ $user->role->label() }}</x-badge></td>
                    <td>@if ($user->is_active)<x-badge tone="success">Active</x-badge>@else<x-badge tone="danger">Disabled</x-badge>@endif</td>
                    <td class="text-muted nowrap">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-ghost btn-sm btn-icon" title="Edit"><x-icon name="pencil" /><span class="sr-only">Edit</span></a>
                            @unless (auth()->user()->is($user))
                                <x-delete-button :action="route('users.destroy', $user)" :icon-only="true" confirm="Delete user {{ $user->name }}?" />
                            @endunless
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</x-card>
@endsection
