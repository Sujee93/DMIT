<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reports) {}

    public function index(): View
    {
        return view('reports.index', ['summary' => $this->reports->summary()]);
    }

    public function sales(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'customer_id' => ['nullable', 'integer'],
        ]);

        $from = isset($validated['from']) ? Carbon::parse($validated['from']) : today()->startOfMonth();
        $to = isset($validated['to']) ? Carbon::parse($validated['to']) : today();
        $customerId = $validated['customer_id'] ?? null;

        return view('reports.sales', [
            ...$this->reports->sales($from, $to, $customerId),
            'from' => $from,
            'to' => $to,
            'customerId' => $customerId,
            'customers' => Contact::query()->customers()->orderBy('name')->get(['id', 'name', 'company']),
        ]);
    }

    public function receivables(): View
    {
        $rows = $this->reports->receivables();

        return view('reports.receivables', ['rows' => $rows, 'total' => $rows->sum('balance')]);
    }

    public function payables(): View
    {
        $rows = $this->reports->payables();

        return view('reports.payables', ['rows' => $rows, 'total' => $rows->sum('balance')]);
    }

    public function dues(): View
    {
        return view('reports.dues', $this->reports->dues());
    }
}
