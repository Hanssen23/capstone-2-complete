<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Member;
use App\Models\Attendance;
use App\Models\Payment;
use App\Models\RfidLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MemberProfileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user for authentication
        $this->user = \App\Models\User::factory()->create([
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin123'),
        ]);
    }

    /** @test */
    public function it_can_display_member_profile_page()
    {
        $member = Member::factory()->create([
            'uid' => 'TEST001',
            'member_number' => 'MEM001',
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '1234567890',
            'membership' => 'premium',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('members.profile', $member->id));

        $response->assertStatus(200);
        $response->assertSee('John Doe\'s Profile');
        $response->assertSee('TEST001');
        $response->assertSee('MEM001');
        $response->assertSee('Premium');
    }

    /** @test */
    public function it_shows_attendance_history_in_profile()
    {
        $member = Member::factory()->create();
        
        // Create some attendance records
        Attendance::factory()->count(3)->create([
            'member_id' => $member->id,
            'check_in_time' => now()->subDays(rand(1, 30)),
            'status' => 'checked_out',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('members.profile', $member->id));

        $response->assertStatus(200);
        $response->assertSee('Recent Attendance');
    }

    /** @test */
    public function it_shows_rfid_activity_in_profile()
    {
        $member = Member::factory()->create(['uid' => 'RFID001']);
        
        // Create some RFID logs
        RfidLog::factory()->count(2)->create([
            'card_uid' => 'RFID001',
            'action' => 'check_in',
            'status' => 'success',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('members.profile', $member->id));

        $response->assertStatus(200);
        $response->assertSee('RFID Activity');
    }

    /** @test */
    public function it_shows_payment_history_in_profile()
    {
        $member = Member::factory()->create();
        
        // Create some payment records
        Payment::factory()->count(2)->create([
            'member_id' => $member->id,
            'amount' => 50.00,
            'payment_method' => 'cash',
            'status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('members.profile', $member->id));

        $response->assertStatus(200);
        $response->assertSee('Payment History');
    }

    /** @test */
    public function it_handles_missing_membership_data_gracefully()
    {
        $member = Member::factory()->create([
            'membership_expires_at' => null,
            'current_membership_period_id' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('members.profile', $member->id));

        $response->assertStatus(200);
        $response->assertSee('No expiration date set');
        $response->assertSee('No expiration');
    }

    /** @test */
    public function it_calculates_membership_status_correctly()
    {
        // Active membership
        $activeMember = Member::factory()->create([
            'membership_expires_at' => now()->addDays(30),
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('members.profile', $activeMember->id));

        $response->assertStatus(200);
        $response->assertSee('Active');

        // Expired membership
        $expiredMember = Member::factory()->create([
            'membership_expires_at' => now()->subDays(1),
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('members.profile', $expiredMember->id));

        $response->assertStatus(200);
        $response->assertSee('Expired');
    }

    /** @test */
    public function it_redirects_to_members_list_from_profile()
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('members.profile', $member->id));

        $response->assertStatus(200);
        $response->assertSee('Back to Members');
    }

    /** @test */
    public function it_returns_404_for_nonexistent_member()
    {
        $response = $this->actingAs($this->user)
            ->get(route('members.profile', 99999));

        $response->assertStatus(404);
    }
}
