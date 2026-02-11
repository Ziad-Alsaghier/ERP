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
        Schema::create('sheet_storage_product', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id')->index('cass');
            $table->string('table_id_cost', 100)->index('caasss');
            $table->unsignedInteger('storage_product_amount_name')->index('sheetnameinstore');
            $table->string('storage_product_amount', 255);
            $table->string('storage_product_amount_for', 255);
            $table->string('storage_cost_start_amount', 255);
            $table->string('storage_cost_start_amount_price', 255);
            $table->string('storage_cost_start_amount_growler', 255);
            $table->string('storage_cost_freq_amount', 255);
            $table->string('storage_cost_freq_amount_price', 255);
            $table->string('storage_cost_freq_anount_growler', 255);
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheet_storage_product');
    }
};
