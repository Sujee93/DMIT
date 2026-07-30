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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable()->unique()->comment('Stable code for fixed customers, e.g. singer, arpico');
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('telephone')->nullable();
            $table->boolean('is_fixed')->default(false)->comment('Preset customer used on the Tax Invoice template');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
