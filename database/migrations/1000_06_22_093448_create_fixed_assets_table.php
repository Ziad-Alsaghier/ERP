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
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('arabic_name', 255);
            $table->string('english_name', 255);
            $table->string('reference_number', 100);
            $table->string('category', 100);
            $table->text('description')->nullable();
            $table->string('measurement_unit', 50);
            $table->integer('tax_percentage');
            $table->string('barcode', 100)->nullable();
            $table->string('asset_image', 255)->nullable();
            $table->integer('created_by');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
