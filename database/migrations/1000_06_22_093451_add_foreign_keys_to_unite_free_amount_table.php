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
        Schema::table('unite_free_amount', function (Blueprint $table) {
            $table->foreign(['product_id'], 'unite_free_amount')->references(['id'])->on('product')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unite_free_amount', function (Blueprint $table) {
            $table->dropForeign('unite_free_amount');
        });
    }
};
