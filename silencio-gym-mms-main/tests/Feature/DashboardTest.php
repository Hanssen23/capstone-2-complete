<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_correct_member_count()
    {
        // Create some test members
        Member::create([
            'uid' => 'TEST001',
            'member_number' => 'MEM001',
            'membership' => 'premium',
            'full_name' => 'Test User 1',
            'mobile_number' => '+1 (555) 123-4567',
            'email' => 'test1@example.com',
        ]);

        Member::create([
            'uid' => 'TEST002',
            'member_number' => 'MEM002',
            'membership' => 'basic',
            'full_name' => 'Test User 2',
            'mobile_number' => '+1 (555) 234-5678',
            'email' => 'test2@example.com',
        ]);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('2'); // Should show 2 members
    }

    public function test_dashboard_shows_zero_when_no_members()
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('0'); // Should show 0 members
    }

    public function test_dashboard_shows_correct_expiring_memberships_count()
    {
        // Create a member expiring tomorrow
        Member::create([
            'uid' => 'TEST003',
            'member_number' => 'MEM003',
            'membership' => 'premium',
            'first_name' => 'Test',
            'last_name' => 'User 3',
            'mobile_number' => '+1 (555) 345-6789',
            'email' => 'test3@example.com',
            'status' => 'active',
            'membership_expires_at' => now()->addDays(2),
        ]);

        // Create a member expiring in 6 days
        Member::create([
            'uid' => 'TEST004',
            'member_number' => 'MEM004',
            'membership' => 'basic',
            'first_name' => 'Test',
            'last_name' => 'User 4',
            'mobile_number' => '+1 (555) 456-7890',
            'email' => 'test4@example.com',
            'status' => 'active',
            'membership_expires_at' => now()->addDays(6),
        ]);

        // Create a member expiring in 10 days (should not be counted)
        Member::create([
            'uid' => 'TEST005',
            'member_number' => 'MEM005',
            'membership' => 'premium',
            'first_name' => 'Test',
            'last_name' => 'User 5',
            'mobile_number' => '+1 (555) 567-8901',
            'email' => 'test5@example.com',
            'status' => 'active',
            'membership_expires_at' => now()->addDays(10),
        ]);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('expiringMembershipsCount', 2); // Should show 2 members expiring this week
    }
}
