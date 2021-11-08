<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use \App;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($locationid=0)
    {
       $user = \Auth::user();

       $clinet = App\Client::where('deleted_at','=',NULL)->where('add_by','=',$user->role_id)->count();   
       $therapist = App\Employee::where('deleted_at','=',NULL)->count(); 
       $rooms = App\Room::select('id','room_name as title')->get(); 
      //echo $user->role_id;exit; 
      if($user->role_id==3)
      {
         
         $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get(); 
          
         $employee_id = $therapist[0]->id;  

         $appointments = App\Appointment::where('deleted_at','=',NULL)->where('employee_id','=',$employee_id)->count(); 
        $data['locations'] = \App\Location::get()->pluck('location_name', 'id')->prepend('Select Location', 0);
        $today_appointments = App\Appointment::whereDate('start_time','=',Carbon::today())->where('employee_id','=',$employee_id)->count(); 

        $gatAlltherapist = App\Employee::where('deleted_at','=',NULL)->get();

      }
      else if ($user->role_id == 1 || $user->role_id == 2 ) {
        $appointments = App\Appointment::where('deleted_at','=',NULL)->count(); 
        $today_appointments = App\Appointment::wheredate('start_time','=','CURDATE()')->count(); 
        $data['locations'] = \App\Location::get()->pluck('location_name', 'id')->prepend('Select Location', 0);
        $data['services'] = \App\Service::get()->pluck('name', 'id')->prepend('Select Service', 0);
        $gatAlltherapist = App\Employee::where('deleted_at','=',NULL)->get();
      }
      else
      {
         $clients = App\Client::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get(); 
         $gatAlltherapist = App\Employee::where('deleted_at','=',NULL)->get();
         $client_id = $clients[0]->id;  

         $appointments = App\Appointment::where('deleted_at','=',NULL)->where('client_id','=',$client_id)->count();

         $todays_appointments = App\Appointment::where('deleted_at','=',NULL)->where('client_id','=',$client_id)->count();
         
      }

       $data['appointments'] = $appointments;
       $data['today_appointment'] = isset($today_appointments) ? $today_appointments : 0 ;
       $data['clinet'] = $clinet;
       $data['therapist'] = $therapist;
       $data['getAlltherapist'] = $gatAlltherapist;
       //dd(count($data['getAlltherapist']));
       $data['rooms'] =  $rooms->toJson(); 
       $data['location_id'] = $locationid;
       
       //role =1 admin,role=2 sub admin, role=3 Therapist, role = 4 customer
       if($user->role_id == 1 || $user->role_id == 2)
         {return view('home',$data);}
       else if($user->role_id == 3)
         {return view('employee_home',$data);} 
    }
    public function EmplyoeeResourceJson()
    {
       $therapistlist = App\Employee::select('id', 'first_name as title','eventColor','small_info as description')->get(); 
      // $therapistlist->put('eventColor', 'green');
      
       $therapistlist = json_encode($therapistlist);
       $data['therapistlist'] = $therapistlist;
       return $data['therapistlist']; 
    }
    public function EmplyoeeTimeResourceJson(Request $request)
    {
       $therapistlist = App\Employee::select('id','first_name as title')->get(); 
       $JsonResource=array();
       //dd($request->all());
       
      $startDateArray =  explode("T", $request->start);
      $endDateArray =  explode("T", $request->end);
      $serviceId =   $request->service_id;
      $locationId =   $request->location_id;
      $from = $startDateArray[0];
      $end = $endDateArray[0];
      foreach($therapistlist as $therapistlist)
          {
            //echo $from;
            //whereBetween('reservation_from', [$from, $to])
            /*echo "ID ".$therapistlist->id."<br>";
            echo "Form ".$from."<br>";
            echo "End ".$end."<br>";*/
            if($serviceId > 0) 
            {
               if($locationId > 0)
              {
                  $workinglist = App\WorkingHour::join('employee_services', 'employee_services.employee_id', '=', 'working_hours.employee_id')->select('start_time','finish_time','date')->where('working_hours.employee_id','=',$therapistlist->id)->where('employee_services.service_id','=',$serviceId)->whereBetween('working_hours.date', [$from, $from])->whereIn('working_hours.location_id',['0',$locationId])->get();
              } 
              else
              {
                   $workinglist = App\WorkingHour::join('employee_services', 'employee_services.employee_id', '=', 'working_hours.employee_id')->select('start_time','finish_time','date')->where('working_hours.employee_id','=',$therapistlist->id)->where('employee_services.service_id','=',$serviceId)->whereBetween('working_hours.date', [$from, $from])->get();

              }
              /*echo "<pre>";print_r($workinglist);                       
              exit;*/
            }
            else
            {
             if($locationId > 0)
              {
               $workinglist = App\WorkingHour::select('start_time','finish_time','date')->where('employee_id','=',$therapistlist->id)->whereBetween('date', [$from, $from])->whereIn('working_hours.location_id',['0',$locationId])->get();   
              }
              else
              {
               /* echo "Form Date ".$from;
                echo "<br>";
                echo "End Date ".$end;
                echo "<br>";
                echo $therapistlist->id;*/
                
                 $workinglist = App\WorkingHour::select('start_time','finish_time','date')->where('employee_id','=',$therapistlist->id)->whereBetween('date', [$from, $from])->get(); 
                if($therapistlist->id==21)
                   { 
                    //echo "<pre>";print_r($workinglist);
                    

                   }

              }
            }
          
             $customizeworkinglists = App\EmployeeCustomtiming::select('start_time','end_time','date','timing_type')->where('employee_id','=',$therapistlist->id)->whereBetween('date', [$from, $from])->orderBy('start_time')->get(); 

           $employeeleavelist = App\EmployeeLeave::select('leave_title','leave_date','leave_to_date','employee_id','time_type')->where('employee_id','=',$therapistlist->id)->whereBetween('leave_date', [$from, $end])->get(); 
           
           if($locationId > 0)
              {
                 $appointmentsEmployees = App\Appointment::select('start_time','finish_time')->where('employee_id','=',$therapistlist->id)->whereIn('location_id',['0',$locationId])->whereBetween('start_time', [$from, $end])->where('deleted_at','=',NULL)->get();
              }
            else {
                $appointmentsEmployees = App\Appointment::select('start_time','finish_time')->where('employee_id','=',$therapistlist->id)->whereBetween('start_time', [$from, $end])->where('deleted_at','=',NULL)->get();
              }  
            $appointment_startTime = '';
            $appointment_finishTime = '';
            $appointment_therpist = array();
           if(count($appointmentsEmployees) > 0)
            {
                foreach($appointmentsEmployees as $appointmentsEmployee)
                    {
                         $appointment_startTime   = $appointmentsEmployee->start_time;
                         $appointment_finishTime  = $appointmentsEmployee->finish_time;
                         $appointment_startTime   =  str_replace(" ","T",$appointment_startTime);
                         $appointment_finishTime   =  str_replace(" ","T",$appointment_finishTime);


                      

                       $JsonResource[]= array('resourceId'=>$therapistlist->id,'start'=>$appointment_startTime ,'end'=> $appointment_finishTime,'color'=>'#415E9B','title'=>'booked');
                        
                    }
            }
            
         
            if(count($employeeleavelist) >  0)
            {
                  $start_time =  date('Y-m-d H:i:s', strtotime($employeeleavelist[0]->leave_date));
                  $start_date_time =  date('Y-m-d', strtotime($employeeleavelist[0]->leave_date));

                  $finish_time =   date('Y-m-d H:i:s', strtotime($employeeleavelist[0]->leave_to_date));
                  
                // echo date('H:i:s','21600');

                  $finish_time_string =    strtotime(date('H:i:s',strtotime($employeeleavelist[0]->leave_to_date)));
                  
                  $JsonResource[]=array('resourceId'=>$therapistlist->id,'start'=>$start_time ,'end'=> $finish_time,'color'=> 'red');

                   $endStart = $from." 23:59:59";
                  
                   $endStart = Carbon::parse($endStart);

                   $finishTime = Carbon::parse($employeeleavelist[0]->leave_to_date);

                   $totalDuration = $endStart->diffInSeconds($finishTime);
                  if($totalDuration > 0) 
                  {
                     foreach($workinglist as $workinglisto)
                      {
                         
                       //put the condition where we checking each therpist leave time with his working time on same date and once they are equal then we using working time if not then we are using finish time of leave date = start time of that partiuclar date   

                       
                     // echo $workinglist->id;
                        if($therapistlist->id == $employeeleavelist[0]->employee_id && (strtotime($start_date_time) == strtotime($workinglisto->date)))
                           {
                              $remaningtime = $finish_time_string-strtotime($workinglisto->start_time);
                             if($remaningtime == 0)
                              {
                                  $start_working_time =  date('Y-m-d', strtotime($workinglisto->date))."T".$workinglisto->start_time;
                              }
                              else
                              {
                               // echo $workinglisto->date;
                                //echo "<br>";
                               if($employeeleavelist[0]->time_type=='end_time')
                                {
                                    $start_working_time =  date('Y-m-d', strtotime($workinglisto->date))."T".date('H:i:s',$finish_time_string);  
                                }
                                else
                                {
                                   $start_working_time =  date('Y-m-d', strtotime($workinglisto->date))."T".$workinglisto->start_time;  
                                }
                              }

                             $finish_working_time =  date('Y-m-d', strtotime($workinglisto->date))."T".$workinglisto->finish_time;
                          // echo $start_working_time;
                           $JsonResource[]=array('resourceId'=>$therapistlist->id,'start'=>$start_working_time ,'end'=> $finish_working_time);
                           }   

                      } 
                  }

              }
            elseif(count($customizeworkinglists) >  0)
            {
                $newStarttime='';$nextworkingH='';

              foreach($customizeworkinglists as  $customizeworkinglist)
                  {
                    //echo "<pre>";print_r($workinglist);
                    //need to check workinghour with customize timing and store customize end time as newstarttime and also need to trace customize end time to create rest of working hour
                     if(isset($workinglist[0]->start_time))
                      {
                          if(strtotime($workinglist[0]->start_time) < strtotime($customizeworkinglist->start_time))
                             {
                              if(empty($newStarttime))
                               {
                                 $start_w_time =  date('Y-m-d', strtotime($customizeworkinglist->date))."T".$workinglist[0]->start_time;

                               }
                               else
                               {
                                  $start_w_time = $newStarttime;
                               }

                              $finish_w_time =  date('Y-m-d', strtotime($customizeworkinglist->date))."T".$customizeworkinglist->start_time;

                              $newStarttime =  date('Y-m-d', strtotime($customizeworkinglist->date))."T".$customizeworkinglist->end_time;

                               $JsonResource[]=array('resourceId'=>$therapistlist->id,'start'=>$start_w_time ,'end'=> $finish_w_time);
                             }

                      }
                     $start_time =  date('Y-m-d', strtotime($customizeworkinglist->date))."T".$customizeworkinglist->start_time;
                     $nextworkingH =$customizeworkinglist->end_time;
                  $finish_time =  date('Y-m-d', strtotime($customizeworkinglist->date))."T".$customizeworkinglist->end_time;
                  //echo $customizeworkinglist->timing_type;
                  if($customizeworkinglist->timing_type=='available')
                     {
                       $JsonResource[]=array('resourceId'=>$therapistlist->id,'start'=>$start_time ,'end'=> $finish_time,'color'=>'#90ee90');
                     }
                    else {
                       /*$JsonResource[]=array('resourceId'=>$therapistlist->id,'start'=>$start_time ,'end'=> $finish_time,'color'=>'#90ee90');*/
                     } 
                  }
                 if(isset($workinglist[0]->start_time))
                      {
                         if(strtotime($workinglist[0]->finish_time) > strtotime($nextworkingH))
                          {
                            $again_start_time =  date('Y-m-d', strtotime($customizeworkinglist->date))."T".$nextworkingH;
                            $again_finish_time =  date('Y-m-d', strtotime($customizeworkinglist->date))."T".$workinglist[0]->finish_time;
                             $JsonResource[]=array('resourceId'=>$therapistlist->id,'start'=>$again_start_time ,'end'=> $again_finish_time);

                          } 
                      }
            }
            elseif(count($workinglist) > 0)
               {

                  foreach($workinglist as $workinglisto)
                  {
                     
                       

                     $start_time =  date('Y-m-d', strtotime($workinglisto->date))."T".$workinglisto->start_time;

                  $finish_time =  date('Y-m-d', strtotime($workinglisto->date))."T".$workinglisto->finish_time;
                 // echo $workinglist->id;
                  
                          $JsonResource[]=array('resourceId'=>$therapistlist->id,'start'=>$start_time ,'end'=> $finish_time);

                  } 
               }
            }
      
     //echo "<pre>" ;print_r($JsonResource);exit;
       
       $JsonResource = json_encode($JsonResource);
       
       $data['JsonResource'] = $JsonResource;
       
       return $data['JsonResource']; 
    }
    public function Emplyoeeappointmentjson(Request $request)
    {
        $user =   Auth::getUser();
        $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
        $employee_id = $therapist[0]->id;
        $startDateArray =  explode("T", $request->Dates);
        $start_time = $startDateArray[0];
        $endDateArray =  explode("T", $request->end);
        $end_time = $endDateArray[0];


        $start_time_new = date('Y-m-d H:i:s',strtotime($request->Dates));
        $start_date_new = date('Y-m-d',strtotime($request->Dates));
        $end_time_new = date('Y-m-d H:i:s',strtotime($request->end));
        $end_date_new = date('Y-m-d',strtotime($request->end));
        //   echo "<pre>"; print_r($start_time_new); print_r($end_time_new);    die;
        //  echo "<pre>"; print_r($request->input());     die;

        $working_hours =  DB::table('employees')->select('working_hours.employee_id','working_hours.date'
            ,'start_time','finish_time','working_hours.days','working_hours.location_id','employees_rooms.room_id')
            ->where('working_hours.employee_id',$employee_id)->whereBetween('date',[$start_time,$end_time])
            ->join('working_hours','working_hours.employee_id','employees.id')
            ->join('employees_rooms','employees_rooms.employee_id','employees.id')
            ->get();


        $custom_timings =  DB::table('employee_customtimings')->select('employee_customtimings.id','employee_customtimings.employee_id','employee_customtimings.date','employee_customtimings.timing_type'
            ,'employee_customtimings.end_time','employee_customtimings.start_time','employees_rooms.location_id','employees_rooms.room_id')
            ->where('employees_rooms.employee_id',$employee_id)->whereBetween('employee_customtimings.date',[$start_time,$end_time]);
//              if(isset($request->location_id)){
//                  $custom_timings = $custom_timings->where('employee_customtimings.location_id',$request->location_id);
//              }
           $custom_timings = $custom_timings->join('employees_rooms','employees_rooms.employee_id','employee_customtimings.employee_id')
            ->get();

        $leaves =  DB::table('employee_leaves')->select('employee_leaves.leave_date','leave_to_date','employees_rooms.room_id as room_id')
            ->where('employee_leaves.employee_id',$employee_id)
            //     ->whereRaw('DATE(leave_date) >= "'.$start_date_new.'"')
            //   ->whereRaw('DATE(leave_to_date) <= "'.$end_date_new.'"')
            // ->whereRaw('leave_to_date',$end_date_new)
            //  ->whereRaw('DATE(leave_date) BETWEEN "'.$start_time.'" AND "'.$end_time.'"')
            // ->join('employees','employee_leaves.employee_id','employees.id')
            ->join('employees_rooms','employees_rooms.employee_id','employee_leaves.employee_id')

            ->get();

        // echo "<pre>"; print_r($custom_timings); die;
//        echo "<pre>"; print_r($start_time_new);
//        echo "<pre>"; print_r($end_time_new);
        // echo "<pre>"; print_r($leaves);     die;

        $rooms = DB::table('rooms')->select('id')->get();
        $temp = [];
        foreach ($rooms as $k => $v){
            $temp[$k] = $v->id;
        }



        if($request->location_id==0)
        {
            $appointments = DB::table('appointments')->join('clients', 'appointments.client_id', '=', 'clients.id')->join('locations', 'appointments.location_id', '=', 'locations.id')->select('appointments.id','clients.first_name','clients.last_name','appointments.room_id','locations.location_name','appointments.start_time','appointments.finish_time')->
            where('appointments.employee_id','=',$employee_id)->
            where('appointments.start_time','>=',$start_time)->where('appointments.finish_time','<=',$end_time)->where('appointments.deleted_at','=',NULL)->get();
        }
        else{
            $appointments = DB::table('appointments')->join('clients', 'appointments.client_id', '=', 'clients.id')->join('locations', 'appointments.location_id', '=', 'locations.id')->select('appointments.id','clients.first_name','clients.last_name','appointments.room_id','locations.location_name','appointments.start_time','appointments.finish_time')->
            where('appointments.employee_id','=',$employee_id)->
            where('appointments.start_time','>=',$start_time)->where('appointments.finish_time','<=',$end_time)->where('appointments.location_id','=',$request->location_id)->where('appointments.deleted_at','=',NULL)->get();
        }
        //echo "<pre>";print_r($appointments);exit;
        $dataJsonArray = array();
        foreach($appointments as $key => $value)
        { $fname=''; $lname='';
            if(isset($value->first_name))
            {$fname=$value->first_name;}
            if(isset($value->last_name))
            {$lname=$value->last_name;}
            $dataJsonArray[] = array('resourceId'=>$value->room_id,'title'=>$fname.' '.$lname.' , '.$value->location_name,'description'=>$fname.' '.$lname.' , '.$value->location_name,'start'=>$value->start_time,'end'=>$value->finish_time,'url'=> "appointments/$value->id");

        }
        //  $final_arr = [];
//        foreach ($working_hours as $key => $value) {
//            foreach ($leaves as $k => $v) {
//                $start_leave_date = date('Y-m-d', strtotime($v->leave_date));
//                $end_leave_date = date('Y-m-d', strtotime($v->leave_to_date));
//                if (($value->date >= $start_leave_date) && ($value->date <= $end_leave_date)) {
//                    unset($working_hours[$key]);
//                }
//            }
//
//        }


        foreach ($leaves as $k => $value) {

            $dataJsonArray[] =
                array('title'=> "Leaves",'description'=> "Leaves",'start'=>  date("Y-m-d\TH:i:s", strtotime($value->leave_date)),
                    'end'=>date("Y-m-d\TH:i:s", strtotime($value->leave_to_date)),
                    'color' =>  "#ffcccb",
                    'resourceId'=>$value->room_id,
                    'url'=>
                        URL::to('/').'/admin/employees_leaves/create/'.$employee_id.'?start_time='.date("Y-m-d\TH:i:s", strtotime($value->leave_date)).'&end_time='.date("Y-m-d\TH:i:s", strtotime($value->leave_to_date)).'&date='.date("Y-m-d", strtotime($value->leave_date)).'&roomId='.$value->room_id.'&therapistId='.$employee_id);
        }


        //  echo "<pre>"; print_r($working_hours); die;
        $temp_custom =  [];

        //echo "<pre>"; print_r($custom_timings); die;
        foreach ($custom_timings as $key => $value){

            if($value->timing_type == 'unavailable') {
                $dataJsonArray[] =
                    array('title' => "Custom Timing", 'description' => "Unavailable", 'start' => date("Y-m-d\TH:i:s", strtotime("" . $value->date . " " . $value->start_time . "")),
                        'end' => date("Y-m-d\TH:i:s", strtotime("" . $value->date . " " . $value->end_time . "")),
                        'color' => "#FFA500",
                        'resourceId' => $value->room_id,
                        'loc' => $value->location_id,
                        'url' =>
                            URL::to('/') . '/admin/employees_customtiming/edit/' . $value->id );
            }else{
                $dataJsonArray[] =
                array('title' => "Custom Timing", 'description' => "Available", 'start' => date("Y-m-d\TH:i:s", strtotime("" . $value->date . " " . $value->start_time . "")),
                    'end' => date("Y-m-d\TH:i:s", strtotime("" . $value->date . " " . $value->end_time . "")),
                    'color' => "#008000",
                    'loc' => $value->room_id,
                    'resourceId' => $value->location_id,
                    'url'=>
                        URL::to('/').'/admin/appointments/create?start_time='.$value->start_time.'&end_time='.$value->end_time.'&date='.$value->date.'&roomId='.$value->room_id.'&therapistId='.$employee_id);

            }
        }

        foreach ($working_hours as $key => $value) {

            if(in_array($value->room_id,$temp)){

                $dataJsonArray[] =
                    array('title'=> "Available",'description'=> "Working hours",'start'=>  date("Y-m-d\TH:i:s", strtotime("".$value->date." ".$value->start_time."")),
                        'end'=>date("Y-m-d\TH:i:s", strtotime("".$value->date." ".$value->finish_time."")),
                        'rendering' => 'background',
                        'color' =>  "#54bf18",

                        'resourceId'=>$value->room_id,
                        'url'=>
                            URL::to('/').'/admin/appointments/create?start_time='.$value->start_time.'&end_time='.$value->finish_time.'&date='.$value->date.'&roomId='.$value->room_id.'&therapistId='.$employee_id);

            }



        }






        return json_encode($dataJsonArray,true);
        // echo "<pre>";print_r($dataJsonArray);exit;
        /*$therapistlist = App\Employee::select('id','first_name as title','eventColor')->get();
        $therapistlist = json_encode($therapistlist);
        $data['therapistlist'] = $therapistlist;
        return $data['therapistlist']; */

        //{"resourceId":"a","title":"event 1","start":"2019-11-20","end":"2019-11-22"}
    }
}
