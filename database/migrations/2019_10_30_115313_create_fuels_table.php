<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFuelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('litres');
            $table->integer('fuel_type_id');
            $table->string('currency');
            $table->integer('vehicle_id')->nullable();
            $table->string('vehicle_name')->nullable();
            $table->integer('supplier_id');
            $table->integer('driver_id')->nullable();
            $table->string('driver_name')->nullable();
            $table->integer('expense_id')->nullable();
            $table->date('fuel_on');
            $table->string('asset_type')->default('company');
            $table->float('current_km_readings')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('cash_sale_no')->nullable();
            $table->text('other_expenses')->nullable();
            $table->float('odometer_readings')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fuels');
    }
}
