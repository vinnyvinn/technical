<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExternalServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('job_card_id');
            $table->integer('service_provider_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->string('service_type');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('description');
            $table->string('item')->nullable();
            $table->float('cost')->nullable();
            $table->integer('quantity')->nullable();
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
        Schema::dropIfExists('external_services');
    }
}
