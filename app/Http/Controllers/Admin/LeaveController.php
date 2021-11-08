<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\EmployeeLeave;
use App\Employee;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Input;
use DB;
class LeaveController extends Controller
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

          if (! Gate::allows('leave_create')) {
            return abort(401);
        }
       $relations=[
        'employee_id'=>$id
       ];
       return view('admin.leaves.create',$relations);
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

        if (! Gate::allows('leave_create')) {
            return abort(401);
        }

       // $rules = array( 'leave_title' => 'required|unique','leave_comment' => 'required',
         //   'leave_date' => 'date_format:Y-m-d|required|unique:employee_leaves',);
        //$validator = Validator::make(Input::all(), $rules);
         $validator = $request->validate([
            'leave_title' => 'required',
            
            'leave_date' => 'date_format:Y-m-d|required'
            // 'leave_date' => 'date_format:Y-m-d|required|unique:employee_leaves'
         ]);
           $countLeave2= DB::table('employee_leaves')->where('leave_date','=',$request->leave_date)->where('employee_id','=',$request->employee_id)->get();
                    if(count($countLeave2) > 0)
                      {
                         return redirect()->route('admin.employees_leaves.create',$request->employee_id )->withInput()->with('error', 'Leave Can not be created, May be your Leave start date or Leave To Date is alreday created');
                      } 
        $date = Carbon::parse($request->leave_date);

        $now = Carbon::parse($request->leave_to_date);
        if($date > $now)
          {
             return redirect()->route('admin.employees_leaves.create',$request->employee_id )->withInput()->with('error', 'Leave Can not be created, From Date need to be greater than To Date');
          }
        $diff = $date->diffInDays($now);
      // exit;
//'employee_id','leave_reason_id','leave_date','leave_comment','leave_title','parent_id'
       // $empservice = EmployeeLeave::create($request->all());    
        $startTime =$request->start_time.':00';$endTime =$request->end_time.':00';
        if($request->start_time=='')
             {$startTime ='00:00:00';}
         if($request->end_time=='')
             {$endTime ='';}
        
        $endTime =  empty($endTime) ? '23:59:59' : $endTime  ;
         $laveToDate=$request->leave_to_date." ".$endTime;
      
         if($diff > 1)
          {$laveToDate=$request->leave_to_date." 23:59:59";}
        $empservice = EmployeeLeave::create([
           'employee_id' => $request->employee_id,
           'leave_date' => $request->leave_date." ".$startTime,
           'leave_to_date' => $laveToDate,
           'leave_comment' => $request->leave_comment,
           'leave_title' => $request->leave_title,
           'parent_id' => 0,
           'time_type' => 'start_time'
           
        ]); 
       $parentId=$empservice->id; 
      if($diff > 1)
         {
            for($i=1;$i<=$diff;$i++)
                {
                    $date = $date->addDays(1);
                 /*   echo " Allowing  ".$i;
                    echo "<br>";
                    echo " Difference ".($diff-1); 
                    echo "<br>";*/
                    $date1="";$endDate='';
                    $timeType ='start_time';
                    if(($diff) == $i)
                      { 
                        $date1 = date('Y-m-d',strtotime($date))." 00:00:00";
                        $endDate= date('Y-m-d',strtotime($date))." ".$endTime;
                         $timeType='end_time';

                      }  
                    else
                        { 
                            $date1 = $date; 
                            $endDate= date('Y-m-d',strtotime($date))." 23:59:59"; 
                        } 
                    /*echo " Date ".$date1; 
                    echo "<br>";*/
                    $countLeave= DB::table('employee_leaves')->where('leave_date','=',$date)->get();
                    if(count($countLeave) == 0)
                      {
                          $empservice1 = EmployeeLeave::create([
                           'employee_id' => $request->employee_id,
                           'leave_date' => $date1,
                           'leave_to_date' => $endDate,
                           'leave_comment' => $request->leave_comment,
                           'leave_title' => $request->leave_title,
                           'parent_id' => $parentId,
                           'time_type' => $timeType
                        ]);
                      }

                }
         }
           
        return redirect()->route('admin.leave.leavelist',[$request->employee_id]);
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
        $employee = EmployeeLeave::findOrFail($id);
        $eid = $employee->employee_id;
        $employee->delete();
        return redirect()->route('admin.leave.leavelist',[$eid]);
    }

    public function leavelist($id)
    {
          if (! Gate::allows('leave_access')) {
            return abort(401);
        }

        $leaves = EmployeeLeave::where('employee_id','=',$id)->get();
        
 $relations = [
         
            'emp_general_info' => Employee::where('id','=',$id)->get(),
            
        ];
     
         
       return view('admin.leaves.index', compact('leaves')+$relations);
    }
}
