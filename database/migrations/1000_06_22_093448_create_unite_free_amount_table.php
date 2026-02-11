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
        Schema::create('unite_free_amount', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id')->index('unite_free_amount');
            $table->string('from_amount', 255);
            $table->string('to_amount', 255);
            $table->string('price_per_amount', 255);
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unite_free_amount');
    }
};
