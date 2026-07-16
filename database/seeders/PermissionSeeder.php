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

            // Activity & Logs
            'activity.view',
            'activity-log.view',

            // Master Data (Company, Position, Status)
            'company.view',
            'company.create',
            'company.edit',
            'company.delete',

            'position.view',
            'position.create',
            'position.edit',
            'position.delete',

            'employee-status.view',
            'employee-status.create',
            'employee-status.edit',
            'employee-status.delete',

            // Employee Management
            'employee.view',
            'employee.create',
            'employee.edit',
            'employee.delete',
            'organization-structure.view',

            // Employee Contracts
            'employee-contract.view',
            'employee-contract.show',
            'employee-contract.create',
            'employee-contract.edit',
            'employee-contract.delete',

            // Team Management
            'team.view',
            'team.create',
            'team.edit',
            'team.delete',

            'team-member.view',
            'team-member.create',
            'team-member.edit',
            'team-member.delete',

            // Leave Management
            'leave-type.view',
            'leave-type.create',
            'leave-type.edit',
            'leave-type.delete',

            'leave-request.view',
            'leave-request.create',
            'leave-request.edit',
            'leave-request.delete',
            'leave-request.approval',

            // Shift Management
            'shift.view',
            'shift.create',
            'shift.edit',
            'shift.delete',

            'shift-assignment.view',
            'shift-assignment.create',
            'shift-assignment.edit',
            'shift-assignment.delete',

            // Holiday Management
            'holiday.view',
            'holiday.create',
            'holiday.edit',
            'holiday.delete',

            // Attendance Logs
            'attendance-log.view',
            'attendance-log.create',
            'attendance-log.edit',
            'attendance-log.delete',
            'attendance-log.import',

            // Attendance Processing & Reports
            'attendance.view',
            'attendance-processor.view',
            'attendance-processor.generate',
            'attendance-monthly.view',
            'attendance-monthly.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }
    }
}