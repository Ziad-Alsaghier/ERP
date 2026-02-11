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
        Schema::create('sheet_stn_amount', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id')->index('sheet_stn_amount');
            $table->string('name', 255);
            $table->integer('sheet_amount');
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheet_stn_amount');
    }
};
