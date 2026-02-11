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
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('reference_number', 255)->unique('reference_number');
            $table->string('english_name', 255);
            $table->string('arabic_name', 255);
            $table->boolean('is_depreciable')->nullable()->default(false);
            $table->enum('depreciation_method', ['Straight-line method', 'Reducing balance method', 'Units of production method', 'Sum of years digits method'])->nullable();
            $table->integer('useful_life')->nullable();
            $table->enum('useful_life_unit', ['years', 'percent'])->nullable()->default('years');
            $table->integer('asset_account');
            $table->integer('depreciation_expense_account')->nullable();
            $table->integer('accumulated_depreciation_account')->nullable();
            $table->boolean('manual_depreciation')->nullable()->default(false);
            $table->boolean('recorded_depreciation')->nullable()->default(false);
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
