<?php

namespace App\Http\Controllers;

use App\Enums\ContactType;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Customers and suppliers are managed together on one page.
 */
class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $type = ContactType::tryFrom((string) $request->query('type'));
        $search = $request->string('q')->trim()->limit(100, '')->toString();

        $contacts = Contact::query()
            ->when($type, fn ($q) => $q->where('type', $type))
            ->search($search)
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $counts = Contact::query()
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('contacts.index', compact('contacts', 'type', 'search', 'counts'));
    }

    public function create(Request $request): View
    {
        $type = ContactType::tryFrom((string) $request->query('type')) ?? ContactType::Customer;

        return view('contacts.create', ['contact' => new Contact(['type' => $type, 'is_active' => true])]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $contact = Contact::create($request->validated());

        return redirect()
            ->route('contacts.show', $contact)
            ->with('success', "{$contact->type->label()} {$contact->name} created.");
    }

    public function show(Contact $contact): View
    {
        if ($contact->isCustomer()) {
            $invoices = $contact->salesInvoices()->latest('invoice_date')->latest('id')->paginate(15);
            $payments = $contact->customerPayments()->with('invoice:id,invoice_no')->latest('payment_date')->limit(15)->get();
        } else {
            $invoices = $contact->purchaseInvoices()->with('customer:id,name,company')->latest('invoice_date')->latest('id')->paginate(15);
            $payments = $contact->supplierPayments()->latest('payment_date')->latest('id')->limit(15)->get();
        }

        return view('contacts.show', [
            'contact' => $contact,
            'invoices' => $invoices,
            'payments' => $payments,
            'balance' => $contact->balance(),
        ]);
    }

    public function edit(Contact $contact): View
    {
        return view('contacts.edit', compact('contact'));
    }

    public function update(ContactRequest $request, Contact $contact): RedirectResponse
    {
        $data = $request->validated();

        // Changing type would orphan invoices/payments linked in the other role.
        if ($data['type'] !== $contact->type->value && $this->hasTransactions($contact)) {
            return back()->withInput()->withErrors(['type' => 'This contact already has transactions, so its type cannot be changed.']);
        }

        $contact->update($data);

        return redirect()->route('contacts.show', $contact)->with('success', 'Contact updated.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $this->authorize('admin');

        if ($this->hasTransactions($contact)) {
            return back()->with('error', 'This contact has invoices or payments. Mark it inactive instead of deleting it.');
        }

        $type = $contact->type;
        $contact->delete();

        return redirect()->route('contacts.index', ['type' => $type->value])->with('success', 'Contact deleted.');
    }

    private function hasTransactions(Contact $contact): bool
    {
        return $contact->salesInvoices()->exists()
            || $contact->purchaseInvoices()->exists()
            || $contact->customerPayments()->exists()
            || $contact->supplierPayments()->exists();
    }
}
