<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UidPoolSeeder::class,
            AdminUserSeeder::class,
            AdminAndEmployeeSeeder::class,
            MembershipPlanSeeder::class,
            MemberSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
