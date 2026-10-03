<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessSettingRequest;
use App\Models\BusinessSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class BusinessSettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', ['settings' => BusinessSetting::current()]);
    }

    public function update(BusinessSettingRequest $request): RedirectResponse
    {
        $settings = BusinessSetting::query()->firstOrNew([]);
        $settings->fill($request->safe()->except(['logo', 'remove_logo']));

        // The storage disk is only touched when a logo is being changed, so saving the
        // name/address can never fail because of folder permissions.
        if ($request->boolean('remove_logo') && $settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $settings->logo_path = null;
        }

        if ($request->hasFile('logo')) {
            try {
                // Stored under a random name; the original client filename is never used.
                $path = $request->file('logo')->store('logos', 'public');
            } catch (Throwable $e) {
                report($e);
                $path = false;
            }

            if ($path === false) {
                return back()->withInput()->withErrors([
                    'logo' => 'The logo could not be saved on the server. Check that storage/app/public is writable (run: php artisan app:check).',
                ]);
            }

            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $settings->logo_path = $path;
        }

        $settings->save();

        return redirect()->route('settings.edit')->with('success', 'Business settings updated.');
    }
}
