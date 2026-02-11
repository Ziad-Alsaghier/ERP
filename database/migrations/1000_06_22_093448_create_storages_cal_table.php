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
        Schema::create('storages_cal', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('storages_id')->index('storages_id');
            $table->string('attach_1', 244)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('group_pro', 244)->nullable();
            $table->string('sub_group_pro', 244)->nullable();
            $table->string('child_sub_group', 255)->nullable();
            $table->string('unite_amount', 244)->nullable();
            $table->string('amount_per_unite', 244)->nullable();
            $table->string('minimum_order', 244)->nullable();
            $table->string('maximum_order', 244)->nullable();
            $table->string('step_order', 244)->nullable();
            $table->string('alert_amount', 244)->nullable();
            $table->string('unite_name', 244)->nullable();
            $table->string('msu', 244)->nullable();
            $table->string('msu_unite', 244)->nullable();
            $table->string('purchase_price', 244)->nullable();
            $table->string('selling_price', 244)->nullable();
            $table->string('profit_prec', 244)->nullable();
            $table->string('discount_prec', 244)->nullable();
            $table->string('suppliers', 244)->nullable();
            $table->string('supply_repres', 244)->nullable();
            $table->string('supply_date', 244)->nullable();
            $table->string('sku_supply', 244)->nullable();
            $table->string('international_num', 244)->nullable();
            $table->string('production_date', 244)->nullable();
            $table->string('expiry_date', 244)->nullable();
            $table->string('barcode', 244)->nullable();
            $table->string('tax_type', 244)->nullable();
            $table->string('batch_number', 244)->nullable();
            $table->string('serial_number', 244)->nullable();
            $table->string('active', 244)->nullable();
            $table->string('visibale', 244)->nullable();
            $table->string('manufacturing', 244)->nullable();
            $table->string('pos', 244)->nullable();
            $table->string('web', 244)->nullable();
            $table->text('info_data')->nullable();
            $table->string('info_title', 244)->nullable();
            $table->string('description', 244)->nullable();
            $table->string('image', 244)->nullable();
            $table->string('attch_1', 244)->nullable();
            $table->string('attch_2', 244)->nullable();
            $table->string('equation', 244)->nullable();
            $table->string('manufacturing_machine', 244);
            $table->string('height', 244)->nullable();
            $table->string('width', 244)->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storages_cal');
    }
};
