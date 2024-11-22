<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

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
            $button = '';
            $button .= ' <a href="' . route('user.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            class="fas fa-pencil-alt"></i></a>';
            $button .= ' <button  class="btn btn-sm btn-danger  action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('user.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                            class="fas fa-trash-alt "></i></button>';
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->addColumn('role', function ($data) {
            return $data->roles[0]->name;
        })->rawColumns(['action', 'role'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'tittle' => 'User',
            // 'role'=>Role::where('name', '!=', 'Developer')->get(),
            'role' => Role::all()
        ];

        return view('pages.settings.user.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        // Validate the request data
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email|max:255',
            'password' => 'required|string|min:6|max:255|confirmed',
            'password_confirmation' => 'required|string|min:6|max:255',
            'is_block' => 'required|boolean',
            'role' => 'required'
        ]);

        // Create the user
        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_block' => $request->is_block,
        ])->assignRole($request->role);

        // Log the activity of creating a user (without using `auth()->user()`)
        activity()
            ->performedOn($user) // Log which model is being affected (User in this case)
            ->withProperties(['attributes' => $user->toArray()])
            ->log('User created: ' . $user->name); // Custom message for activity

        return redirect()->route('user');
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);

        $data = [
            'tittle' => 'User',
            'user' => $user,
            'role' => Role::all(),
            'userRoles' => $user->roles->pluck('name')->toArray(), // Get current user roles
        ];

        return view('pages.settings.user.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Temukan user berdasarkan ID
        $user = User::findOrFail($id);

        // Aturan validasi
        $rules = [
            'username' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|unique:users,email,' . $user->id . '|max:255',
            'is_block' => 'nullable|boolean',
            'role' => 'nullable|exists:roles,name',
            'password' => 'nullable|string|min:6|confirmed'
        ];

        // Validasi request data
        $request->validate($rules);

        // Persiapkan data untuk update
        $updateData = $request->only(['username', 'name', 'email', 'is_block']);

        // Update password jika diberikan
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Lakukan pembaruan data pada user
        $user->update($updateData);

        // Sync role jika ada
        if ($request->has('role')) {
            $user->syncRoles($request->role);
        }

        // Log the activity of updating the user
        activity()
            ->performedOn($user) // Log the affected user
            ->withProperties(['attributes' => $user->toArray()])
            ->log('User updated: ' . $user->name); // Custom message for activity

        // Redirect dengan pesan sukses
        return redirect()->route('user')->with('success', 'User updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $data = User::findOrFail($id);
            $data->delete();

            //return response
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
