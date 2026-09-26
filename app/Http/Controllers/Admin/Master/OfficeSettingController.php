<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\OfficeSetting;
use Illuminate\Http\Request;

class OfficeSettingController extends Controller
{
    /**
     * Display the office setting page with interactive map picker.
     */
    public function index()
    {
        $office = OfficeSetting::getActiveSetting();

        return view('admin.master.lokasi-kantor', compact('office'));
    }

    /**
     * Update the office attendance location and radius.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:5|max:10000',
        ], [
            'name.required' => 'Nama lokasi kantor wajib diisi.',
            'latitude.required' => 'Latitude koordinat wajib ditentukan.',
            'longitude.required' => 'Longitude koordinat wajib ditentukan.',
            'radius_meters.required' => 'Radius absensi wajib ditentukan.',
            'radius_meters.min' => 'Radius minimal adalah 5 meter.',
        ]);

        $office = OfficeSetting::getActiveSetting();
        $office->update([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meters' => $request->radius_meters,
        ]);

        return back()->with('success', 'Titik lokasi kantor dan radius presensi berhasil diperbarui!');
    }
}
