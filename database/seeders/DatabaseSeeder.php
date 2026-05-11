// database/seeders/RolePermissionSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        Permission::create(['name' => 'manage leaves']);
        Permission::create(['name' => 'view all leaves']);

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $employeeRole = Role::create(['name' => 'employee']);

        // Assign permissions to admin
        $adminRole->givePermissionTo(['manage leaves', 'view all leaves']);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'leave_quota' => 12,
        ]);
        $admin->assignRole('admin');

        // Create employee user
        $employee = User::create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => bcrypt('password123'),
            'leave_quota' => 12,
        ]);
        $employee->assignRole('employee');
    }
}
