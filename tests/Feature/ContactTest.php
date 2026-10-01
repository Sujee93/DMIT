<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_and_suppliers_share_one_page(): void
    {
        $this->actingAs($this->staff());
        Contact::factory()->customer()->create(['name' => 'Shoe Palace']);
        Contact::factory()->supplier()->create(['name' => 'Lanka Mfg']);

        $this->get('/contacts')->assertSee('Shoe Palace')->assertSee('Lanka Mfg');
        $this->get('/contacts?type=supplier')->assertSee('Lanka Mfg')->assertDontSee('Shoe Palace');
    }

    public function test_contact_validation(): void
    {
        $this->actingAs($this->staff());

        $this->post('/contacts', ['type' => 'hacker', 'name' => '', 'email' => "a@b.com\r\nBcc: x@y.com"])
            ->assertSessionHasErrors(['type', 'name', 'email']);

        $this->post('/contacts', ['type' => 'customer', 'name' => 'City Shoes', 'email' => 'city@example.com'])
            ->assertRedirect();
        $this->assertDatabaseHas('contacts', ['name' => 'City Shoes', 'type' => 'customer']);
    }
}
