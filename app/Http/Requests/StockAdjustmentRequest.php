<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'direction' => ['required', Rule::in(['add', 'remove'])],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'movement_date' => ['required', 'date', 'before_or_equal:'.now()->addDay()->toDateString()],
            'note' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['product_id' => 'product', 'movement_date' => 'date', 'note' => 'reason'];
    }
}
