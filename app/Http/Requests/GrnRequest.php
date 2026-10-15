<?php

namespace App\Http\Requests;

use App\Enums\ContactType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GrnRequest extends FormRequest
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
            'supplier_id' => ['required', 'integer', Rule::exists('contacts', 'id')->where('type', ContactType::Supplier->value)],
            'grn_date' => ['required', 'date', 'before_or_equal:'.now()->addDay()->toDateString()],
            'supplier_ref' => ['nullable', 'string', 'max:100'],
            'discount' => ['nullable', ...$money],
            'notes' => ['nullable', 'string', 'max:2000'],

            'items' => ['required', 'array', 'min:1', 'max:300'],
            'items.*.product_id' => ['required', 'integer', 'distinct', Rule::exists('products', 'id')],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'items.*.unit_cost' => ['required', ...$money],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one product received.',
            'items.*.product_id.distinct' => 'Each product can only be added once - increase the quantity instead.',
            'grn_date.before_or_equal' => 'The GRN date cannot be in the future.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'supplier_id' => 'supplier',
            'supplier_ref' => 'supplier bill no.',
            'items.*.quantity' => 'quantity',
            'items.*.unit_cost' => 'unit cost',
        ];
    }
}
