<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB;
class CalandarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($locationid=0)
    {
         $user = \Auth::user();
       $clinet = App\Client::where('deleted_at','=',NULL)->where('add_by','=',$user->role_id)->count();
        $therapist = App\Employee::where('deleted_at','=',NULL)->get();
       $rooms = App\Room::select('id','room_name as title')->get(); 
      
      if($user->role_id==3)
      {
         $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get(); 
         $employee_id = $therapist[0]->id;  
         $appointments = App\Appointment::where('deleted_at','=',NULL)->where('employee_id','=',$employee_id)->count(); 
        $data['locations'] = \App\Location::get()->pluck('location_name', 'id')->prepend('Select Location', 0);
        $today_appointments = App\Appointment::whereDate('start_time','=',Carbon::today())->where('employee_id','=',$employee_id)->count(); 
      }
      else if ($user->role_id == 1 || $user->role_id == 2 ) {
        $appointments = App\Appointment::where('deleted_at','=',NULL)->count(); 
        $today_appointments = App\Appointment::wheredate('start_time','=','CURDATE()')->count(); 
        $data['locations'] = \App\Location::get()->pluck('location_name', 'id')->prepend('Select Location', 0);
        $data['services'] = \App\Service::get()->pluck('name', 'id')->prepend('Select Service', 0);
      }
      else
      {
         $clients = App\Client::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get(); 
         
         $client_id = $clients[0]->id;  

         $appointments = App\Appointment::where('deleted_at','=',NULL)->where('client_id','=',$client_id)->count();

         $todays_appointments = App\Appointment::where('deleted_at','=',NULL)->where('client_id','=',$client_id)->count();
         
      }

        if(isset($_GET['therapist_id'])){
            $url = url("admin/get-all-appointments").'?therapist_id='.$_GET['therapist_id'];

        }else{
            $url = url("admin/get-all-appointments");
        }
       $data['appointments'] = $appointments;
       $data['today_appointment'] = $today_appointments;
       $data['clinet'] = $clinet;
        $data['therapist_arr'] = $therapist;
       $data['rooms'] =  $rooms->toJson(); 
       $data['location_id'] = $locationid;
        $data['therapist'] = count($therapist);
        $data['url'] = $url;
       //role =1 admin,role=2 sub admin, role=3 Therapist, role = 4 customer
     /*  if($user->role_id == 1 || $user->role_id == 2)
         {return view('home',$data);}
       else if($user->role_id == 3)
         {return view('employee_home',$data);} */

         //return view('home',$data);
         return view('admin.calandar.index', $data);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
