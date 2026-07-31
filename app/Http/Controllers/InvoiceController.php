<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Support\NumberToWords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    private const CUSTOMER_KEYS = ['singer', 'arpico'];

    public function index(Request $request): View
    {
        $invoices = Invoice::query()
            ->with('customer')
            ->when($request->string('type')->toString(), fn ($query, $type) => $query->where('invoice_type', $type))
            ->orderByDesc('date_of_invoice')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('invoices.index', [
            'invoices' => $invoices,
            'type' => $request->string('type')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('invoices.create', [
            'fixedCustomers' => Customer::where('is_fixed', true)->get()->keyBy('key'),
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateInvoice($request);

        $invoice = DB::transaction(function () use ($data) {
            return $this->saveInvoice(new Invoice, $data);
        });

        return redirect()->route('invoices.print', $invoice)->with('status', 'Invoice created.');
    }

    public function show(Invoice $invoice): RedirectResponse
    {
        return redirect()->route('invoices.print', $invoice);
    }

    public function edit(Invoice $invoice): View
    {
        $invoice->load('items');

        return view('invoices.edit', [
            'invoice' => $invoice,
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $this->validateInvoice($request, $invoice);

        DB::transaction(function () use ($invoice, $data) {
            $this->saveInvoice($invoice, $data);
        });

        return redirect()->route('invoices.print', $invoice)->with('status', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('status', 'Invoice deleted.');
    }

    public function print(Invoice $invoice): View
    {
        $invoice->load(['items.product', 'customer', 'preparedBy']);

        $view = $invoice->isTaxInvoice() ? 'invoices.print.tax' : 'invoices.print.general';

        return view($view, ['invoice' => $invoice]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateInvoice(Request $request, ?Invoice $existing = null): array
    {
        $rules = [
            'date_of_invoice' => ['required', 'date'],
            'mode_of_payment' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.reference' => ['nullable', 'string', 'max:50'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];

        if ($existing) {
            // Type is fixed after creation — only the fields relevant to that type are required.
            $rules += $existing->isTaxInvoice() ? $this->taxRules() : $this->generalRules();
        } else {
            $rules['invoice_choice'] = ['required', Rule::in(['tax:singer', 'tax:arpico', 'general'])];
            $rules += $this->taxRules(required: false) + $this->generalRules(required: false);
        }

        $validated = $request->validate($rules);
        $validated['invoice_choice'] = $request->string('invoice_choice')->toString();

        return $validated;
    }

    private function taxRules(bool $required = true): array
    {
        $presence = $required ? 'required' : 'nullable';

        return [
            'date_of_supply' => [$presence, 'date'],
            'place_of_supply' => ['nullable', 'string', 'max:255'],
            'vat_rate' => [$presence, 'numeric', 'min:0', 'max:100'],
        ];
    }

    private function generalRules(bool $required = true): array
    {
        $presence = $required ? 'required' : 'nullable';

        return [
            'customer_name' => [$presence, 'string', 'max:255'],
            'customer_address' => ['nullable', 'string', 'max:1000'],
            'customer_telephone' => ['nullable', 'string', 'max:50'],
            'vehicle_no' => ['nullable', 'string', 'max:50'],
            'sup_no' => ['nullable', 'string', 'max:50'],
            'advance' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    private function saveInvoice(Invoice $invoice, array $data): Invoice
    {
        $isNew = ! $invoice->exists;

        if ($isNew) {
            [$invoiceType, $customerKey] = str_contains($data['invoice_choice'], ':')
                ? explode(':', $data['invoice_choice'], 2)
                : [$data['invoice_choice'], null];

            $invoice->invoice_type = $invoiceType;
        }

        $isTax = $invoice->invoice_type === 'tax';

        if ($isNew) {
            $invoice->invoice_number = Invoice::nextInvoiceNumber($invoice->invoice_type);

            if ($isTax) {
                if (! in_array($customerKey, self::CUSTOMER_KEYS, true)) {
                    throw ValidationException::withMessages(['invoice_choice' => 'Invalid customer selection for a Tax Invoice.']);
                }

                $customer = Customer::where('key', $customerKey)->firstOrFail();
                $invoice->customer_id = $customer->id;
                $invoice->customer_name = $customer->name;
                $invoice->customer_address = $customer->address;
                $invoice->customer_tin = $customer->tin_number;
                $invoice->customer_telephone = $customer->telephone;
                $invoice->reference_number = Invoice::referenceNumberFor($customerKey, $invoice->invoice_number);
            }
        }

        $invoice->date_of_invoice = $data['date_of_invoice'];
        $invoice->mode_of_payment = $data['mode_of_payment'] ?? null;
        $invoice->prepared_by = $invoice->prepared_by ?? request()->user()->id;
        $invoice->status = 'finalized';

        if ($isTax) {
            $invoice->date_of_supply = $data['date_of_supply'] ?? $data['date_of_invoice'];
            $invoice->place_of_supply = $data['place_of_supply'] ?? null;
            $invoice->vat_rate = $data['vat_rate'] ?? 18;
        } else {
            $invoice->customer_name = $data['customer_name'];
            $invoice->customer_address = $data['customer_address'] ?? null;
            $invoice->customer_telephone = $data['customer_telephone'] ?? null;
            $invoice->vehicle_no = $data['vehicle_no'] ?? null;
            $invoice->sup_no = $data['sup_no'] ?? null;
            $invoice->advance = $data['advance'] ?? 0;
        }

        $subtotal = 0;
        $itemRows = [];

        foreach ($data['items'] as $index => $item) {
            $amount = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
            $subtotal += $amount;

            $itemRows[] = [
                'product_id' => $item['product_id'] ?? null,
                'reference' => $item['reference'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'amount' => $amount,
                'sort_order' => $index,
            ];
        }

        $invoice->subtotal = $subtotal;

        if ($isTax) {
            $invoice->vat_amount = round($subtotal * ((float) $invoice->vat_rate) / 100, 2);
            $invoice->total_amount = $subtotal + $invoice->vat_amount;
            $invoice->advance = null;
            $invoice->balance = null;
        } else {
            $invoice->vat_rate = null;
            $invoice->vat_amount = null;
            $invoice->total_amount = $subtotal;
            $invoice->balance = $subtotal - (float) $invoice->advance;
        }

        $invoice->amount_in_words = NumberToWords::rupees((float) $invoice->total_amount);

        $invoice->save();
        $invoice->items()->delete();
        $invoice->items()->createMany($itemRows);

        return $invoice;
    }
}
