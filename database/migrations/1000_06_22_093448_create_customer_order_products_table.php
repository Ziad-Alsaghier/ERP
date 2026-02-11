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
        Schema::create('customer_order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('customer_order_products_user_id_foreign');
            $table->unsignedBigInteger('order_id')->index('customer_order_products_order_id_foreign');
            $table->unsignedBigInteger('product_id');
            $table->string('name');
            $table->string('type');
            $table->string('sku');
            $table->string('category');
            $table->string('status');
            $table->string('price');
            $table->integer('count');
            $table->text('description');
            $table->mediumText('photo');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_order_products');
    }
};
