<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Line discounts are now a rupee amount for the whole line instead of a percentage.
 * Existing lines are converted so their totals stay exactly the same.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('discount', 14, 2)->default(0)->after('unit_price');
        });

        // discount = gross (qty x price) - stored line total
        DB::table('invoice_items')->update([
            'discount' => DB::raw('(quantity * unit_price) - line_total'),
        ]);

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('discount_percent', 5, 2)->default(0)->after('unit_price');
        });

        DB::table('invoice_items')
            ->where('quantity', '>', 0)
            ->where('unit_price', '>', 0)
            ->update(['discount_percent' => DB::raw('ROUND(discount * 100 / (quantity * unit_price), 2)')]);

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};
