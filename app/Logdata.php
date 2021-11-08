<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logdata extends Model
{
     protected $fillable = ['log_from','refrance_id','log_datetime','tr_by','message'];

}
