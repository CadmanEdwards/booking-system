<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Location extends Model
{
	 use SoftDeletes;
    //
     protected $fillable = ['location_name','location_description','location_address'];

    public function rooms()
    {
         return $this->belongsToMany('App\Room', 'rooms_locations', 
		      'location_id', 'room_id');
    }

}
