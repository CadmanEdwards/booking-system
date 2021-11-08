<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
    //
    protected $fillable = ['room_id','day','start_time','end_time','date'];
}
