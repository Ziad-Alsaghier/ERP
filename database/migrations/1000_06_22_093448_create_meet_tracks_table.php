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
        Schema::create('meet_tracks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('job_title', 255)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->text('company_activites')->nullable();
            $table->string('mobile', 100);
            $table->string('email', 100)->nullable();
            $table->text('tech_solutions')->nullable();
            $table->enum('interest_level', ['high', 'medium', 'low'])->nullable();
            $table->unsignedTinyInteger('demo_request')->nullable()->default(0);
            $table->string('contact_time', 255)->nullable();
            $table->enum('contact_method', ['email', 'phone', 'whatsapp'])->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meet_tracks');
    }
};
