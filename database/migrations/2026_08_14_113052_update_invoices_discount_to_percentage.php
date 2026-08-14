<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Replaces the fixed-amount `discount` column (added moments ago, not yet
     * used on any real invoice) with a percentage-rate model matching the
     * existing vat_rate/vat_amount pattern: `discount_rate` is what the user
     * types (e.g. 5.00 for 5%), `discount_amount` is the computed Rs. value
     * stored alongside it so print/report views don't need to recompute it.
     */
    public function up(): void
    {
        if (Schema::hasColumn('invoices', 'discount')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('discount');
            });
        }

        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'discount_rate')) {
                $table->decimal('discount_rate', 5, 2)->default(0)->after('subtotal');
            }
            if (! Schema::hasColumn('invoices', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['discount_rate', 'discount_amount']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
        });
    }
};
