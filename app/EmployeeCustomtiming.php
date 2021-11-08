<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeCustomtiming extends Model
{
    //
    protected $fillable = ['employee_id','date','start_time','end_time','days','location_id','timing_type'];
}