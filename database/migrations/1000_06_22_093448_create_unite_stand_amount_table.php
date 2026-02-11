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
        Schema::create('unite_stand_amount', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id')->index('unite_stand_amount');
            $table->string('name_amount', 255);
            $table->string('value_amount', 255);
            $table->string('price_per_amount', 255);
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unite_stand_amount');
    }
};
