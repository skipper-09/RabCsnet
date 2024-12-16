<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            "tittle" => "User",
        ];

        return view("pages.settings.user.index", $data);
    }

    public function getData(Request $request)
    {
        $user = User::orderByDesc('id')->get();

        return DataTables::of($user)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            if ($userauth->can('update-users')) {
                $button .= ' <a href="' . route('user.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                class="fas fa-pencil-alt"></i></a>';
            }
            if ($userauth->can('delete-users')) {
                $button .= ' <button  class="btn btn-sm btn-danger  action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('user.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                class="fas fa-trash-alt "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->addColumn('role', function ($data) {
            return $data->roles[0]->name;
        })->editColumn('picture', function ($data) {
            return $data->picture == null ? '<img src="' . asset('assets/images/avataaars.png') . '" alt="Profile Image" class="rounded-circle header-profile-user">' :'<img src="' . asset("storage/images/user/$data->picture") . '" alt="Profile Image" class="rounded-circle header-profile-user">' ;
        })->rawColumns(['action', 'role','picture'])->make(true);
    }

    public function create()
    {
        $data = [
            'tittle' => 'User',
            'role' => Role::whereNotIn('name', ['Developer'])->get(),
        ];

        return view('pages.settings.user.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email|max:255',
            'password' => 'required|string|min:6|max:255|confirmed',
            'password_confirmation' => 'required|string|min:6|max:255',
            'is_block' => 'required|boolean',
            'role' => 'required'
        ], [
            'picture.image' => 'File harus berupa gambar.',
            'picture.mimes' => 'Format gambar tidak valid.',
            'picture.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.max' => 'Username maksimal 255 karakter.',
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Email harus berupa teks.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.max' => 'Password maksimal 255 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'password_confirmation.string' => 'Konfirmasi password harus berupa teks.',
            'password_confirmation.min' => 'Konfirmasi password minimal 6 karakter.',
            'password_confirmation.max' => 'Konfirmasi password maksimal 255 karakter.',
            'is_block.required' => 'Status wajib diisi.',
            'role.required' => 'Role wajib diisi.',
        ]);

        $filename = '';
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = 'user_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/images/user/'), $filename);
        }

        $user = User::create([
            'picture' => $filename,
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_block' => $request->is_block,
        ]);

        $user->assignRole($request->role);

        // Log activity for User creation
        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($user) // The entity being changed
            ->event('created') // Event of the action
            ->withProperties([
                'attributes' => $user->toArray() // The data that was created
            ])
            ->log('User dibuat dengan nama ' . $user->name);

        return redirect()->route('user')->with(['status' => 'Success!', 'message' => 'User created successfully!']);
    }

    public function show($id)
    {
        // Find the user by the given ID
        $user = User::find($id);

        // Get the currently authenticated user
        $currentUser = Auth::user();

        // Check the role of the current user, get the first role if exists
        $userRole = $currentUser->roles->first() ? $currentUser->roles->first()->name : null;

        // If the current user's role is 'Developer', allow viewing all roles
        if ($userRole === 'Developer') {
            $roles = Role::all();  // Developer can see all roles
        } else {
            $roles = Role::whereNotIn('name', ['Developer'])->get();  // Others can't see 'Developer' role
        }

        // Prepare the data to pass to the view
        $data = [
            'tittle' => 'User',
            'user' => $user,
            'role' => $roles,  // Roles depend on the current user's role
            'userRoles' => $user->roles->pluck('name')->toArray(),  // User's roles
        ];

        // Return the view with the data
        return view('pages.settings.user.edit', $data);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldUser = $user->toArray();

        $rules = [
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|unique:users,email,' . $user->id . '|max:255',
            'is_block' => 'nullable|boolean',
            'role' => 'nullable|exists:roles,name',
            'password' => 'nullable|string|min:6|confirmed'
        ];

        $request->validate($rules);

        $updateData = $request->only(['username', 'name', 'email', 'is_block']);

        $filename = $user->picture;

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = 'user_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/images/user/'), $filename);
            $updateData['picture'] = $filename;

            if ($user->picture !== 'default.png' && file_exists(public_path('storage/images/user/' . $user->picture))) {
                File::delete(public_path('storage/images/user/' . $user->picture));
            }
        }

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        if ($request->has('role')) {
            $user->syncRoles($request->role);
        }

        // Log activity for user update
        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($user) // The entity being changed
            ->event('updated') // Event of the action
            ->withProperties([
                'old' => $oldUser, // The data before update
                'attributes' => $user->toArray() // The updated data
            ])
            ->log('User di update dengan nama ' . $user->name);

        return redirect()->route('user')->with(['status' => 'Success!', 'message' => 'User updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $userData = $user->toArray(); // Capture the data before deletion

            $user->delete();

            activity()
                ->causedBy(Auth::user()) // Logs who performed the action
                ->performedOn($user) // The entity being changed
                ->event('deleted') // Event of the action
                ->withProperties([
                    'attributes' => $userData // The data before deletion
                ])
                ->log('User dihapus dengan nama ' . $user->name);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data !',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
