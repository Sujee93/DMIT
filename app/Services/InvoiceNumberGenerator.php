<?php

namespace App\Services;

use App\Models\BusinessSetting;

/**
 * Issues sequential document numbers such as "INV-00042" and "GRN-00007".
 *
 * Must be called inside a database transaction: the settings row is locked
 * so two documents saved at the same moment can never share a number.
 */
class InvoiceNumberGenerator
{
    public function next(): string
    {
        $settings = $this->lockedSettings();

        return $settings->invoice_prefix.$this->take($settings, 'invoice_next_number');
    }

    public function nextGrn(): string
    {
        return config('distribution.grn_prefix', 'GRN-').$this->take($this->lockedSettings(), 'grn_next_number');
    }

    private function lockedSettings(): BusinessSetting
    {
        return BusinessSetting::query()->lockForUpdate()->first()
            ?? BusinessSetting::query()->create(BusinessSetting::defaults());
    }

    private function take(BusinessSetting $settings, string $column): string
    {
        $number = max(1, (int) $settings->{$column});
        $settings->forceFill([$column => $number + 1])->save();

        return str_pad((string) $number, 5, '0', STR_PAD_LEFT);
    }
}
