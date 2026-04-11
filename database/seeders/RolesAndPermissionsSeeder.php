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
            'workorders.view',
            'workorders.create',
            'workorders.update',
            'workorders.delete',
            'workorders.assign',
            'inspections.view',
            'inspections.create',
            'inspections.update',
            'inspections.delete',
            'inspections.export',
            'notams.view',
            'notams.create',
            'notams.update',
            'notams.delete',
            'passalongs.view',
            'passalongs.create',
            'passalongs.update',
            'passalongs.delete',
            'passalongs.export',
            'audit.view',
            'audit.export',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin      = Role::firstOrCreate(['name' => 'Admin']);
        $supervisor = Role::firstOrCreate(['name' => 'Ops Supervisor']);
        $staff      = Role::firstOrCreate(['name' => 'Ops Staff']);
        $viewer     = Role::firstOrCreate(['name' => 'Viewer']);

        $admin->syncPermissions($permissions);

        $supervisor->syncPermissions([
            'workorders.view',
            'workorders.create',
            'workorders.update',
            'workorders.delete',
            'workorders.assign',
            'inspections.view',
            'inspections.create',
            'inspections.update',
            'inspections.delete',
            'inspections.export',
            'notams.view',
            'notams.create',
            'notams.update',
            'notams.delete',
            'passalongs.view',
            'passalongs.create',
            'passalongs.update',
            'passalongs.delete',
            'passalongs.export',
            'users.view',
            'users.create',
            'users.update',
        ]);

        $staff->syncPermissions([
            'workorders.view',
            'workorders.create',
            'workorders.update',
            'inspections.view',
            'inspections.create',
            'inspections.update',
            'inspections.export',
            'notams.view',
            'notams.create',
            'notams.update',
            'passalongs.view',
            'passalongs.create',
            'passalongs.update',
            'passalongs.export',
        ]);

        $viewer->syncPermissions([
            'workorders.view',
            'inspections.view',
            'notams.view',
            'passalongs.view',
        ]);

        $email = env('ADMIN_EMAIL', 'jaydenlyricr@gmail.com');

        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->syncRoles(['Admin']);
            }
        }
    }
}