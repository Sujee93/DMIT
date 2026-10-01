<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('address', 500)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('registration_no', 100)->nullable();
            $table->string('tax_no', 100)->nullable();
            $table->string('currency_symbol', 10)->default('Rs.');
            $table->string('invoice_prefix', 20)->default('INV-');
            $table->unsignedInteger('invoice_next_number')->default(1);
            $table->unsignedSmallInteger('default_due_days')->default(30);
            $table->text('bank_details')->nullable();
            $table->text('invoice_terms')->nullable();
            $table->string('invoice_footer', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
