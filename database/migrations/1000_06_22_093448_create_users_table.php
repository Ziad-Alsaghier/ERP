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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('type', 100)->nullable();
            $table->string('profile_image')->nullable();
            $table->string('mobile')->nullable();
            $table->string('register_type')->default('email');
            $table->string('is_assign_store')->nullable();
            $table->string('current_store')->nullable()->unique('current_store');
            $table->string('language')->default('en');
            $table->string('default_language')->default('en');
            $table->string('theme_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->integer('plan')->nullable();
            $table->date('plan_expire_date')->nullable();
            $table->integer('requested_plan')->default(0);
            $table->integer('trial_plan')->default(0);
            $table->date('trial_expire_date')->nullable();
            $table->double('storage_limit', 8, 2)->default(0);
            //$table->string('avatar')->default('avatar.png');
            //$table->string('messenger_color')->default('#2180f3');
            $table->string('lang', 100)->nullable();
            $table->integer('default_pipeline')->nullable();
            //$table->boolean('active_status')->default(false);
            $table->integer('delete_status')->default(1);
            $table->string('mode', 10)->default('light');
            //$table->boolean('dark_mode')->default(false);
            $table->integer('is_disable')->default(1);
            $table->integer('is_enable_login')->default(1);
            $table->integer('is_active')->default(1);
            $table->dateTime('last_login_at')->nullable();
            $table->integer('created_by')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
