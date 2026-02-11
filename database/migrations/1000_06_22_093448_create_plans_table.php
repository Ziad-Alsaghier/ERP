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
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->decimal('price', 30)->nullable()->default(0);
            $table->string('duration', 100);
            $table->integer('max_users')->default(0);
            $table->integer('max_customers')->default(0);
            $table->integer('max_venders')->default(0);
            $table->integer('max_clients')->default(0);
            $table->decimal('user_price', 10, 0)->nullable();
            $table->decimal('customers_price', 10, 0)->nullable();
            $table->decimal('storage_price', 10, 0)->nullable();
            $table->decimal('venders_price', 10, 0)->nullable();
            $table->decimal('clients_price', 10, 0)->nullable();
            $table->integer('trial')->default(0);
            $table->integer('trial_days')->nullable();
            $table->integer('is_disable')->default(0);
            $table->integer('is_visible')->default(0);
            $table->double('storage_limit', 8, 2)->default(0);
            $table->integer('manfuc');
            $table->integer('chatgpt')->default(0);
            $table->integer('crm')->default(0);
            $table->integer('hrm')->default(0);
            $table->integer('account')->default(0);
            $table->integer('project')->default(0);
            $table->integer('pos')->default(0);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
