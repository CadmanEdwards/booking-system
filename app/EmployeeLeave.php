<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    //
       protected $fillable = ['employee_id','leave_reason_id','leave_date','leave_comment','leave_title','parent_id','leave_to_date','time_type'];
}
