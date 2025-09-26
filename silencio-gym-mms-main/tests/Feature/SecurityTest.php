<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function member_registration_does_not_auto_login()
    {
        $response = $this->post('/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Should redirect to login page, not member dashboard
        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Registration successful! Please log in with your credentials.');
        
        // User should not be logged in
        $this->assertFalse(auth()->guard('member')->check());
        $this->assertFalse(auth()->guard('web')->check());
    }

    /** @test */
    public function new_member_has_member_role()
    {
        $response = $this->post('/register', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $member = Member::where('email', 'jane@example.com')->first();
        $this->assertEquals('member', $member->role);
        $this->assertTrue($member->isMember());
        $this->assertFalse($member->isAdmin());
    }

    /** @test */
    public function member_cannot_access_admin_routes()
    {
        // Create a member
        $member = Member::create([
            'uid' => 'test-uid',
            'member_number' => 'M-TEST123',
            'membership' => 'basic',
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'role' => 'member',
        ]);

        // Login as member
        $this->actingAs($member, 'member');

        // Try to access admin dashboard
        $response = $this->get('/dashboard');
        $response->assertRedirect('/member');
        $response->assertSessionHasErrors(['access']);

        // Try to access admin members page
        $response = $this->get('/members');
        $response->assertRedirect('/member');
        $response->assertSessionHasErrors(['access']);

        // Try to access membership management
        $response = $this->get('/membership/plans');
        $response->assertRedirect('/member');
        $response->assertSessionHasErrors(['access']);
    }

    /** @test */
    public function admin_can_access_admin_routes()
    {
        // Create an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Login as admin
        $this->actingAs($admin, 'web');

        // Should be able to access admin dashboard
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function non_admin_user_cannot_access_admin_routes()
    {
        // Create a user with member role (shouldn't happen but test security)
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'member',
        ]);

        // Login as this user via web guard
        $this->actingAs($user, 'web');

        // Should be redirected to login with error
        $response = $this->get('/dashboard');
        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['email' => 'Access denied. Admin privileges required.']);
    }

    /** @test */
    public function member_can_access_member_routes()
    {
        // Create a member
        $member = Member::create([
            'uid' => 'test-uid',
            'member_number' => 'M-TEST123',
            'membership' => 'basic',
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'role' => 'member',
        ]);

        // Login as member
        $this->actingAs($member, 'member');

        // Should be able to access member dashboard
        $response = $this->get('/member');
        $response->assertStatus(200);

        // Should be able to access member plans
        $response = $this->get('/member/plans');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_cannot_access_member_routes()
    {
        // Create an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Login as admin
        $this->actingAs($admin, 'web');

        // Should be redirected to admin dashboard when trying to access member routes
        $response = $this->get('/member');
        $response->assertRedirect('/dashboard');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        // Try to access admin dashboard without login
        $response = $this->get('/dashboard');
        $response->assertRedirect('/');

        // Try to access member dashboard without login
        $response = $this->get('/member');
        $response->assertRedirect('/');
    }

    /** @test */
    public function member_login_redirects_to_member_dashboard()
    {
        // Create a member
        $member = Member::create([
            'uid' => 'test-uid',
            'member_number' => 'M-TEST123',
            'membership' => 'basic',
            'first_name' => 'Test',
            'last_name' => 'Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
            'role' => 'member',
        ]);

        // Login as member
        $response = $this->post('/login', [
            'email' => 'member@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/member');
        $this->assertTrue(auth()->guard('member')->check());
    }

    /** @test */
    public function admin_login_redirects_to_admin_dashboard()
    {
        // Login with admin credentials
        $response = $this->post('/login', [
            'email' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(auth()->guard('web')->check());
    }
}
