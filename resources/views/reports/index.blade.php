@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<x-page-header title="Reports" subtitle="Sales performance and who owes what." />

<div class="grid grid-4 mb-2">
    <x-stat label="Sales this month" :value="money($summary['sales_month'])" icon="trending" tone="blue" />
    <x-stat label="Receivables" :value="money($summary['receivable'])" icon="arrow-down" tone="sky" />
    <x-stat label="Payables" :value="money($summary['payable'])" icon="arrow-up" tone="amber" />
    <x-stat label="Overdue" :value="money($summary['overdue'])" icon="clock" tone="red" :hint="$summary['overdue_count'].' invoices'" />
</div>

<div class="report-links mt-3">
    @foreach ([
        ['reports.sales', 'trending', 'blue', 'Sales report', 'Invoices, revenue, cost and gross profit for any date range, with top products.'],
        ['reports.receivables', 'arrow-down', 'sky', 'Receivables', 'Outstanding balance for each customer - money you are waiting to collect.'],
        ['reports.payables', 'arrow-up', 'amber', 'Payables', 'What you owe each supplier after the payments you have made.'],
        ['reports.dues', 'clock', 'red', 'Dues & overdue', 'Invoices past their due date, grouped by how late they are.'],
    ] as [$route, $icon, $tone, $title, $text])
        <a href="{{ route($route) }}" class="card report-link">
            <div class="stat__icon tone-{{ $tone }}"><x-icon :name="$icon" /></div>
            <div>
                <h3>{{ $title }}</h3>
                <p>{{ $text }}</p>
            </div>
        </a>
    @endforeach
</div>
@endsection
