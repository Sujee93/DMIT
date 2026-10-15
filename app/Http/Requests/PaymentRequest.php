<?php

namespace App\Http\Requests;

use App\Enums\ContactType;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * Shared by customer receipts (against an invoice) and supplier payments.
 */
class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $forSupplier = $this->routeIs('supplier-payments.store');
        $methods = $forSupplier ? PaymentMethod::forSuppliers() : PaymentMethod::forCustomers();
        $usesCustomerCheques = $forSupplier && $this->input('method') === PaymentMethod::CustomerCheque->value;
        $cheque = PaymentMethod::Cheque->value;

        $rules = [
            'payment_date' => ['required', 'date', 'before_or_equal:'.now()->addDay()->toDateString()],
            // With customer cheques the amount is the total of the selected cheques.
            'amount' => $usesCustomerCheques
                ? ['nullable']
                : ['required', 'numeric', 'min:0.01', 'max:999999999.99', 'decimal:0,2'],
            'method' => ['required', new Enum(PaymentMethod::class), Rule::in(array_map(fn ($m) => $m->value, $methods))],
            'reference' => ['nullable', 'string', 'max:100', "required_if:method,{$cheque}"],
            'bank' => ['nullable', 'string', 'max:100'],
            // Customers' cheque dates are needed to know when a cheque expires.
            'cheque_date' => ['nullable', 'date', $forSupplier ? 'nullable' : "required_if:method,{$cheque}"],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($forSupplier) {
            $rules['supplier_id'] = ['required', 'integer', Rule::exists('contacts', 'id')->where('type', ContactType::Supplier->value)];
            $rules['cheque_ids'] = $usesCustomerCheques
                ? ['required', 'array', 'min:1', 'max:50']
                : ['prohibited'];
            $rules['cheque_ids.*'] = ['integer', 'distinct', Rule::exists('cheques', 'id')];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reference.required_if' => 'Enter the cheque number for cheque payments.',
            'cheque_date.required_if' => 'Enter the date written on the cheque.',
            'cheque_ids.required' => 'Select at least one customer cheque.',
            'cheque_ids.prohibited' => 'Cheques can only be selected with the "Customer cheque" method.',
            'method.in' => 'This payment method is not allowed here.',
            'payment_date.before_or_equal' => 'The payment date cannot be in the future.',
        ];
    }
}
