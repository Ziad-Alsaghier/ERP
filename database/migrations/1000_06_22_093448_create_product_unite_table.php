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
        Schema::create('product_unite', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id')->index('product unite delete and uptade');
            $table->string('unite_name', 255);
            $table->string('unite_type', 255);
            $table->string('min_order', 255);
            $table->string('max_order', 255);
            $table->string('unit_prof_margin', 255);
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_unite');
    }
};
