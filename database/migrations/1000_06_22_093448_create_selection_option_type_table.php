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
        Schema::create('selection_option_type', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 255);
            $table->string('attr', 255);
            $table->string('status', 255)->default('active');
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selection_option_type');
    }
};
