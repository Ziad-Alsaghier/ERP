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
        Schema::create('pos_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pos_id')->default(0);
            $table->integer('product_id')->default(0);
            $table->integer('quantity')->default(0);
            $table->string('tax')->default('0.00');
            $table->double('discount', 8, 2)->nullable()->default(0);
            $table->decimal('price', 15)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_products');
    }
};
