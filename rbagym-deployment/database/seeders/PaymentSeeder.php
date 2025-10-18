<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Member;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get member IDs
        $members = Member::all();
        
        foreach ($members as $member) {
            // Create a payment record for each member
            Payment::create([
                'member_id' => $member->id,
                'amount' => $this->getPlanAmount($member->membership),
                'payment_date' => now()->subDays(rand(1, 30)),
                'payment_time' => now()->format('H:i:s'),
                'status' => 'completed',
                'plan_type' => $member->membership,
                'duration_type' => 'monthly',
                'membership_start_date' => now()->subDays(rand(1, 30)),
                'membership_expiration_date' => now()->addDays(rand(1, 30)),
                'notes' => 'Initial membership payment'
            ]);
        }
    }

    private function getPlanAmount(string $planType): float
    {
        return match(strtolower($planType)) {
            'basic' => 700.00,
            'premium' => 1200.00,
            'vip' => 1900.00,
            default => 700.00
        };
    }
}
