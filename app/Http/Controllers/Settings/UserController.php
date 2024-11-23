<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
        })->rawColumns(['action', 'role'])->make(true);
    }

    public function create()
    {
        $data = [
            'tittle' => 'User',
            'role' => Role::all()
        ];

        return view('pages.settings.user.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email|max:255',
            'password' => 'required|string|min:6|max:255|confirmed',
            'password_confirmation' => 'required|string|min:6|max:255',
            'is_block' => 'required|boolean',
            'role' => 'required'
        ]);

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_block' => $request->is_block,
        ]);

        $user->assignRole($request->role);

        // Improved activity logging
        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->event('created')
            ->withProperties([
                'attributes' => [
                    'name' => $user->name,
                    'role' => $request->role,
                    'email' => $user->email,
                    'username' => $user->username,
                    'is_block' => $user->is_block
                ],
                'old' => [],
                'created_at' => $user->created_at,
                'description' => "Created new user: {$user->name} with role {$request->role}"
            ])
            ->log('user_created');

        return redirect()->route('user')->with(['status' => 'Success!', 'message' => 'User created successfully!']);
    }

    public function show($id)
    {
        $user = User::find($id);

        $data = [
            'tittle' => 'User',
            'user' => $user,
            'role' => Role::all(),
            'userRoles' => $user->roles->pluck('name')->toArray(),
        ];

        return view('pages.settings.user.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldRole = $user->roles->first()->name ?? 'none';
        $oldAttributes = $user->getAttributes();

        $rules = [
            'username' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|unique:users,email,' . $user->id . '|max:255',
            'is_block' => 'nullable|boolean',
            'role' => 'nullable|exists:roles,name',
            'password' => 'nullable|string|min:6|confirmed'
        ];

        $request->validate($rules);

        $updateData = $request->only(['username', 'name', 'email', 'is_block']);

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        if ($request->has('role')) {
            $user->syncRoles($request->role);
        }

        // Improved activity logging for update
        $changes = [];
        $updatedAttributes = $user->getAttributes();

        foreach ($updateData as $key => $value) {
            if ($oldAttributes[$key] !== $updatedAttributes[$key]) {
                if ($key !== 'password') {
                    $changes[$key] = [
                        'old' => $oldAttributes[$key],
                        'new' => $updatedAttributes[$key]
                    ];
                }
            }
        }

        if ($request->has('role') && $oldRole !== $request->role) {
            $changes['role'] = [
                'old' => $oldRole,
                'new' => $request->role
            ];
        }

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->event('updated')
            ->withProperties([
                'changes' => $changes,
                'old_attributes' => $oldAttributes,
                'new_attributes' => $updatedAttributes,
                'created_at' => $user->updated_at,
                'description' => "Updated user: {$user->name}"
            ])
            ->log('user_updated');

        return redirect()->route('user')->with(['status' => 'Success!', 'message' => 'User updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $userData = $user->getAttributes();
            $userName = $user->name;

            $user->delete();

            // Improved activity logging for deletion
            activity()
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->event('deleted')
                ->withProperties([
                    'deleted_attributes' => $userData,
                    'created_at' => now(),
                    'description' => "Deleted user: {$userName}"
                ])
                ->log('user_deleted');

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
