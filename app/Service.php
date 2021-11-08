<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name','tax_amount','vat_status','booking_block_duration','booking_block_duration_unit','min_block_duration','max_block_duration','basic_cost','block_cost','description','description_second','tax_rate_id_moneybrid'];

	public function employees()
    {
        return $this->belongsToMany('App\Employee');
    }
    public function extra_cost()
    {
        return $this->hasMany('App\ServiceExtraCost');
    }
    public function employees_service()
    {
        return $this->belongsToMany('App\EmployeeService');
    }
}
