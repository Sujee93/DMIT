<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Units on hand. Only changed through StockService (GRN, invoice, adjustment).
            $table->integer('stock')->default(0)->after('price');
            $table->unsignedInteger('reorder_level')->default(0)->after('stock');
        });

        Schema::table('business_settings', function (Blueprint $table) {
            $table->unsignedInteger('grn_next_number')->default(1)->after('invoice_next_number');
        });

        // Goods Received Notes: stock coming in from a supplier, with the supplier's bill value.
        Schema::create('grns', function (Blueprint $table) {
            $table->id();
            $table->string('grn_no', 40)->unique();
            $table->foreignId('supplier_id')->constrained('contacts')->restrictOnDelete();
            $table->date('grn_date');
            $table->string('supplier_ref', 100)->nullable(); // supplier's invoice / bill number
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('grn_date');
        });

        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('product_code', 50);
            $table->string('product_name');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_cost', 14, 2);
            $table->decimal('line_total', 14, 2);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Every change to stock, so the on-hand figure can always be explained.
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('source', 20);                 // grn | invoice | adjustment
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('reference', 60)->nullable();  // GRN / invoice number
            $table->date('movement_date');
            $table->integer('quantity');                  // + in, - out
            $table->string('note', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['source', 'source_id']);
            $table->index(['product_id', 'movement_date']);
        });

        // Cheques received from customers. They can be deposited, given (endorsed) to a
        // supplier as payment, or returned by the bank.
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_payment_id')->nullable()->constrained('customer_payments')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('contacts')->restrictOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('cheque_no', 100);
            $table->string('bank', 100)->nullable();
            $table->date('cheque_date');
            $table->decimal('amount', 14, 2);
            $table->string('status', 20)->default('in_hand');
            $table->foreignId('supplier_payment_id')->nullable()->constrained('supplier_payments')->nullOnDelete();
            $table->date('deposited_on')->nullable();
            $table->date('returned_on')->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();

            $table->index(['status', 'cheque_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cheques');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('grn_items');
        Schema::dropIfExists('grns');

        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropColumn('grn_next_number');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'reorder_level']);
        });
    }
};
