<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessSettingRequest;
use App\Models\BusinessSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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

        $disk = Storage::disk('public');

        if ($request->boolean('remove_logo') && $settings->logo_path) {
            $disk->delete($settings->logo_path);
            $settings->logo_path = null;
        }

        if ($request->hasFile('logo')) {
            // Stored under a random name; the original client filename is never used.
            $path = $request->file('logo')->store('logos', 'public');

            if ($settings->logo_path) {
                $disk->delete($settings->logo_path);
            }
            $settings->logo_path = $path;
        }

        $settings->save();

        return redirect()->route('settings.edit')->with('success', 'Business settings updated.');
    }
}
