<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeeServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
       //Schema::table('employee_service', function (Blueprint $table) {
          Schema::table('employee_service', function($table) {
            $table->double('price',8,2)->after('service_id')->nullable()->comment('regular price');
            $table->double('outtimeprice',8,2)->after('price')->nullable()->comment('evening/weekend price');
            $table->double('discount',8,2)->after('outtimeprice')->nullable();
            $table->double('tax_amount',8,2)->after('discount')->nullable();
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
    }
}
