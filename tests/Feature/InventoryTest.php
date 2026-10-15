<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    private Contact $supplier;

    private Contact $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->admin());
        $this->supplier = Contact::factory()->supplier()->create();
        $this->customer = Contact::factory()->customer()->create();
    }

    private function invoice(Product $product, int $qty): \Illuminate\Testing\TestResponse
    {
        return $this->post('/invoices', [
            'customer_id' => $this->customer->id,
            'invoice_date' => today()->toDateString(),
            'items' => [['product_id' => $product->id, 'quantity' => $qty, 'unit_price' => '100']],
        ]);
    }

    public function test_grn_adds_stock_records_bill_and_updates_cost(): void
    {
        $product = Product::factory()->stock(0)->create(['cost' => '50']);

        $grn = $this->receive($this->supplier, [[$product, 24, '62.50']], ['discount' => '100']);

        $this->assertSame('GRN-00001', $grn->grn_no);
        $this->assertSame('1500.00', $grn->subtotal);
        $this->assertSame('1400.00', $grn->total);
        $this->assertSame(24, $product->fresh()->stock);
        $this->assertSame('62.50', $product->fresh()->cost);
        $this->assertSame('1400.00', $this->supplier->balance());
        $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'source' => 'grn', 'quantity' => 24]);
    }

    public function test_cannot_invoice_more_than_stock(): void
    {
        $product = Product::factory()->stock(0)->create(['code' => 'SH-1']);

        $this->invoice($product, 1)->assertSessionHasErrors(['items.0.quantity' => 'Not enough stock for SH-1: only 0 available.']);

        $this->receive($this->supplier, [[$product, 5, '40']]);
        $this->invoice($product, 6)->assertSessionHasErrors('items.0.quantity');
        $this->assertSame(0, Invoice::count());
        $this->assertSame(5, $product->fresh()->stock);

        $this->invoice($product, 5)->assertSessionHasNoErrors();
        $this->assertSame(0, $product->fresh()->stock);
    }

    public function test_editing_and_deleting_an_invoice_corrects_stock(): void
    {
        $product = Product::factory()->stock(0)->create();
        $this->receive($this->supplier, [[$product, 10, '40']]);
        $this->invoice($product, 4);
        $invoice = Invoice::firstOrFail();
        $this->assertSame(6, $product->fresh()->stock);

        // Editing can use the 4 already on the invoice plus the 6 left.
        $this->put("/invoices/{$invoice->id}", [
            'customer_id' => $this->customer->id,
            'invoice_date' => today()->toDateString(),
            'items' => [['product_id' => $product->id, 'quantity' => 10, 'unit_price' => '100']],
        ])->assertSessionHasNoErrors();
        $this->assertSame(0, $product->fresh()->stock);

        $this->delete("/invoices/{$invoice->id}")->assertRedirect('/invoices');
        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(0, StockMovement::where('source', 'invoice')->count());
    }

    public function test_grn_cannot_be_deleted_or_reduced_below_what_was_sold(): void
    {
        $product = Product::factory()->stock(0)->create(['code' => 'SH-9']);
        $grn = $this->receive($this->supplier, [[$product, 10, '40']]);
        $this->invoice($product, 7);

        $this->delete("/grns/{$grn->id}")->assertSessionHasErrors('grn');
        $this->assertModelExists($grn);

        $this->put("/grns/{$grn->id}", [
            'supplier_id' => $this->supplier->id, 'grn_date' => today()->toDateString(),
            'items' => [['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => '40']],
        ])->assertSessionHasErrors('items');
        $this->assertSame(3, $product->fresh()->stock);

        $this->put("/grns/{$grn->id}", [
            'supplier_id' => $this->supplier->id, 'grn_date' => today()->toDateString(),
            'items' => [['product_id' => $product->id, 'quantity' => 12, 'unit_cost' => '45']],
        ])->assertSessionHasNoErrors();
        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame('540.00', $grn->fresh()->total);
    }

    public function test_stock_adjustments_are_admin_only_and_never_go_negative(): void
    {
        $product = Product::factory()->stock(3)->create();

        $this->post('/stock/adjust', ['product_id' => $product->id, 'direction' => 'add', 'quantity' => 7, 'movement_date' => today()->toDateString(), 'note' => 'Opening stock'])
            ->assertRedirect("/stock/{$product->id}");
        $this->assertSame(10, $product->fresh()->stock);

        $this->post('/stock/adjust', ['product_id' => $product->id, 'direction' => 'remove', 'quantity' => 11, 'movement_date' => today()->toDateString(), 'note' => 'Damaged'])
            ->assertSessionHasErrors('quantity');
        $this->assertSame(10, $product->fresh()->stock);

        $this->actingAs($this->staff())
            ->post('/stock/adjust', ['product_id' => $product->id, 'direction' => 'add', 'quantity' => 1, 'movement_date' => today()->toDateString(), 'note' => 'x'])
            ->assertForbidden();
    }

    public function test_products_with_stock_history_cannot_be_deleted(): void
    {
        $product = Product::factory()->stock(0)->create();
        $this->receive($this->supplier, [[$product, 2, '10']]);

        $this->delete("/products/{$product->id}")->assertSessionHas('error');
        $this->assertModelExists($product);
    }

    public function test_stock_cannot_be_set_through_the_product_form(): void
    {
        $product = Product::factory()->stock(5)->create(['code' => 'SH-5']);

        $this->put("/products/{$product->id}", [
            'code' => 'SH-5', 'name' => 'X', 'cost' => '1', 'price' => '2', 'stock' => 9999, 'reorder_level' => 3, 'is_active' => '1',
        ])->assertRedirect('/products');

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(3, $product->fresh()->reorder_level);
    }
}
