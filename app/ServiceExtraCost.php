<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceExtraCost extends Model
{
    //
    protected $fillable = ['service_id', 'booking_basic_pricing', 'booking_block_pricing', 'booking_series_type','booking_block_cost_duration_type_unit','booking_basic_cost_duration_type_unit','booking_pricing_time_from','booking_pricing_time_to','created_at', 'updated_at'];

    protected $table = "service_extra_cost";
}
