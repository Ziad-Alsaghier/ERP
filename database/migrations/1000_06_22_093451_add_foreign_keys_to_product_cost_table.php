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
        Schema::table('product_cost', function (Blueprint $table) {
            $table->foreign(['product_id'], 'delete machin cost')->references(['id'])->on('product')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['machine_id'], 'machn cascade')->references(['id'])->on('machines')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['KindOfSheet'], 'sheetcascade')->references(['id'])->on('storages_cal')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_cost', function (Blueprint $table) {
            $table->dropForeign('delete machin cost');
            $table->dropForeign('machn cascade');
            $table->dropForeign('sheetcascade');
        });
    }
};
