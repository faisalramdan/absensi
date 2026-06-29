<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            // Dashboard
            'dashboard.view',
            'dashboard.admin',
            'dashboard.employee',

            // User Management
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // Role Management
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',

            // Permission Management
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',

            // Menu Management
            'menu.view',
            'menu.create',
            'menu.edit',
            'menu.delete',

            // Activity
            'activity.view',

            // Activity Log
            'activity-log.view',

            // Company
            'company.view',
            'company.create',
            'company.edit',
            'company.delete',

            // Position
            'position.view',
            'position.create',
            'position.edit',
            'position.delete',

            // Employee Status
            'employee-status.view',
            'employee-status.create',
            'employee-status.edit',
            'employee-status.delete',

            // Employee
            'employee.view',
            'employee.create',
            'employee.edit',
            'employee.delete',

            // Leave Type
            'leave-type.view',
            'leave-type.create',
            'leave-type.edit',
            'leave-type.delete',

            // Leave Request
            'leave-request.view',
            'leave-request.create',
            'leave-request.edit',
            'leave-request.delete',
            'leave-request.approval',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }
    }
}
