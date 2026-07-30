<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // 'tax' = Katmo Interiors TAX INVOICE template (fixed customers: Singer, Arpico)
            // 'general' = plain invoice template with editable customer details, for any customer
            $table->enum('invoice_type', ['tax', 'general']);
            $table->string('invoice_number');
            $table->string('reference_number')->nullable()->comment('e.g. KATMO-ARP-008, printed as Tax Invoice No.');

            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->text('customer_address')->nullable();
            $table->string('customer_tin')->nullable();
            $table->string('customer_telephone')->nullable();

            $table->date('date_of_invoice');
            $table->date('date_of_supply')->nullable();
            $table->string('place_of_supply')->nullable();

            $table->string('vehicle_no')->nullable();
            $table->string('sup_no')->nullable();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->nullable();
            $table->decimal('vat_amount', 12, 2)->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('advance', 12, 2)->nullable();
            $table->decimal('balance', 12, 2)->nullable();

            $table->string('amount_in_words')->nullable();
            $table->string('mode_of_payment')->nullable();

            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('status', ['draft', 'finalized'])->default('draft');

            $table->timestamps();

            $table->unique(['invoice_type', 'invoice_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
