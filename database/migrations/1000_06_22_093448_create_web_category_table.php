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
        Schema::create('web_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->text('meta_descriptions')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->tinyInteger('is_feature')->nullable()->default(1);
            $table->integer('serial')->default(0);
            $table->timestamps();
            $table->integer('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_category');
    }
};
