<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_with_security_headers(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Login to')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        foreach (['/dashboard', '/products', '/contacts', '/invoices', '/reports', '/settings', '/users'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_user_can_log_in_and_out(): void
    {
        $user = User::factory()->create(['email' => 'staff@example.com']);

        $this->post('/login', ['email' => 'STAFF@example.com', 'password' => 'password123'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected_with_generic_message(): void
    {
        User::factory()->create(['email' => 'staff@example.com']);

        $this->from('/login')->post('/login', ['email' => 'staff@example.com', 'password' => 'wrong-pass'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        User::factory()->inactive()->create(['email' => 'old@example.com']);

        $this->post('/login', ['email' => 'old@example.com', 'password' => 'password123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_deactivated_user_is_logged_out_on_next_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $user->forceFill(['is_active' => false])->save();

        $this->get('/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        User::factory()->create(['email' => 'staff@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'staff@example.com', 'password' => 'nope']);
        }

        $this->post('/login', ['email' => 'staff@example.com', 'password' => 'password123'])
            ->assertStatus(429);
        $this->assertGuest();
    }

    public function test_staff_cannot_access_admin_areas(): void
    {
        $this->actingAs($this->staff());

        $this->get('/settings')->assertForbidden();
        $this->get('/users')->assertForbidden();
        $this->put('/settings', ['name' => 'Hacked'])->assertForbidden();
    }
}
