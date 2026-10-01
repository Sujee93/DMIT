<?php

namespace App\Http\Requests;

use App\Enums\ContactType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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
        $money = ['numeric', 'min:0', 'max:999999999.99', 'decimal:0,2'];

        return [
            'customer_id' => ['required', 'integer', Rule::exists('contacts', 'id')->where('type', ContactType::Customer->value)],
            'supplier_id' => ['nullable', 'integer', Rule::exists('contacts', 'id')->where('type', ContactType::Supplier->value)],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'discount' => ['nullable', ...$money],
            'notes' => ['nullable', 'string', 'max:2000'],

            'items' => ['required', 'array', 'min:1', 'max:200'],
            'items.*.product_id' => ['required', 'integer', 'distinct', Rule::exists('products', 'id')],
            'items.*.description' => ['nullable', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'items.*.unit_price' => ['required', ...$money],
            'items.*.unit_cost' => ['nullable', ...$money],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one product to the invoice.',
            'items.*.product_id.distinct' => 'Each product can only be added once - increase the quantity instead.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_id' => 'customer',
            'supplier_id' => 'supplier',
            'items.*.quantity' => 'quantity',
            'items.*.unit_price' => 'unit price',
            'items.*.unit_cost' => 'unit cost',
        ];
    }
}
