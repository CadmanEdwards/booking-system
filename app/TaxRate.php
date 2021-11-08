<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    //

     protected $fillable = ['moneybird_tax_id', 'name', 'percentage', 'tax_rate_type', 'show_tax','active'];
}
