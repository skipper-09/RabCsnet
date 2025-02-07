<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index($id)
    {

        $data = [
            'tittle' => 'Profile',
            'profile' => User::find($id),
        ];

        return view('pages.profile.index', $data);
    }


    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);

            $request->validate([
                'picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'name' => 'nullable|string|max:255',
                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('users', 'email')->ignore($user->id), // Abaikan pengguna saat ini berdasarkan ID
                ],
                'username' => [
                    'nullable',
                    'string',
                    Rule::unique('users', 'username')->ignore($user->id), // Abaikan pengguna saat ini berdasarkan ID
                ],
                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                ],
            ], [
                'picture.image' => 'File harus berupa gambar.',
                'picture.mimes' => 'Format gambar tidak valid.',
                'picture.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan.',
                'username.unique' => 'Username sudah digunakan.',
                'password.min' => 'Password harus memiliki minimal :min karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
                'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol (@$!%*?&).',
            ]);

            $filename = $user->picture;

            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $filename = 'user_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/user/'), $filename);

                if ($user->picture !== 'default.png' && file_exists(public_path('storage/images/user/' . $user->picture))) {
                    File::delete(public_path('storage/images/user/' . $user->picture));
                }
            }

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $user->update([
                'picture' => $filename,
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
            ]);

            return redirect()->back()->with(['status' => 'Success', 'message' => 'Berhasil Update Profile']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => 'Error', 'message' => 'Gagal Update Profile']);
        }
    }
}
