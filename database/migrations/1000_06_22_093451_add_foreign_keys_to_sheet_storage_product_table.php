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
        Schema::table('sheet_storage_product', function (Blueprint $table) {
            $table->foreign(['product_id'], 'delete storage product')->references(['id'])->on('product')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['storage_product_amount_name'], 'sheetnameinstore')->references(['id'])->on('storages_cal')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sheet_storage_product', function (Blueprint $table) {
            $table->dropForeign('delete storage product');
            $table->dropForeign('sheetnameinstore');
        });
    }
};
