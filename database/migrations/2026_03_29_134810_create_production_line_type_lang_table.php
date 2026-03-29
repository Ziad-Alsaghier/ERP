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
        Schema::create('production_line_type_lang', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('type_id');
            $table->string('lang', 10);
            $table->string('name');


            $table->foreign('type_id')
                ->references('id')
                ->on('production_line_type')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_line_type_lang');
    }
};
