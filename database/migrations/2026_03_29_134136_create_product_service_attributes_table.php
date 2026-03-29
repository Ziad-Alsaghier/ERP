<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_service_attributes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('attr_id');

            $table->text('attr_value')->nullable();
            $table->string('value_mode')->nullable(); // e.g: static/dynamic/manual
            $table->boolean('is_dynamic')->default(0);


            // Foreign keys (optional but recommended)
            $table->foreign('product_id')
                ->references('id')
                ->on('product_services')
                ->onDelete('cascade');

            $table->foreign('attr_id')
                ->references('id')
                ->on('attributes')
                ->onDelete('cascade');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_service_attributes');
    }
};
