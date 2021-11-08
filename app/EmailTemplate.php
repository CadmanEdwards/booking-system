<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    //
     protected $fillable = ['email_type','email_content','email_content_h','email_subject','email_user_type','email_id','attachment'];
}
