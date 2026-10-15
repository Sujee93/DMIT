<?php

namespace App\Http\Controllers;

use App\Enums\ChequeStatus;
use App\Models\Cheque;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Register of cheques received from customers and what happened to each one.
 */
class ChequeController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function index(Request $request): View
    {
        $filter = (string) $request->query('status', 'available');
        $search = $request->string('q')->trim()->limit(100, '')->toString();

        $query = Cheque::query()
            ->with(['customer:id,name,company', 'invoice:id,invoice_no', 'supplierPayment:id,supplier_id,payment_date', 'supplierPayment.supplier:id,name,company']);

        match ($filter) {
            'available' => $query->available(),
            'expired' => $query->expired(),
            'all' => null,
            default => ($status = ChequeStatus::tryFrom($filter)) ? $query->where('status', $status) : $query->available(),
        };

        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($q) use ($like) {
                $q->where('cheque_no', 'like', $like)
                    ->orWhere('bank', 'like', $like)
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', $like)->orWhere('company', 'like', $like));
            });
        }

        $cheques = $query->orderBy('cheque_date')->orderBy('id')->paginate(30)->withQueryString();

        return view('cheques.index', [
            'cheques' => $cheques,
            'filter' => $filter,
            'search' => $search,
            'summary' => $this->summary(),
        ]);
    }

    public function deposit(Request $request, Cheque $cheque): RedirectResponse
    {
        $data = $request->validate(['deposited_on' => ['required', 'date', 'before_or_equal:'.now()->addDay()->toDateString()]]);
        $this->payments->depositCheque($cheque, $data['deposited_on']);

        return back()->with('success', "Cheque {$cheque->cheque_no} marked as deposited.");
    }

    public function undoDeposit(Cheque $cheque): RedirectResponse
    {
        $this->authorize('admin');
        $this->payments->undoDeposit($cheque);

        return back()->with('success', "Cheque {$cheque->cheque_no} moved back to in hand.");
    }

    public function markReturned(Request $request, Cheque $cheque): RedirectResponse
    {
        $this->authorize('admin');
        $data = $request->validate([
            'returned_on' => ['required', 'date', 'before_or_equal:'.now()->addDay()->toDateString()],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->payments->returnCheque($cheque, $data['returned_on'], $data['notes'] ?? null);

        return back()->with('success', "Cheque {$cheque->cheque_no} marked as returned. The customer's invoice is owed again.");
    }

    /**
     * @return array<string, array{count: int, amount: float}>
     */
    private function summary(): array
    {
        $row = fn ($query) => ['count' => (clone $query)->count(), 'amount' => (float) (clone $query)->sum('amount')];

        $warnDays = (int) config('distribution.cheque_expiry_warning_days', 14);
        $months = Cheque::validMonths();

        return [
            'available' => $row(Cheque::query()->available()),
            'expiring' => $row(Cheque::query()->available()->whereDate('cheque_date', '<=', today()->addDays($warnDays)->subMonthsNoOverflow($months)->toDateString())),
            'expired' => $row(Cheque::query()->expired()),
            'endorsed' => $row(Cheque::query()->where('status', ChequeStatus::Endorsed)),
            'deposited' => $row(Cheque::query()->where('status', ChequeStatus::Deposited)),
            'returned' => $row(Cheque::query()->where('status', ChequeStatus::Returned)),
        ];
    }
}
