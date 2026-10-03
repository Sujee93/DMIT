<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Optional customer/supplier code printed on invoices, e.g. "WP HOR 267".
            $table->string('code', 50)->nullable()->after('type');
            $table->index('code');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            // Per-line discount in percent; line_total is the amount after this discount.
            $table->decimal('discount_percent', 5, 2)->default(0)->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropColumn('code');
        });
    }
};
