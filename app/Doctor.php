<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Doctor extends Model
{
	 use SoftDeletes;
    //

	 protected $fillable = [ 'first_name', 'last_name', 'house_number', 'address' ,'phone', 'dob', 'email','add_by','comment','status','postcode','location_id','company_name','city_name'];

    
}
