<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SettingAplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingAplicationController extends Controller
{
    public function index()
    {
        $data = [
            'tittle' => 'Setting Aplikasi',
            'setting' => SettingAplication::first(),
        ];

        return view('pages.settings.settingaplication.index', $data);
    }


    public function update(Request $request)
    {
       
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ppn' => 'required|numeric',
            'description' => 'nullable|string|max:225',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:5048', 
        ]);
    
        // get data
        $setting = SettingAplication::firstOrCreate([]);
    
        if ($request->hasFile('logo')) {
            // delete logo
            if ($setting->logo !== 'default.png' && Storage::exists('public/' . $setting->logo)) {
                Storage::delete('public/' . $setting->logo);
            }else{
                $validated['logo'] = $request->file('logo')->store('logo', 'public');
            }
        }
    
        // update
        $setting->update($validated);
        return redirect()->back()->with(['status' => 'Success!', 'message' => 'Berhasil Setting Aplication!']);
    }
}
