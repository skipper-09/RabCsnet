<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SettingAplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingAplicationController extends Controller
{
    public function index($id)
    {
        $data = [
            'tittle' => 'Setting Aplikasi',
            'app' => SettingAplication::findOrFail($id),
        ];

        return view('pages.settings.settingaplication.index', $data);
    }


    public function update(Request $request, $id)
    {
        // dd($request->all());
        $app = SettingAplication::find($id);
        // Validate and save image
        $filename = $app->logo;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/logo/'), $filename);
            if ($app->logo !== 'logocsnet.webp' && file_exists(public_path('storage/logo/' . $app->logo))) {
                File::delete(public_path('storage/logo/' . $app->logo));
            }
        }

        $app->update([
            'name' => $request->name,
            'logo' => $filename,
            'description' => $request->description
        ]);

        return redirect()->back();
    }
}
