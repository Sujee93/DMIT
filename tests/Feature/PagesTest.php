<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke test: every main screen renders for an administrator.
 */
class PagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_main_pages_render(): void
    {
        $this->actingAs($this->admin());
        $customer = Contact::factory()->customer()->create();
        $supplier = Contact::factory()->supplier()->create();
        $product = Product::factory()->create();

        $invoice = $this->post('/invoices', [
            'customer_id' => $customer->id,
            'supplier_id' => $supplier->id,
            'invoice_date' => now()->subDays(40)->toDateString(),
            'due_date' => now()->subDays(10)->toDateString(),
            'items' => [['product_id' => $product->id, 'quantity' => 3, 'unit_price' => '100.00']],
        ]);
        $invoice->assertRedirect();
        $invoiceUrl = $invoice->headers->get('Location');

        $urls = [
            '/dashboard', '/products', '/products/create', "/products/{$product->id}/edit",
            '/contacts', '/contacts?type=customer', '/contacts/create?type=supplier', "/contacts/{$customer->id}",
            "/contacts/{$supplier->id}", "/contacts/{$customer->id}/edit",
            '/invoices', '/invoices?status=overdue', '/invoices/create', $invoiceUrl, $invoiceUrl.'/edit', $invoiceUrl.'/print',
            '/supplier-payments', '/supplier-payments/create', "/supplier-payments/create?supplier_id={$supplier->id}",
            '/reports', '/reports/sales', '/reports/receivables', '/reports/payables', '/reports/dues',
            '/settings', '/users', '/users/create',
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }
}
