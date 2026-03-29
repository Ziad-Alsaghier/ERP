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
        Schema::create('attribute_select_langs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attribute_select_id')
                ->constrained('attribute_selects')
                ->cascadeOnDelete();

            $table->string('lang', 10);

            $table->string('value');

            $table->unique(['attribute_select_id', 'lang']);

            $table->index('lang');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_select_langs');
    }
};
