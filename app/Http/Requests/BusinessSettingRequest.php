<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessSettingRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email:filter', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'registration_no' => ['nullable', 'string', 'max:100'],
            'tax_no' => ['nullable', 'string', 'max:100'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'invoice_prefix' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\-\/_]*$/'],
            'default_due_days' => ['required', 'integer', 'min:0', 'max:365'],
            'bank_details' => ['nullable', 'string', 'max:1000'],
            'invoice_terms' => ['nullable', 'string', 'max:2000'],
            'invoice_footer' => ['nullable', 'string', 'max:500'],
            // SVG is deliberately excluded: it can carry scripts.
            'logo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'remove_logo' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'invoice_prefix.regex' => 'The invoice prefix may only contain letters, numbers, dashes, slashes and underscores.',
        ];
    }
}
