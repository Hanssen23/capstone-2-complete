<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_member()
    {
        $memberData = [
            'uid' => 'TEST001',
            'member_number' => 'MEM001',
            'membership' => 'premium',
            'full_name' => 'Test User',
            'mobile_number' => '+1 (555) 123-4567',
            'email' => 'test@example.com',
        ];

        $response = $this->post(route('members.store'), $memberData);

        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseHas('members', [
            'uid' => 'TEST001',
            'full_name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_can_edit_member()
    {
        $member = Member::create([
            'uid' => 'TEST001',
            'member_number' => 'MEM001',
            'membership' => 'basic',
            'full_name' => 'Original Name',
            'mobile_number' => '+1 (555) 123-4567',
            'email' => 'original@example.com',
        ]);

        $updatedData = [
            'uid' => 'TEST001',
            'member_number' => 'MEM001',
            'membership' => 'premium',
            'full_name' => 'Updated Name',
            'mobile_number' => '+1 (555) 987-6543',
            'email' => 'updated@example.com',
        ];

        $response = $this->put(route('members.update', $member->id), $updatedData);

        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'full_name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_can_delete_member()
    {
        $member = Member::create([
            'uid' => 'TEST001',
            'member_number' => 'MEM001',
            'membership' => 'basic',
            'full_name' => 'Test User',
            'mobile_number' => '+1 (555) 123-4567',
            'email' => 'test@example.com',
        ]);

        $response = $this->delete(route('members.destroy', $member->id));

        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }
}
