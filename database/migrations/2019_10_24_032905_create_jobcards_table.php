<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobcardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobcards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('machine_id');
            $table->integer('track_by_id');
            $table->integer('service_provider_type_id');
            $table->string('service_provider_id')->nullable();
            $table->text('item_cost_qty')->nullable();
            $table->string('labour')->nullable();
            $table->string('cost')->nullable();
            $table->string('supplier_id')->nullable();
            $table->integer('service_type_id')->nullable();
            $table->string('service_type')->nullable();
            $table->float('next_readings')->nullable()->default(0);
            $table->float('current_readings')->nullable()->default(0);
            $table->date('next_service_date')->nullable();
            $table->integer('fuel_balance_id')->nullable();
            $table->date('actual_date');
            $table->date('completion_date');
            $table->string('checklist_file')->nullable();
            $table->string('service_type_description')->nullable();
            $table->time('time_in');
            $table->time('time_out');
            $table->text('maintenance')->nullable();
            $table->string('card_no')->nullable();
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
        Schema::dropIfExists('jobcards');
    }
}
