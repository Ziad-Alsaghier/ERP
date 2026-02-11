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
        Schema::create('product_cost', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id')->index('product_id');
            $table->unsignedInteger('machine_id')->index('machine_id');
            $table->unsignedInteger('table_id')->index('table_id');
            $table->unsignedInteger('KindOfSheet')->index('kindofsheet');
            $table->string('start_amount_cost', 100);
            $table->string('start_amount_growler', 100);
            $table->string('Freq_amount', 100);
            $table->string('Freq_amount_growler', 100);
            $table->string('hours_cost', 100);
            $table->string('prcent_cost', 100);
            $table->string('Profit_margin', 100);
            $table->integer('user_id');

            $table->index(['product_id'], 'product_id_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_cost');
    }
};
