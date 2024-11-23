<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        $data = [
            'role' => Role::all(),
            "tittle" => "Role",
        ];
        return view('pages.settings.role.index', $data);
    }

    public function getData(Request $request)
    {
        $role = Role::whereNotIn('name', ['Developer'])->orderBy('id', 'asc')->get();
        return DataTables::of($role)->addIndexColumn()->addColumn('action', function ($role) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            if ($userauth->can('update-roles')) {
                $button .= ' <a href="' . route('role.edit', ['id' => $role->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $role->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                class="fas fa-pencil-alt"></i></a>';
            }
            if ($userauth->can('delete-roles')) {
                $button .= ' <button  class="btn btn-sm btn-danger  action" data-id=' . $role->id . ' data-type="delete" data-route="' . route('role.delete', ['id' => $role->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                            class="fas fa-trash-alt "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->rawColumns(['action'])->make(true);
    }

    public function create()
    {
        $data = [
            'permission' => Permission::all(),
            'tittle' => 'Role',
        ];
        return view('pages.settings.role.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required'], [
            'name.required' => 'Nama wajib diisi.',
        ]);
        $role = Role::create($request->only(['name']));
        $role->givePermissionTo($request->permissions);

        // Enhanced activity logging for creation
        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($role) // The entity being changed
            ->event('created') // Event of the action
            ->withProperties([
                'attributes' => [
                    'name' => $role->name,
                    'permissions' => $request->permissions,
                ],
            ])
            ->log("Created new role: {$role->name} with " . count($request->permissions) . " permission(s)"); // Log entry for role creation

        return redirect()->route('role')->with(['status' => 'Success!', 'message' => 'Berhasil Menambahkan Role!']);
    }

    public function show($id)
    {
        $data = [
            'permission' => Permission::all(),
            'tittle' => 'Role',
            'role' => Role::with('permissions')->findOrFail($id)
        ];
        return view('pages.settings.role.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required'], [
            'name.required' => 'Nama wajib diisi.',
        ]);

        $role = Role::find($id);
        $oldName = $role->name;
        $oldPermissions = $role->permissions->pluck('name')->toArray();

        $role->syncPermissions($request->permissions);
        $role->update($request->only(['name']));

        // Get added and removed permissions
        $newPermissions = collect($request->permissions);
        $addedPermissions = $newPermissions->diff($oldPermissions);
        $removedPermissions = collect($oldPermissions)->diff($newPermissions);

        // Log activity for role update
        $changes = [];
        if ($oldName !== $request->name) {
            $changes[] = "name changed from '{$oldName}' to '{$request->name}'";
        }
        if ($addedPermissions->count() > 0) {
            $changes[] = "added " . $addedPermissions->count() . " permission(s)";
        }
        if ($removedPermissions->count() > 0) {
            $changes[] = "removed " . $removedPermissions->count() . " permission(s)";
        }

        activity()
            ->causedBy(Auth::user()) // Logs who performed the action
            ->performedOn($role) // The entity being changed
            ->event('updated') // Event of the action
            ->withProperties([
                'changes' => $changes,
                'old' => [
                    'name' => $oldName,
                    'permissions' => $oldPermissions,
                ],
                'new' => [
                    'name' => $request->name,
                    'permissions' => $request->permissions,
                ]
            ])
            ->log("Updated role: {$role->name} with changes: " . implode(', ', $changes)); // Log entry for role update

        return redirect()->route('role')->with(['status' => 'Success!', 'message' => 'Berhasil Mengubah Role!']);
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $roleName = $role->name; // Store name before deletion

            $role->delete();

            // Log activity for role deletion
            activity()
                ->causedBy(Auth::user()) // Logs who performed the action
                ->performedOn($role) // The entity being changed
                ->event('deleted') // Event of the action
                ->withProperties([
                    'attributes' => [
                        'name' => $roleName,
                        'permissions' => $role->permissions->pluck('name')->toArray(),
                    ]
                ])
                ->log("Deleted role: {$roleName}"); // Log entry for role deletion

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Role Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Role Gagal dihapus!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
