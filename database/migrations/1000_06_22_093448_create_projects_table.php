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
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('project_name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('project_image')->nullable();
            $table->integer('budget')->nullable();
            $table->integer('client_id');
            $table->text('description')->nullable();
            $table->string('status');
            $table->string('estimated_hrs')->nullable();
            $table->string('password')->nullable();
            $table->text('copylinksetting')->nullable();
            $table->text('tags')->nullable();
            $table->string('ref');
            $table->integer('ref_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
