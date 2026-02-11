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
        Schema::table('sheet_stn_size', function (Blueprint $table) {
            $table->foreign(['product_id'], 'delete sheet_stn_size id from product')->references(['id'])->on('product')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sheet_stn_size', function (Blueprint $table) {
            $table->dropForeign('delete sheet_stn_size id from product');
        });
    }
};
