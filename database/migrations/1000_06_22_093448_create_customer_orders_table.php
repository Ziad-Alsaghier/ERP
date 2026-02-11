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
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('customer_orders_user_id_foreign');
            $table->unsignedBigInteger('order_id');
            $table->string('status');
            $table->json('profile_details');
            $table->json('shipping_details');
            $table->json('billing_details');
            $table->json('payment_details');
            $table->json('quotation_details')->nullable();
            $table->json('invoice_details')->nullable();
            $table->json('payout_details')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_orders');
    }
};
