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
        Schema::create('product', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 244)->nullable();
            $table->string('sku', 255)->nullable();
            $table->unsignedInteger('categorie')->index('categorie');
            $table->integer('visiblity')->nullable();
            $table->boolean('active')->nullable();
            $table->integer('new')->nullable();
            $table->integer('offer')->nullable();
            $table->integer('designable')->nullable();
            $table->string('design_price', 255)->nullable();
            $table->integer('first_precent')->nullable();
            $table->integer('tax_amount')->nullable();
            $table->text('description')->nullable();
            $table->string('pricingtype', 255)->nullable();
            $table->text('product_script')->nullable();
            $table->string('img', 255)->nullable();
            $table->string('img1', 255)->nullable();
            $table->string('img2', 255)->nullable();
            $table->string('img3', 255)->nullable();
            $table->boolean('free_size')->default(true);
            $table->boolean('free_amount')->default(true);
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
