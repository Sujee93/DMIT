<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created_updated_and_searched(): void
    {
        $this->actingAs($this->staff());

        $this->post('/products', [
            'code' => ' sn-100 ', 'name' => 'Runner', 'color' => 'Black', 'size' => '42',
            'cost' => '1500', 'price' => '2200.50', 'is_active' => '1',
        ])->assertRedirect('/products');

        $product = Product::firstOrFail();
        $this->assertSame('SN-100', $product->code);
        $this->assertSame('2200.50', $product->price);

        $this->put("/products/{$product->id}", [
            'code' => 'SN-100', 'name' => 'Runner Pro', 'cost' => '1500', 'price' => '2500', 'is_active' => '0',
        ])->assertRedirect('/products');
        $this->assertFalse($product->fresh()->is_active);

        $this->get('/products?q=Pro')->assertSee('Runner Pro');
    }

    public function test_product_code_must_be_unique_and_prices_valid(): void
    {
        $this->actingAs($this->staff());
        Product::factory()->create(['code' => 'SN-1']);

        $this->post('/products', ['code' => 'sn-1', 'name' => 'X', 'cost' => '-5', 'price' => '1.234'])
            ->assertSessionHasErrors(['code', 'cost', 'price']);
    }

    public function test_only_admin_can_delete_products(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->staff())->delete("/products/{$product->id}")->assertForbidden();
        $this->actingAs($this->admin())->delete("/products/{$product->id}")->assertRedirect('/products');
        $this->assertModelMissing($product);
    }

    public function test_search_input_is_escaped_in_output(): void
    {
        $this->actingAs($this->staff());

        $this->get('/products?q='.urlencode('<script>alert(1)</script>'))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
