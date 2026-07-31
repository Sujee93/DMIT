@php
    $filters = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        '7days' => 'Last 7 Days',
        'month' => 'This Month',
        'custom' => 'Custom',
    ];
@endphp

<div class="p-4 border-b border-gray-100">
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($filters as $key => $label)
            <a href="{{ route($routeName, $key === 'custom' ? ['filter' => $key, 'from' => $from->toDateString(), 'to' => $to->toDateString()] : ['filter' => $key]) }}"
               class="px-3 py-1.5 rounded-md text-sm {{ $filter === $key ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($filter === 'custom')
        <form method="GET" action="{{ route($routeName) }}" class="mt-3 flex flex-wrap items-end gap-3">
            <input type="hidden" name="filter" value="custom">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="from" value="{{ $from->toDateString() }}" class="text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="to" value="{{ $to->toDateString() }}" class="text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500">
            </div>
            <button type="submit" class="px-4 py-1.5 rounded-md bg-primary-600 text-white text-sm hover:bg-primary-700">Apply</button>
        </form>
    @endif

    <div class="mt-2 text-xs text-gray-500">
        Showing {{ $from->toDateString() }} to {{ $to->toDateString() }}
    </div>
</div>
