<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Http\Requests\InvoiceRequest;
use App\Models\BusinessSetting;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoices) {}

    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->limit(100, '')->toString();
        $status = $request->query('status');
        $from = $this->validDate($request->query('from'));
        $to = $this->validDate($request->query('to'));

        $invoices = Invoice::query()
            ->with(['customer:id,name,company', 'supplier:id,name,company'])
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $q->where(function ($q) use ($like) {
                    $q->where('invoice_no', 'like', $like)
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', $like)->orWhere('company', 'like', $like));
                });
            })
            ->when($status === 'overdue', fn ($q) => $q->overdue())
            ->when(InvoiceStatus::tryFrom((string) $status), fn ($q, $s) => $q->where('status', $s))
            ->when($from, fn ($q) => $q->whereDate('invoice_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('invoice_date', '<=', $to))
            ->latest('invoice_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('invoices.index', compact('invoices', 'search', 'status', 'from', 'to'));
    }

    public function create(Request $request): View
    {
        $invoice = new Invoice([
            'invoice_date' => today(),
            'due_date' => today()->addDays(BusinessSetting::current()->default_due_days),
            'customer_id' => $request->integer('customer_id') ?: null,
            'supplier_id' => $request->integer('supplier_id') ?: null,
        ]);

        return view('invoices.create', $this->formData($invoice));
    }

    public function store(InvoiceRequest $request): RedirectResponse
    {
        $invoice = $this->invoices->create($request->validated(), $request->user());

        return redirect()->route('invoices.show', $invoice)->with('success', "Invoice {$invoice->invoice_no} created.");
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['customer', 'supplier', 'items', 'payments.creator:id,name', 'creator:id,name']);

        return view('invoices.show', [
            'invoice' => $invoice,
            'paymentMethods' => PaymentMethod::options(),
        ]);
    }

    public function print(Invoice $invoice): View
    {
        $invoice->load(['customer', 'items']);

        return view('invoices.print', compact('invoice'));
    }

    public function edit(Invoice $invoice): View|RedirectResponse
    {
        if ($invoice->hasPayments()) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'This invoice has payments recorded and can no longer be edited.');
        }

        $invoice->load('items');

        return view('invoices.edit', $this->formData($invoice));
    }

    public function update(InvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $invoice = $this->invoices->update($invoice, $request->validated());

        return redirect()->route('invoices.show', $invoice)->with('success', "Invoice {$invoice->invoice_no} updated.");
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('admin');

        $this->invoices->delete($invoice);

        return redirect()->route('invoices.index')->with('success', "Invoice {$invoice->invoice_no} deleted.");
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Invoice $invoice): array
    {
        $products = Product::query()
            ->active()
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'description', 'color', 'size', 'cost', 'price']);

        // Products already on the invoice stay selectable even if since deactivated.
        if ($invoice->exists) {
            $missing = $invoice->items->pluck('product_id')->filter()->diff($products->pluck('id'));
            if ($missing->isNotEmpty()) {
                $products = $products->concat(
                    Product::query()->whereIn('id', $missing)->get(['id', 'code', 'name', 'description', 'color', 'size', 'cost', 'price'])
                );
            }
        }

        return [
            'invoice' => $invoice,
            'customers' => Contact::query()->customers()->active()->orderBy('name')->get(['id', 'name', 'company']),
            'suppliers' => Contact::query()->suppliers()->active()->orderBy('name')->get(['id', 'name', 'company']),
            'productOptions' => $products->map(fn (Product $p) => [
                'id' => $p->id,
                'code' => $p->code,
                'name' => $p->name,
                'description' => trim(implode(' - ', array_filter([$p->description, $p->variant()]))),
                'cost' => $p->cost,
                'price' => $p->price,
            ])->values(),
        ];
    }

    private function validDate(mixed $value): ?string
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        return strtotime($value) !== false ? $value : null;
    }
}
