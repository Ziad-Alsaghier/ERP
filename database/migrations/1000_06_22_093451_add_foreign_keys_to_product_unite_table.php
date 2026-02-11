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
        Schema::table('product_unite', function (Blueprint $table) {
            $table->foreign(['product_id'], 'product unite delete and uptade')->references(['id'])->on('product')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_unite', function (Blueprint $table) {
            $table->dropForeign('product unite delete and uptade');
        });
    }
};
