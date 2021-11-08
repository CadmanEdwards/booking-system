<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Page extends Model
{
    //
    use SoftDeletes;
    protected $fillable = ['page_content','page_slug','page_subject'];
   
}
