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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('customer_id');
            $table->dateTime('issue_date');
            $table->dateTime('due_date');
            $table->date('send_date')->nullable();
            $table->integer('category_id');
            $table->integer('order_id')->nullable();
            $table->text('invoice_details');
            $table->integer('project');
            $table->integer('project_id');
            $table->text('ref_number')->nullable();
            $table->integer('status')->default(0);
            $table->longText('address');
            $table->integer('shipping_display')->default(1);
            $table->integer('discount_apply')->default(0);
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
