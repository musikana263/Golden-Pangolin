<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\SiteSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', [
            'whatsappNumber' => site_whatsapp_number(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            // Digits only, 8-15 characters (E.164 max minus the +).
            'whatsapp_number' => ['required', 'string', 'regex:/^[0-9]{8,15}$/'],
        ], [
            'whatsapp_number.regex' => 'Enter the number in digits only (no +, no spaces). Include the country code, e.g. 26371527381.',
        ]);

        SiteSetting::put('whatsapp_number', $data['whatsapp_number']);
        session()->flash('success', 'Settings saved.');

        return redirect()->route('settings.edit');
    }
}
