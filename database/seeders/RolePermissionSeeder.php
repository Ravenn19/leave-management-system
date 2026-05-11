<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions with guard_name 'web' (default)
        Permission::create(['name' => 'manage leaves', 'guard_name' => 'web']);
        Permission::create(['name' => 'view all leaves', 'guard_name' => 'web']);

        // Create roles with guard_name 'web' (default)
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $employeeRole = Role::create(['name' => 'employee', 'guard_name' => 'web']);

        // Assign permissions to admin
        $adminRole->givePermissionTo(['manage leaves', 'view all leaves']);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'leave_quota' => 12,
        ]);
        $admin->assignRole('admin');

        // Create employee user
        $employee = User::create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => Hash::make('password123'),
            'leave_quota' => 12,
        ]);
        $employee->assignRole('employee');

        // Create additional employee for testing
        $employee2 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'leave_quota' => 12,
        ]);
        $employee2->assignRole('employee');

        $this->command->info('====================================');
        $this->command->info('Seeder completed successfully!');
        $this->command->info('====================================');
        $this->command->info('Admin Login: admin@example.com / password123');
        $this->command->info('Employee Login: employee@example.com / password123');
        $this->command->info('John Doe: john@example.com / password123');
        $this->command->info('====================================');
    }
}
