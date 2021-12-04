<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreColoumServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('services', function($table) {
        $table->enum('vat_status', ['taxable', 'shipping','none'])->default('none');
        $table->string('booking_block_duration')->nullable();
        $table->enum('booking_block_duration_unit', ['minute', 'hour','day','month'])->default('minute');
        $table->string('min_block_duration')->nullable();
        $table->string('max_block_duration')->nullable();
        $table->double('basic_cost',8,2)->nullable()->comment('regular price');
        $table->double('block_cost',8,2)->nullable();
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
         Schema::table('services', function($table) {
             $table->dropColumn('vat_status');
             $table->dropColumn('booking_block_duration');
             $table->dropColumn('booking_block_duration_unit');
             $table->dropColumn('min_block_duration');
             $table->dropColumn('max_block_duration');
             $table->dropColumn('basic_cost');
             $table->dropColumn('block_cost');
        });
    }
}
