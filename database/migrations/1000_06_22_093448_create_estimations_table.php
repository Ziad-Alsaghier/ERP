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
        Schema::create('estimations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('estimation_id');
            $table->unsignedBigInteger('client_id');
            $table->string('status');
            $table->date('issue_date');
            $table->double('discount', 8, 2);
            $table->unsignedBigInteger('tax_id');
            $table->text('terms')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimations');
    }
};
