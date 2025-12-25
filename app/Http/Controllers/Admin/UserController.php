<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        // Read-only access
        $this->middleware('permission:users.view')->only([
            'index',
        ]);

        // Create users
        $this->middleware('permission:users.create')->only([
            'create',
            'store',
        ]);

        // Edit / update users
        $this->middleware('permission:users.update')->only([
            'edit',
            'update',
        ]);

        // Delete users
        $this->middleware('permission:users.delete')->only([
            'destroy',
        ]);
    }


    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('title', 'like', "%{$q}%");
            })
            ->with('roles')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()?->can('roles.manage'), 403);

        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(UpsertUserRequest $request)
    {
        abort_unless($request->user()?->can('roles.manage'), 403);

        $data = $request->validated();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'title'    => $data['title'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        $user->syncRoles($data['roles'] ?? []);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created.');
    }

    public function edit(Request $request, User $user)
    {
        abort_unless($request->user()?->can('users.update'), 403);

        $roles = Role::orderBy('name')->get();
        $userRoleNames = $user->roles->pluck('name')->toArray();
        $canManageRoles = $request->user()?->can('roles.manage');

        return view('admin.users.edit', compact(
            'user',
            'roles',
            'userRoleNames',
            'canManageRoles'
        ));
    }

    public function update(UpsertUserRequest $request, User $user)
    {
        abort_unless($request->user()?->can('users.update'), 403);

        $data = $request->validated();

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            'title' => $data['title'] ?? null,
        ]);

        if (!empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        if ($request->user()?->can('roles.manage')) {
            $user->syncRoles($data['roles'] ?? []);
        }

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User updated.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($request->user()?->id === $user->id, 403, 'You cannot delete yourself.');

        if ($user->hasRole('Admin') && !$request->user()?->hasRole('Admin')) {
            abort(403, 'Only Admin may delete another Admin.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted.');
    }
}
