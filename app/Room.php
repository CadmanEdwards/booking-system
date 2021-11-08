<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Room extends Model
{
    //
    use SoftDeletes;
    protected $fillable = ['room_name'];
    public function rooms_availabilities()
    {
         return $this->belongsToMany(RoomAvailability::class, 'room_id'); 
    }
}
