<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

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

        // Admin gets everything
        $admin->syncPermissions($permissions);

        // Supervisor: everything except role management (and optionally user delete)
        $supervisor->syncPermissions([
            'workorders.view','workorders.create','workorders.update','workorders.delete','workorders.assign',
            'inspections.view','inspections.create','inspections.update','inspections.delete','inspections.export',
            'users.view','users.create','users.update',
        ]);

        // Staff: create/update, view, export, no deletes by default
        $staff->syncPermissions([
            'workorders.view','workorders.create','workorders.update',
            'inspections.view','inspections.create','inspections.update','inspections.export',
        ]);

        // Viewer: read-only
        $viewer->syncPermissions([
            'workorders.view',
            'inspections.view',
        ]);
    }
}
