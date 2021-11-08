<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EmployeeService
 *
 * @package App
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $email
*/
class EmployeeService extends Model
{
    use SoftDeletes;

    protected $fillable = ['employee_id','service_id','discount','moneybird_username'];
    
   
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function services()
    {
         return $this->belongsTo(Service::class, 'service_id'); 
    }
    
	public function provides_service($service)
	{
		
		return $this->services()->where('service_id', $service)->exists();
	}
	 	
	public function service_info($service)
	{
		return $this->services()->where('service_id', $service)->first();
	}
	
}
