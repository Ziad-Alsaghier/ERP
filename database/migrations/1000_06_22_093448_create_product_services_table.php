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
        Schema::create('product_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('sku')->nullable();
            $table->decimal('sale_price', 16)->default(0);
            $table->decimal('purchase_price', 16)->default(0);
            $table->double('quantity', 8, 2)->default(0);
            $table->string('tax_id', 50)->nullable();
            $table->integer('category_id')->default(0);
            $table->integer('unit_id')->default(0);
            $table->string('type');
            $table->integer('web_category_id')->nullable();
            $table->integer('web_subcategory_id')->nullable();
            $table->integer('web_childcategory_id')->nullable();
            $table->integer('sale_chartaccount_id')->default(0);
            $table->integer('expense_chartaccount_id')->default(0);
            $table->text('description')->nullable();
            $table->string('pro_image')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_services');
    }
};
