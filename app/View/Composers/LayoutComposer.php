<?php

namespace App\View\Composers;

use App\Models\BusinessSetting;
use Illuminate\View\View;
use Throwable;

/**
 * Shares the business profile with every view.
 */
class LayoutComposer
{
    public function compose(View $view): void
    {
        $view->with('business', $this->business());
    }

    private function business(): BusinessSetting
    {
        try {
            return BusinessSetting::current();
        } catch (Throwable $e) {
            // Never let a database/cache problem break rendering (including error pages).
            report($e);
            $fallback = new BusinessSetting(BusinessSetting::defaults());
            app()->instance(BusinessSetting::class.'@current', $fallback);

            return $fallback;
        }
    }
}
