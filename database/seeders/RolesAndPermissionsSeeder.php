<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Work Orders
            'workorders.view',
            'workorders.create',
            'workorders.update',
            'workorders.delete',
            'workorders.assign',

            // Inspections
            'inspections.view',
            'inspections.create',
            'inspections.update',
            'inspections.delete',
            'inspections.export',

            // Audit Logs ✅ (this is the one you’re missing)
            'audit.view',
            'audit.export',

            // Users / Roles
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $supervisor = Role::firstOrCreate(['name' => 'Ops Supervisor']);
        $staff = Role::firstOrCreate(['name' => 'Ops Staff']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);

        $admin->syncPermissions($permissions);

        $supervisor->syncPermissions([
            'workorders.view','workorders.create','workorders.update','workorders.delete','workorders.assign',
            'inspections.view','inspections.create','inspections.update','inspections.delete','inspections.export',
            'users.view','users.create','users.update',
        ]);

        $staff->syncPermissions([
            'workorders.view','workorders.create','workorders.update',
            'inspections.view','inspections.create','inspections.update','inspections.export',
        ]);

        $viewer->syncPermissions([
            'workorders.view',
            'inspections.view',
        ]);

        // ✅ Assign Admin to your live account (set this to YOUR email)
        $email = env('jaydenlyricr@gmail.com');

        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->syncRoles(['Admin']);
            }
        }
    }
}
