<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\BusinessSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsAndUsersTest extends TestCase
{
    use RefreshDatabase;

    private function settingsPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Step Ahead Distributors',
            'currency_symbol' => 'Rs.',
            'invoice_prefix' => 'SA-',
            'default_due_days' => 45,
            'phone' => '0112 000 000',
        ], $overrides);
    }

    public function test_admin_can_update_business_settings_and_logo(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->put('/settings', $this->settingsPayload(['logo' => UploadedFile::fake()->image('logo.png', 200, 80)]))
            ->assertRedirect('/settings');

        $settings = BusinessSetting::current();
        $this->assertSame('Step Ahead Distributors', $settings->name);
        $this->assertSame(45, $settings->default_due_days);
        Storage::disk('public')->assertExists($settings->logo_path);
        $this->assertStringStartsWith('logos/', $settings->logo_path);

        $this->get('/dashboard')->assertSee('Step Ahead Distributors');
    }

    public function test_svg_and_non_image_logos_are_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $svg = UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
        $this->put('/settings', $this->settingsPayload(['logo' => $svg]))->assertSessionHasErrors('logo');

        $php = UploadedFile::fake()->createWithContent('logo.php', '<?php echo 1;');
        $this->put('/settings', $this->settingsPayload(['logo' => $php]))->assertSessionHasErrors('logo');
    }

    public function test_admin_manages_users_and_cannot_lock_themselves_out(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $this->post('/users', [
            'name' => 'Nimal', 'email' => 'Nimal@Example.com', 'role' => 'staff', 'is_active' => '1',
            'password' => 'Secret123', 'password_confirmation' => 'Secret123',
        ])->assertRedirect('/users');

        $user = User::where('email', 'nimal@example.com')->firstOrFail();
        $this->assertSame(UserRole::Staff, $user->role);

        $this->post('/users', [
            'name' => 'Weak', 'email' => 'weak@example.com', 'role' => 'staff',
            'password' => 'short', 'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');

        $this->put("/users/{$admin->id}", ['name' => $admin->name, 'email' => $admin->email, 'role' => 'staff', 'is_active' => '1'])
            ->assertSessionHasErrors('role');
        $this->delete("/users/{$admin->id}")->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->isAdmin());

        $this->delete("/users/{$user->id}")->assertRedirect('/users');
        $this->assertModelMissing($user);
    }

    public function test_role_cannot_be_mass_assigned_by_staff(): void
    {
        $staff = $this->staff();
        $this->actingAs($staff)->post('/users', [
            'name' => 'X', 'email' => 'x@example.com', 'role' => 'admin', 'password' => 'Secret123', 'password_confirmation' => 'Secret123',
        ])->assertForbidden();

        $this->assertSame(1, User::count());
    }
}
