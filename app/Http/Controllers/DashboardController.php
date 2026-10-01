<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\ReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(ReportService $reports): View
    {
        return view('dashboard', [
            'summary' => $reports->summary(),
            'recentInvoices' => Invoice::query()
                ->with('customer:id,name,company')
                ->latest('invoice_date')
                ->latest('id')
                ->limit(8)
                ->get(),
            'overdueInvoices' => Invoice::query()
                ->with('customer:id,name,company')
                ->overdue()
                ->orderBy('due_date')
                ->limit(5)
                ->get(),
        ]);
    }
}
