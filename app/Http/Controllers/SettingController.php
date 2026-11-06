<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AuditLogger;
use App\Support\SchoolSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        Gate::authorize('manage', Setting::class);

        return view('settings.edit', [
            'settings' => Setting::allCached(),
            'radius' => SchoolSetting::radius(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('manage', Setting::class);

        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:150'],
            'school_npsn' => ['nullable', 'string', 'max:30'],
            'school_address' => ['nullable', 'string', 'max:255'],
            'school_phone' => ['nullable', 'string', 'max:30'],
            'school_email' => ['nullable', 'email', 'max:150'],
            'school_website' => ['nullable', 'string', 'max:150'],
            'school_headmaster' => ['nullable', 'string', 'max:150'],
            'school_description' => ['nullable', 'string', 'max:2000'],
            'school_vision' => ['nullable', 'string', 'max:1000'],
            'school_mission' => ['nullable', 'string', 'max:2000'],
            'school_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'school_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'geofence_radius' => ['required', 'integer', 'between:10,1000'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'late_after' => ['required', 'date_format:H:i'],
            'check_out_time' => ['required', 'date_format:H:i'],
            'school_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('school_logo')) {
            $oldLogo = Setting::get(SchoolSetting::LOGO);

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $data['school_logo'] = $request->file('school_logo')->store('school', 'public');
        } else {
            unset($data['school_logo']);
        }

        Setting::putMany($data, 'school');

        AuditLogger::log('settings.updated', null, 'Memperbarui pengaturan sekolah & koordinat.');

        return back()->with('success', 'Pengaturan sekolah berhasil disimpan.');
    }
}
