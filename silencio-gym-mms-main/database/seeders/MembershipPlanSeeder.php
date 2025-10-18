<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipPlan;

class MembershipPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'description' => 'Basic gym access with standard facilities',
                'price' => 900.00, // Monthly ₱900.00 (updated to match config)
                'currency' => '₱',
                'duration_days' => 30,
                'features' => [
                    'Gym access',
                    'Basic equipment',
                    'Locker room access',
                    'Standard hours (6 AM - 10 PM)'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'VIP',
                'description' => 'Premium access with additional amenities',
                'price' => 1250.00, // Monthly ₱1,250.00 (updated to match config)
                'currency' => '₱',
                'duration_days' => 30,
                'features' => [
                    'All Basic features',
                    'Premium equipment access',
                    'Group classes',
                    'Personal trainer consultation (1/month)',
                    'Extended hours (24/7)',
                    'Guest passes (2/month)'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'description' => 'Full access with all premium features',
                'price' => 950.00, // Monthly ₱950.00 (updated to match config)
                'currency' => '₱',
                'duration_days' => 30,
                'features' => [
                    'All VIP features',
                    'Unlimited group classes',
                    'Personal trainer sessions (4/month)',
                    'Nutrition consultation',
                    'Spa access',
                    'Guest passes (5/month)',
                    'Priority booking',
                    'Exclusive events'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::create($plan);
        }
    }
}
