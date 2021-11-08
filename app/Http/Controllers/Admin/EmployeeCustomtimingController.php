<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\EmployeeCustomtiming;
use Illuminate\Support\Facades\Gate;

use App\Employee;
class EmployeeCustomtimingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //
         if (! Gate::allows('employee_custom_timing_create')) {
            return abort(401);
        }
       $relations=[
        'locations' => \App\Location::get()->pluck('location_name', 'id')->prepend('Please select', 0),
        'employee_id'=>$id
       ];
       return view('admin.customtiming.create',$relations);
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

//        if (! Gate::allows('employee_custom_timing_create')) {
//            return abort(401);
//        }
       $alredayExists=EmployeeCustomtiming::where('date','=',$request->date)->where('employee_id','=',$request->employee_id)->where('start_time','=',$request->start_time)->get()->count();
       if($alredayExists==0) 
         {
             $empservice = EmployeeCustomtiming::create($request->all());
             return redirect()->route('admin.employeecustomtiming.employeecustomtiminglist',[$request->employee_id]);
         }
        else{
           return redirect()->back()->with('flash_message',
          'This Date , '. $request->date.' is alreday assign to employee in custom timing');
         } 
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
       $custom_timing = EmployeeCustomtiming::find($id);
        $relations=[
            'locations' => \App\Location::get()->pluck('location_name', 'id')->prepend('Please select', 0),
            'custom_timing'=>$custom_timing,
            'id' => $id
        ];
        return view('admin.customtiming.edit',$relations);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request['id'];
        $alredayExists=EmployeeCustomtiming::where('date','=',$request->date)->where('employee_id','=',$request->employee_id)->where('start_time','=',$request->start_time)->get()->count();
        if($alredayExists==0)
        {
            $custom_timing = EmployeeCustomtiming::find($id);
            $custom_timing->date = $request['date'];
            $custom_timing->start_time = $request['start_time'];
            $custom_timing->end_time = $request['end_time'];
            $custom_timing->timing_type = $request['timing_type'];
            $custom_timing->location_id = $request['location_id'];
            $custom_timing->update();

            return redirect()->route('admin.employeecustomtiming.employeecustomtiminglist',[$custom_timing->employee_id]);
        }
        else{
            return redirect()->back()->with('flash_message',
                'This Date , '. $request->date.' is alreday assign to employee in custom timing');
        }




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
        $employee = EmployeeCustomtiming::findOrFail($id);
        $eid = $employee->employee_id;
        $employee->delete();
        return redirect()->route('admin.employeecustomtiming.employeecustomtiminglist',[$eid]);
    }
    public function timinglist($id)
     {

         if (! Gate::allows('leave_access')) {
            return abort(401);
        }

        $customtiming = EmployeeCustomtiming::where('employee_id','=',$id)->get();
        
     $relations = [
             
                'emp_general_info' => Employee::where('id','=',$id)->get(),
                
            ];
     
         
       return view('admin.customtiming.index', compact('customtiming')+$relations);
     }
}
