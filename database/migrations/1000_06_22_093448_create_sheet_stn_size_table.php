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
        Schema::create('sheet_stn_size', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id')->index('delete sheet_stn_size id from product');
            $table->string('name', 255);
            $table->string('sheet_value', 255);
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheet_stn_size');
    }
};
