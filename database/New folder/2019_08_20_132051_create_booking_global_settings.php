<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingGlobalSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if(! Schema::hasTable('bookingsettings')) {
            Schema::create('bookingsettings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('booking_time_from')->nullable();
                $table->string('booking_time_to')->nullable();
                $table->enum('booking_series_type', ['time', 'eve_time','days','week_days'])->default('time'); 
                $table->timestamps();
                
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
         Schema::dropIfExists('bookingsettings');
    }
}
