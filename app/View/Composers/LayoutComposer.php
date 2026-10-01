<?php

namespace App\View\Composers;

use App\Models\BusinessSetting;
use Illuminate\View\View;

/**
 * Shares the business profile with layouts and printable views.
 */
class LayoutComposer
{
    public function compose(View $view): void
    {
        $view->with('business', BusinessSetting::current());
    }
}
