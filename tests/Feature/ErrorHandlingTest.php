<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.debug' => false]);
    }

    public function test_missing_record_shows_friendly_404_page(): void
    {
        $this->actingAs($this->staff())
            ->get('/invoices/999999')
            ->assertNotFound()
            ->assertSee('Page not found');
    }

    public function test_forbidden_page_is_friendly(): void
    {
        $this->actingAs($this->staff())
            ->get('/settings')
            ->assertForbidden()
            ->assertSee('Access denied');
    }

    public function test_unexpected_error_while_saving_returns_to_form_with_input_and_code(): void
    {
        $this->actingAs($this->admin());
        // Simulate a database failure on save (e.g. a broken table on the server).
        BusinessSetting::saving(fn () => throw new \RuntimeException('Simulated database failure'));

        $this->from('/settings')
            ->put('/settings', ['name' => 'WH Marketing', 'currency_symbol' => 'Rs.', 'invoice_prefix' => 'INV', 'default_due_days' => 30])
            ->assertRedirect('/settings')
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'nothing was saved'))
            ->assertSessionHasInput('name', 'WH Marketing');

    }

    public function test_pages_still_render_when_settings_cannot_be_loaded(): void
    {
        $this->actingAs($this->staff());
        $this->app->bind(BusinessSetting::class.'@current', fn () => throw new \RuntimeException('Simulated database failure'));

        $this->get('/dashboard')->assertOk();
        $this->get('/invoices/999999')->assertNotFound()->assertSee('Page not found');
    }

    public function test_unexpected_error_on_a_page_shows_friendly_500_with_reference(): void
    {
        Route::middleware('web')->get('/_boom', fn () => throw new \RuntimeException('boom'));

        $this->get('/_boom')
            ->assertStatus(500)
            ->assertSee('Something went wrong')
            ->assertSee(\App\Exceptions\Handler::reference())
            ->assertDontSee('RuntimeException');
    }

    public function test_expired_session_redirects_back_to_login_with_message(): void
    {
        Route::middleware('web')->post('/_expired', fn () => abort(419));

        $this->post('/_expired', ['email' => 'a@b.com', 'password' => 'secret'])
            ->assertRedirect('/login')
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'session expired'))
            ->assertSessionMissing('_old_input.password');
    }

    public function test_login_page_shows_session_errors(): void
    {
        $this->withSession(['error' => 'Your session expired, so that action was not saved. Please try again.'])
            ->get('/login')
            ->assertOk()
            ->assertSee('Your session expired');
    }
}
