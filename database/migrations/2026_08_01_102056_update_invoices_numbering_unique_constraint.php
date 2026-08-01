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
        Schema::table('invoices', function (Blueprint $table) {
            // Tax invoices are now numbered per customer (Singer and Arpico each restart
            // at 1), so the old (type, number) pair alone can no longer guarantee uniqueness.
            $table->dropUnique('invoices_invoice_type_invoice_number_unique');
            $table->unique(['invoice_type', 'customer_id', 'invoice_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['invoice_type', 'customer_id', 'invoice_number']);
            $table->unique(['invoice_type', 'invoice_number']);
        });
    }
};
