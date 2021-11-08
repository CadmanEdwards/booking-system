<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceExtraCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
          Schema::create('service_extra_cost', function (Blueprint $table) {
            $table->increments('extra_cost_id');
            $table->integer('service_id')->unsigned();
            $table->foreign('service_id')->references('id')->on('services');
            $table->string('booking_pricing_from')->nullable();
            $table->string('booking_pricing_to')->nullable();
            $table->enum('booking_series_type', ['time', 'eve_time','days','week_days'])->default('time'); 
           $table->enum('booking_block_cost_duration_type_unit', ['minus', 'plus','divide','times'])->default('plus');
           $table->enum('booking_basic_cost_duration_type_unit', ['minus', 'plus','divide','times'])->default('plus');
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
        //
         Schema::dropIfExists('service_extra_cost');
    }
}
