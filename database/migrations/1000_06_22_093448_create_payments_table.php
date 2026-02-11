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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('payment_id');
            $table->date('date');
            $table->decimal('amount', 16)->default(0);
            $table->integer('account_id');
            $table->integer('chart_account_id')->default(0);
            $table->integer('vender_id');
            $table->text('description')->nullable();
            $table->integer('category_id');
            $table->string('recurring')->nullable();
            $table->integer('payment_method');
            $table->string('reference')->nullable();
            $table->string('add_receipt')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
