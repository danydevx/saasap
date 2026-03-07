<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.activate',
            'users.deactivate',
            'users.assign_roles',
            'users.resend_verification',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'settings.view',
            'settings.update',
            'plans.view',
            'plans.create',
            'plans.update',
            'plans.delete',
            'subscriptions.view',
            'subscriptions.create',
            'subscriptions.update',
            'subscriptions.cancel',
            'payments.view',
            'payments.create',
            'payments.update',
            'coupons.view',
            'coupons.create',
            'coupons.update',
            'coupons.delete',
            'invoices.view',
            'invoices.create',
            'invoices.update',
            'invoices.download',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);
        $normal = Role::firstOrCreate(['name' => 'normal']);
        $member = Role::firstOrCreate(['name' => 'member']);

        $superAdmin->givePermissionTo(Permission::all());

        $admin->givePermissionTo([
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.activate',
            'users.deactivate',
            'users.assign_roles',
            'users.resend_verification',
            'roles.view',
            'permissions.view',
            'settings.view',
            'settings.update',
            'activity.view',
            'plans.view',
            'plans.create',
            'plans.update',
            'plans.delete',
            'subscriptions.view',
            'subscriptions.update',
            'payments.view',
            'payments.create',
            'payments.update',
            'coupons.view',
            'coupons.create',
            'coupons.update',
            'coupons.delete',
            'invoices.view',
            'invoices.create',
            'invoices.update',
            'invoices.download',
        ]);

        $user->givePermissionTo([]);
        $normal->givePermissionTo([]);
        $member->givePermissionTo([]);
    }
}
