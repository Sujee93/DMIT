<?php

namespace App\Services;

use App\Models\BusinessSetting;

/**
 * Issues sequential invoice numbers such as "INV-00042".
 *
 * Must be called inside a database transaction: the settings row is locked
 * so two invoices saved at the same moment can never share a number.
 */
class InvoiceNumberGenerator
{
    public function next(): string
    {
        $settings = BusinessSetting::query()->lockForUpdate()->first()
            ?? BusinessSetting::query()->create(BusinessSetting::defaults());

        $number = max(1, (int) $settings->invoice_next_number);

        $settings->forceFill(['invoice_next_number' => $number + 1])->save();

        return $settings->invoice_prefix.str_pad((string) $number, 5, '0', STR_PAD_LEFT);
    }
}
