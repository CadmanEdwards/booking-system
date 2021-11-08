<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Employee
 *
 * @package App
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $email
*/
class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id','first_name', 'last_name', 'phone', 'email','location_id','moneybird_username','moneybird_key','registration_no','address','small_info',
    	'rcbz_no', 'agb_code', 'logo1', 'logo2', 'behandelaar'
    ];
     

	public function working_hours()
	{
		return $this->hasMany('App\WorkingHour', 'employee_id');
	}

	public function provides_service()
	{
		return $this->hasMany('App\EmployeeService', 'employee_id');
	}	
	
	public function is_working($date) {
		return $this->working_hours->where('date', '=', $date)->first();
	}
	
	public function service_info($service)
	{
		return $this->services()->where('service_id', $service)->first();
	}

	 public function user()
    {
       // return $this->belongsTo('App\User');
        return $this->belongsTo('App\User','user_id','id');
    }


    public function rooms()
    {
         return $this->belongsToMany('App\Room', 'employees_rooms', 
		      'employee_id', 'room_id','orders');
    }

	
}
