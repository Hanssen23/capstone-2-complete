<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminAndEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User in users table
        User::firstOrCreate(
            ['email' => 'admin@silencio-gym.com'],
            [
                'name' => 'Admin User',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'mobile_number' => '+1 (555) 000-0001',
                'email' => 'admin@silencio-gym.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create Employee User in users table
        User::firstOrCreate(
            ['email' => 'employee@silencio-gym.com'],
            [
                'name' => 'Employee User',
                'first_name' => 'Employee',
                'last_name' => 'User',
                'mobile_number' => '+1 (555) 000-0002',
                'email' => 'employee@silencio-gym.com',
                'password' => Hash::make('employee123'),
                'role' => 'employee',
                'email_verified_at' => now(),
            ]
        );

        // Create Manager User in users table
        User::firstOrCreate(
            ['email' => 'manager@silencio-gym.com'],
            [
                'name' => 'Manager User',
                'first_name' => 'Manager',
                'last_name' => 'User',
                'mobile_number' => '+1 (555) 000-0003',
                'email' => 'manager@silencio-gym.com',
                'password' => Hash::make('manager123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create Staff User in users table
        User::firstOrCreate(
            ['email' => 'staff@silencio-gym.com'],
            [
                'name' => 'Staff User',
                'first_name' => 'Staff',
                'last_name' => 'User',
                'mobile_number' => '+1 (555) 000-0004',
                'email' => 'staff@silencio-gym.com',
                'password' => Hash::make('staff123'),
                'role' => 'employee',
                'email_verified_at' => now(),
            ]
        );
    }
}
