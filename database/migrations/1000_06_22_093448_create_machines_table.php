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
        Schema::create('machines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_REQ_UNQ', 255);
            $table->string('address', 255)->nullable();
            $table->string('country_origin_HIDE', 255);
            $table->string('version_HIDE', 255);
            $table->string('expiry_date_HIDE', 255);
            $table->string('fix_date', 255);
            $table->string('space_x_num', 255);
            $table->string('space_y_num', 255);
            $table->string('space_sheet_num', 255);
            $table->string('status', 255)->default('active');
            $table->string('description_HIDE', 255)->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
