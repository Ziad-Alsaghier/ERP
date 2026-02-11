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
        Schema::create('customer_billing_address', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->index('customer_billing_address_user_id_index');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company_name');
            $table->string('email');
            $table->string('contact_num');
            $table->string('country');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code')->nullable();
            $table->string('address');
            $table->string('vat_num');
            $table->string('tax_num');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_billing_address');
    }
};
