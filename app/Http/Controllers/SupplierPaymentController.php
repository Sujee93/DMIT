<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Http\Requests\PaymentRequest;
use App\Models\Cheque;
use App\Models\Contact;
use App\Models\SupplierPayment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Payments made to suppliers.
 */
class SupplierPaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function index(Request $request): View
    {
        $supplierId = $request->integer('supplier_id') ?: null;

        $payments = SupplierPayment::query()
            ->with(['supplier:id,name,company', 'creator:id,name', 'cheques:id,supplier_payment_id,cheque_no,bank,cheque_date,amount,customer_id', 'cheques.customer:id,name,company'])
            ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId))
            ->latest('payment_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('supplier-payments.index', [
            'payments' => $payments,
            'suppliers' => Contact::query()->suppliers()->orderBy('name')->get(['id', 'name', 'company']),
            'supplierId' => $supplierId,
        ]);
    }

    public function create(Request $request): View
    {
        $supplier = $request->integer('supplier_id')
            ? Contact::query()->suppliers()->find($request->integer('supplier_id'))
            : null;

        return view('supplier-payments.create', [
            'suppliers' => Contact::query()->suppliers()->active()->orderBy('name')->get(['id', 'name', 'company']),
            'selected' => $supplier,
            'balance' => $supplier?->balance(),
            'paymentMethods' => PaymentMethod::supplierOptions(),
            'defaultMethod' => array_key_exists((string) $request->query('method'), PaymentMethod::supplierOptions())
                ? (string) $request->query('method')
                : PaymentMethod::Cash->value,
            // Customer cheques that are still with us and still valid.
            'availableCheques' => Cheque::query()
                ->available()
                ->with('customer:id,name,company')
                ->orderBy('cheque_date')
                ->get(),
        ]);
    }

    public function store(PaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $supplier = Contact::query()->suppliers()->findOrFail($data['supplier_id']);

        $payment = $this->payments->paySupplier($supplier, $data, $request->user());

        return redirect()
            ->route('contacts.show', $supplier)
            ->with('success', 'Payment of '.money($payment->amount)." to {$supplier->name} recorded.");
    }

    public function destroy(SupplierPayment $supplierPayment): RedirectResponse
    {
        $this->authorize('admin');

        $this->payments->removeSupplierPayment($supplierPayment);

        return back()->with('success', 'Supplier payment removed. Any customer cheques it used are back in hand.');
    }
}
