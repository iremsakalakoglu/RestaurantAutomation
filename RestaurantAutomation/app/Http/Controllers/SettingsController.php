<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function saveGeneral(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);
        $settings = Setting::first();
        if (!$settings) {
            $settings = new Setting();
        }
        $settings->name = $validated['name'];
        $settings->address = $validated['address'];
        $settings->phone = $validated['phone'];
        $settings->email = $validated['email'];
        $settings->save();
        return response()->json(['success' => true]);
    }

    public function getGeneralSettings()
    {
        $settings = Setting::first();
        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    public function showSettingsPage()
    {
        $settings = \App\Models\Setting::first();
        return view('Dashboard.settings', ['settings' => $settings]);
    }
}
