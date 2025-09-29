<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [
            [
                'uid' => 'UID001',
                'member_number' => 'MEM001',
                'membership' => 'premium',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'mobile_number' => '+1 (555) 123-4567',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password123'),
                'status' => 'active'
            ],
            [
                'uid' => 'UID002',
                'member_number' => 'MEM002',
                'membership' => 'basic',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'mobile_number' => '+1 (555) 234-5678',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password123'),
                'status' => 'active'
            ],
            [
                'uid' => 'UID003',
                'member_number' => 'MEM003',
                'membership' => 'vip',
                'first_name' => 'Mike',
                'last_name' => 'Johnson',
                'mobile_number' => '+1 (555) 345-6789',
                'email' => 'mike.johnson@example.com',
                'password' => Hash::make('password123'),
                'status' => 'active'
            ],
            [
                'uid' => 'UID004',
                'member_number' => 'MEM004',
                'membership' => 'premium',
                'first_name' => 'Sarah',
                'last_name' => 'Wilson',
                'mobile_number' => '+1 (555) 456-7890',
                'email' => 'sarah.wilson@example.com',
                'password' => Hash::make('password123'),
                'status' => 'active'
            ],
            [
                'uid' => 'UID005',
                'member_number' => 'MEM005',
                'membership' => 'basic',
                'first_name' => 'David',
                'last_name' => 'Brown',
                'mobile_number' => '+1 (555) 567-8901',
                'email' => 'david.brown@example.com',
                'password' => Hash::make('password123'),
                'status' => 'active'
            ],
        ];

        foreach ($members as $memberData) {
            Member::create($memberData);
        }
    }
}
