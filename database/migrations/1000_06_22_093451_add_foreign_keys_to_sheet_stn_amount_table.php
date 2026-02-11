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
        Schema::table('sheet_stn_amount', function (Blueprint $table) {
            $table->foreign(['product_id'], 'sheet_stn_amount')->references(['id'])->on('product')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sheet_stn_amount', function (Blueprint $table) {
            $table->dropForeign('sheet_stn_amount');
        });
    }
};
