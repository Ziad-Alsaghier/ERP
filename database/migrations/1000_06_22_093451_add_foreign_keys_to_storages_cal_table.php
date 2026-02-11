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
        Schema::table('storages_cal', function (Blueprint $table) {
            $table->foreign(['storages_id'], 'cascadestorage')->references(['id'])->on('storages')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storages_cal', function (Blueprint $table) {
            $table->dropForeign('cascadestorage');
        });
    }
};
