<?php

namespace App\Support;

use App\Enums\RoleName;

class PermissionRegistry
{
    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            'dashboard.view',

            'leads.view',
            'leads.create',
            'leads.edit',
            'leads.delete',
            'leads.assign',
            'leads.export',
            'leads.import',

            'followups.view',
            'followups.create',
            'followups.edit',
            'followups.delete',

            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',

            'companies.view',
            'companies.create',
            'companies.edit',
            'companies.delete',

            'products.view',
            'products.create',
            'products.edit',
            'products.delete',

            'quotations.view',
            'quotations.create',
            'quotations.edit',
            'quotations.delete',
            'quotations.send',

            'tasks.view',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',

            'calendar.view',
            'activities.view',

            'emails.send',
            'whatsapp.send',
            'sms.send',

            'reports.view',
            'reports.export',

            'settings.view',
            'settings.edit',

            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            'permissions.view',
            'permissions.edit',

            'indiamart.view',
            'indiamart.sync',
            'indiamart.settings',

            'notifications.view',
            'api.access',
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function rolePermissions(): array
    {
        $all = self::all();
        $viewOnly = array_values(array_filter($all, fn (string $p) => str_ends_with($p, '.view')));

        return [
            RoleName::SuperAdmin->value => $all,

            RoleName::Admin->value => array_values(array_diff($all, [
                'roles.delete',
            ])),

            RoleName::SalesManager->value => [
                'dashboard.view',
                'leads.view', 'leads.create', 'leads.edit', 'leads.assign', 'leads.export', 'leads.import',
                'followups.view', 'followups.create', 'followups.edit', 'followups.delete',
                'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
                'companies.view', 'companies.create', 'companies.edit',
                'products.view',
                'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send',
                'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
                'calendar.view', 'activities.view',
                'emails.send', 'whatsapp.send', 'sms.send',
                'reports.view', 'reports.export',
                'notifications.view',
            ],

            RoleName::SalesExecutive->value => [
                'dashboard.view',
                'leads.view', 'leads.create', 'leads.edit',
                'followups.view', 'followups.create', 'followups.edit',
                'customers.view', 'customers.create', 'customers.edit',
                'companies.view',
                'products.view',
                'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.send',
                'tasks.view', 'tasks.create', 'tasks.edit',
                'calendar.view', 'activities.view',
                'emails.send', 'whatsapp.send',
                'notifications.view',
            ],

            RoleName::TeleCaller->value => [
                'dashboard.view',
                'leads.view', 'leads.create', 'leads.edit',
                'followups.view', 'followups.create', 'followups.edit',
                'customers.view',
                'tasks.view', 'tasks.create',
                'calendar.view', 'activities.view',
                'sms.send',
                'notifications.view',
            ],

            RoleName::Marketing->value => [
                'dashboard.view',
                'leads.view', 'leads.create', 'leads.import',
                'customers.view',
                'activities.view',
                'reports.view', 'reports.export',
                'notifications.view',
            ],

            RoleName::Viewer->value => $viewOnly,
        ];
    }
}
