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
        Schema::create('attribute_selects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attr_id');
            $table->string('value'); // Option value
            // Foreign key (recommended)
            $table->foreign('attr_id')
                ->references('id')
                ->on('attributes')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_selects');
    }
};
