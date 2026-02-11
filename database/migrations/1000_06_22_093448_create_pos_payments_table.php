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
        Schema::create('pos_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pos_id');
            $table->date('date')->nullable();
            $table->decimal('amount', 15)->default(0);
            $table->decimal('discount', 15)->nullable()->default(0);
            $table->decimal('discount_amount', 15)->nullable()->default(0);
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_payments');
    }
};
