<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminAndEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        Member::firstOrCreate(
            ['email' => 'admin@silencio-gym.com'],
            [
                'uid' => 'ADMIN001',
                'member_number' => 'ADM001',
                'membership' => null, // Admin doesn't need membership
                'first_name' => 'Admin',
                'last_name' => 'User',
                'mobile_number' => '+1 (555) 000-0001',
                'email' => 'admin@silencio-gym.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'subscription_status' => 'not_subscribed',
            ]
        );

        // Create Employee User
        Member::firstOrCreate(
            ['email' => 'employee@silencio-gym.com'],
            [
                'uid' => 'EMP001',
                'member_number' => 'EMP001',
                'membership' => null, // Employee doesn't need membership
                'first_name' => 'Employee',
                'last_name' => 'User',
                'mobile_number' => '+1 (555) 000-0002',
                'email' => 'employee@silencio-gym.com',
                'password' => Hash::make('employee123'),
                'role' => 'employee',
                'status' => 'active',
                'subscription_status' => 'not_subscribed',
            ]
        );

        // Create additional admin user
        Member::firstOrCreate(
            ['email' => 'manager@silencio-gym.com'],
            [
                'uid' => 'MGR001',
                'member_number' => 'MGR001',
                'membership' => null,
                'first_name' => 'Manager',
                'last_name' => 'User',
                'mobile_number' => '+1 (555) 000-0003',
                'email' => 'manager@silencio-gym.com',
                'password' => Hash::make('manager123'),
                'role' => 'admin',
                'status' => 'active',
                'subscription_status' => 'not_subscribed',
            ]
        );

        // Create additional employee user
        Member::firstOrCreate(
            ['email' => 'staff@silencio-gym.com'],
            [
                'uid' => 'STF001',
                'member_number' => 'STF001',
                'membership' => null,
                'first_name' => 'Staff',
                'last_name' => 'User',
                'mobile_number' => '+1 (555) 000-0004',
                'email' => 'staff@silencio-gym.com',
                'password' => Hash::make('staff123'),
                'role' => 'employee',
                'status' => 'active',
                'subscription_status' => 'not_subscribed',
            ]
        );
    }
}
