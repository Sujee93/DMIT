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
        $rules = [
            'payment_date' => ['required', 'date', 'before_or_equal:'.now()->addDay()->toDateString()],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99', 'decimal:0,2'],
            'method' => ['required', new Enum(PaymentMethod::class)],
            'reference' => ['nullable', 'string', 'max:100', 'required_if:method,'.PaymentMethod::Cheque->value],
            'bank' => ['nullable', 'string', 'max:100'],
            'cheque_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($this->routeIs('supplier-payments.store')) {
            $rules['supplier_id'] = ['required', 'integer', Rule::exists('contacts', 'id')->where('type', ContactType::Supplier->value)];
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
            'payment_date.before_or_equal' => 'The payment date cannot be in the future.',
        ];
    }
}
