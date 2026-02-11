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
        Schema::create('groupproduct', function (Blueprint $table) {
            $table->increments('id');
            $table->string('arch-id', 20);
            $table->string('name', 244)->nullable()->index('name');
            $table->string('maingroup', 255)->nullable()->index('maingroup');
            $table->integer('type')->nullable();
            $table->integer('status')->nullable();
            $table->string('note', 244)->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groupproduct');
    }
};
