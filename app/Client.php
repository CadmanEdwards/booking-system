<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Client
 *
 * @package App
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $email
*/
class Client extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'first_name', 'last_name', 'postcode', 'house_number', 'address' ,'phone', 'dob', 'email','email_invoice','location_id','moneybird_contact_id','add_by','parent_id','company_name','city_name','comment','comment_log','status','verify_link','email_verified','doctor_id','probleem','bereiken','waarnemingen','procediagnose',
    'fysieke','samenvatting'];
}
