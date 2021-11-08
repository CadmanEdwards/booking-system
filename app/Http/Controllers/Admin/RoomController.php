<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Room;
use App\RoomAvailability;
class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (! Gate::allows('user_access')) {
            return abort(401);
        }

        $rooms = Room::all();

         
       return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if (! Gate::allows('user_create')) {
            return abort(401);
        }
       
        $relations = [
         
            /*'working_type' => array('wholeweek'=>'Time Series(entire week)','weekend'=>'Weekend'),*/
             'locations' => \App\Location::get()->pluck('location_name', 'id')->prepend('Please select', ''),
             'working_type' => array('1'=>'Monday','2'=>'Tuesday','3'=>'Wednesday','4'=>'Thursday','5'=>'Friday','6'=>'Saturday','7'=>'Sunday')
        ];

        return view('admin.rooms.create',$relations);
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

        if (! Gate::allows('user_create')) {
            return abort(401);
        }
        $room = Room::create($request->all()); 

           $date = date('Y-m-d');
    // End date
    $end_date = date('Y-m-d', strtotime('+7 days'));
    $lookup=['Sunday'=>0,'Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6];
    $startday = date('l'); //current day 
   
    //while (strtotime($date) <= strtotime($end_date)) {
               foreach($request->booking_pricing_time_from as $key => $value)
                {
                  
                  $noOfdays1 = $lookup[$key]-$lookup[$startday];
                  $noOfdays= $noOfdays1 < 1 ? $noOfdays1+7: $noOfdays1; 

                  if($noOfdays1==0)
                     { $start_date = date('Y-m-d');}
                    else {
                       $start_date = date('Y-m-d', strtotime('+'.$noOfdays.' days'));
                     } 
                   
                   $working_hour = RoomAvailability::create([
                   'room_id' => $room->id,
                   'date' => $start_date,
                   'day' => $key,
                   'start_time' => isset($request->booking_pricing_time_from[$key]) ? $request->booking_pricing_time_from[$key].":00" : "00:00:00",
                   'end_time' => isset($request->booking_pricing_time_to[$key]) ? $request->booking_pricing_time_to[$key].":00" : "00:00:00"
                  
                  ]); 

                  
                }


        return redirect()->route('admin.rooms.index');
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

          if (! Gate::allows('user_edit')) {
            return abort(401);
        }
        $room = Room::findOrFail($id);

        $WorkingHorsArray =  \App\RoomAvailability::where('room_id','=',$id)->where('date','>=',date('Y-m-d'))->get();
       $accordingDays = array();

       if($WorkingHorsArray->count() > 0)
       {
         foreach($WorkingHorsArray as $WorkingHorsArray)
               {
                   $accordingDays[$WorkingHorsArray->day]=array(
                     'start_time' => $WorkingHorsArray->start_time,
                     'finish_time' => $WorkingHorsArray->end_time,
                   );

               }
       }

           $relations = [
         
            'locations' => \App\Location::get()->pluck('location_name', 'id')->prepend('Please select', ''),
             'working_type' => array('1'=>'Monday','2'=>'Tuesday','3'=>'Wednesday','4'=>'Thursday','5'=>'Friday','6'=>'Saturday','7'=>'Sunday'),
             'empworkinghHours' =>$accordingDays,
        ];
        return view('admin.rooms.edit', compact('room')+ $relations);

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

        if (! Gate::allows('user_edit')) {
            return abort(401);
        }
        $room = Room::findOrFail($id);

      
       $WorkingHorsArray =  \App\RoomAvailability::where('room_id','=',$id)->where('date','>=',date('Y-m-d'))->delete();
        
    $date = date('Y-m-d');
    // End date
    $end_date = date('Y-m-d', strtotime('+7 days'));
    $lookup=['Sunday'=>0,'Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6];
    $startday = date('l'); //current day 
   
    //while (strtotime($date) <= strtotime($end_date)) {
               foreach($request->booking_pricing_time_from as $key => $value)
                {
                  
                  $noOfdays1 = $lookup[$key]-$lookup[$startday];
                  $noOfdays= $noOfdays1 < 1 ? $noOfdays1+7: $noOfdays1; 

                  if($noOfdays1==0)
                     { $start_date = date('Y-m-d');}
                    else {
                       $start_date = date('Y-m-d', strtotime('+'.$noOfdays.' days'));
                     } 
                    
                     $dayL = date('l', strtotime($start_date));
                     $workingHoursDays = RoomAvailability::where('room_id','=',$id)->where('day','=',$dayL)->get(); 

                       if($workingHoursDays->count() == 0)
                       {
                         $working_hour = RoomAvailability::create([
                       'room_id' => $id,
                       'date' => $start_date,
                       'day' => $key,
                      
                      'start_time' => isset($request->booking_pricing_time_from[$key]) ? date('H:i:s',strtotime($request->booking_pricing_time_from[$key])) : "00:00:00",
                       'end_time' => isset($request->booking_pricing_time_to[$key]) ? date('H:i:s',strtotime($request->booking_pricing_time_to[$key])) : "00:00:00"
                      
                      ]);
                       }
                    

                  
                }


       


        $room->update($request->all()); 
        return redirect()->route('admin.rooms.index');
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
        if (! Gate::allows('user_delete')) {
            return abort(401);
        }
        $room = Room::findOrFail($id);
        $WorkingHorsArray =  \App\RoomAvailability::where('room_id','=',$id)->delete();
        $location_room_delete =  DB::table('rooms_locations')->where('room_id', $id)->delete();

        $room->delete();

        return redirect()->route('admin.rooms.index');
    }
}
