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
        Schema::create('proposals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('proposal_id');
            $table->unsignedBigInteger('customer_id');
            $table->dateTime('issue_date');
            $table->dateTime('due_date');
            $table->dateTime('send_date')->nullable();
            $table->integer('category_id');
            $table->text('proposal_details')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('project');
            $table->integer('project_id');
            $table->integer('status')->default(0);
            $table->string('type');
            $table->integer('discount_apply')->default(0);
            $table->longText('address');
            $table->integer('is_convert')->default(0);
            $table->integer('converted_invoice_id')->default(0);
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
