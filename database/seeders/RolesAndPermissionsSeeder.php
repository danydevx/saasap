<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Convencion: modulo.accion para mantener permisos consistentes.
        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.activate',
            'users.deactivate',
            'users.assign_roles',
            'users.resend_verification',
            'invitations.view',
            'invitations.create',
            'invitations.update',
            'invitations.revoke',
            'legal-documents.view',
            'legal-documents.create',
            'legal-documents.update',
            'announcements.view',
            'announcements.create',
            'announcements.update',
            'announcements.delete',
            'announcements.publish',
            'automations.view',
            'automations.update',
            'templates.view',
            'templates.create',
            'templates.update',
            'templates.delete',
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
            'support.view',
            'support.reply',
            'support.update',
            'support.close',
            'help.view',
            'help.create',
            'help.update',
            'help.delete',
            'help.publish',
            'reports.view',
            'activity.view',
            'exports.view',
            'exports.download',
            'system-errors.view',
            'system-errors.update',
            'api-keys.manage',
            'api-keys.view',
            'api-keys.update',
            'api-keys.revoke',
            'webhooks.manage',
            'webhooks.view',
            'webhooks.update',
            'queues.view',
            'queues.retry',
            'queues.flush-failed',
            'feature-flags.view',
            'feature-flags.create',
            'feature-flags.update',
            'security-events.view',
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
            'invitations.view',
            'invitations.create',
            'invitations.update',
            'invitations.revoke',
            'legal-documents.view',
            'legal-documents.create',
            'legal-documents.update',
            'announcements.view',
            'announcements.create',
            'announcements.update',
            'announcements.delete',
            'announcements.publish',
            'automations.view',
            'automations.update',
            'templates.view',
            'templates.create',
            'templates.update',
            'templates.delete',
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
            'support.view',
            'support.reply',
            'support.update',
            'support.close',
            'help.view',
            'help.create',
            'help.update',
            'help.delete',
            'help.publish',
            'reports.view',
            'exports.view',
            'exports.download',
            'system-errors.view',
            'system-errors.update',
            'api-keys.view',
            'api-keys.update',
            'api-keys.revoke',
            'webhooks.view',
            'webhooks.update',
            'queues.view',
            'queues.retry',
            'queues.flush-failed',
            'feature-flags.view',
            'feature-flags.create',
            'feature-flags.update',
            'security-events.view',
        ]);

        $user->givePermissionTo([]);
        $normal->givePermissionTo([]);
        $member->givePermissionTo([
            'api-keys.manage',
            'webhooks.manage',
        ]);

        // Garantiza que el usuario ID 1 tenga rol super-admin para recuperar acceso total.
        $rootUser = User::query()->find(1);
        if ($rootUser) {
            $rootUser->assignRole($superAdmin);
        }
    }
}
