<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Doctor;
/*use App\Http\Requests\Admin\StoreDoctorRequest;
use App\Http\Requests\Admin\UpdateDoctorRequest;*/
use App\User;
//use App\EmailTemplate;
use DB; 
use \App;
use Date;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Mail;
use DataTables;

class DoctorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatables(Request $request)
    {

        $emailTemplate=array();
        $user = \Auth::user();   
        $start = $request->start;
        $length = $request->length;
        $employee_id = 0;
        if($user->role_id == 3)
         {  
          $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
          $employee_id = $therapist[0]->id;
         }

        $column_name_with_key = [1 => 'doctors.first_name',2 => 'doctors.last_name',3=>'doctors.phone',4 => 'doctors.email',5 => 'doctors.created_at', 6 => 'doctors.comment'];
     $search = $request->input('search');
     if($search)
      {
          $search = $request->input('search');

      }

        /*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX*/
        $doctors = Doctor::select('id','first_name','last_name','phone','email','created_at','comment');
        if($employee_id>0) { $doctors = $doctors->Where('doctors.add_by', $employee_id); }
        if($search)
        {
          $doctors = $doctors->Where(
                        function ($doctors) use ($search){
                       return $doctors->Where('first_name', 'LIKE', '%'.$search.'%')
                          ->orwhere('last_name', 'LIKE', '%'.$search.'%')
                          ->orwhere('phone', 'LIKE', '%'.$search.'%')
                          ->orwhere('email', 'LIKE', '%'.$search.'%')
                          ->orwhere('comment', 'LIKE', '%'.$search.'%')
                          ->orwhere('doctors.created_at', 'LIKE', '%'.$search.'%');
                        });
        }



       // if(isset($request->order)){

       //   echo "<pre>"; print_r($request->order); die;

          if($request->order[0]['column'] == 0){
              $doctors = $doctors->orderBy('doctors.created_at', 'desc');
          }else{
              foreach ($request->order as $k => $v){
                  $tem = $v['column'];

                  $doctors = $doctors->orderBy($column_name_with_key[$tem], $v['dir']);

              }
          }

     //   }else{
         //   $doctors = $doctors->orderBy('doctors.created_at', 'desc');
    //   }

        



            $doctors = $doctors->offset($start)->limit($length);

        /*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX*/
        $doctorsTot = Doctor::select('id','first_name','last_name','phone','email','created_at','comment');
        if($employee_id>0) 
            { $doctorsTot = $doctorsTot->Where('add_by', $employee_id); }
        if($search)
        {

          $doctorsTot = $doctorsTot->Where(
                        function ($doctorsTot) use ($search){
                       return $doctorsTot->Where('first_name', 'LIKE', '%'.$search.'%')
                          ->orwhere('last_name', 'LIKE', '%'.$search.'%')
                          ->orwhere('phone', 'LIKE', '%'.$search.'%')
                          ->orwhere('email', 'LIKE', '%'.$search.'%')
                          ->orwhere('comment', 'LIKE', '%'.$search.'%')
                          ->orwhere('doctors.created_at', 'LIKE', '%'.$search.'%');
                        });
        }

//        foreach ($search->input['order'] as $k => $v){
//
//            $doctors = $doctors->orderBy($column_name_with_key['column'], $column_name_with_key['dir']);
//
//        }
        $cntClient = $doctorsTot->orderBy('doctors.created_at', 'desc')->count();
         
        return Datatables::of($doctors)
                           ->editColumn('checkbox', function(Doctor $data) {

                         //      echo "<pre>"; print_r($data->id); die;
        return  $data->id ;
    })

                            ->editColumn('first_name', function(Doctor $data) {
                              $first_name = $data->first_name;
                              return  $first_name;
                            })
                            ->editColumn('last_name', function(Doctor $data) {
                                 $price = $data->last_name ;
                                return  $price;
                            })
                            ->editColumn('phone', function(Doctor $data) {
                                return $data->phone;
                            })
                            ->editColumn('email', function(Doctor $data) {
                                return $data->email;
                            })
                            ->editColumn('created_at', function(Doctor $data) {
                                return date('Y-m-d',strtotime($data->created_at));
                            })
                            ->editColumn('comment', function(Doctor $data) {
                                return $data->comment;
                            })
                             
                            ->addColumn('action', function(Doctor $data) {
                                
                                

                                $StrVal =  '<a href="'.route('admin.doctors.show',[$data->id]).'" class="btn btn-xs btn-primary" title="View"><i class="fa fa-eye"></i></a>

                                  <a href="'.route('admin.doctors.edit',[$data->id]).'" class="btn btn-xs btn-info" title="Edit"><i class="fa fa-pencil"></i></a>

                                  <a href="'.route('admin.doctor_destroy',[$data->id]).'" class="btn btn-xs btn-danger" title="Delete"><i class="fa fa-trash"></i></a>';

                                $StrVal = $StrVal.'<a onclick="loadcolydiew('.$data->id.')"  href="javascript:void(0)" data-toggle="modal" data-target="#calendarModal" class="btn btn-xs btn-info" title="Copy"><i class="fa fa-clone"></i></a>';

                                $StrVal = $StrVal.'<a onclick="loadcolydiew('.$data->id.')"  href="javascript:void(0)" data-toggle="modal" data-target="#CustomerModal" class="btn btn-xs btn-info" title="Create Customer"><i class="fa fa-user"></i></a>';
 
                                $StrVal =  '<div class="btn-group">'.$StrVal.'</div>';

                                 
                                  

                                return $StrVal;
                            })
                             ->with([
                             "recordsTotal"    => $cntClient,
                             "recordsFiltered"  => $cntClient,
                         ])->skipPaging()
                            ->toJson(); 
                            //--- Returning Json Data To Client Side
    }
    public function index()
    {
      if (! Gate::allows('doctor_access')) { return abort(401);  }
      $clientsOther = $emailTemplate=array();
       $user = \Auth::user();   
        $employee_id=0;
        
        $doctors = Doctor::where('deleted_at','=',NULL)->get();/*->where('add_by','=',$employee_id)*/
             
        $relations = ['clientsOther' => $clientsOther,'emailTemplate' =>$emailTemplate ];
        return view('admin.doctors.index',compact('doctors')+$relations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('doctor_create')) { return abort(401); }
        $user = \Auth::user();   
        $relations = ['locations' => \App\Location::get()];
        return view('admin.doctors.create',$relations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('doctor_create')) { return abort(401); }
        $rules = array( 'first_name' => 'required','last_name' => 'required');
        $validator = Validator::make(Input::all(), $rules);
        $name = $request->first_name.' '.$request->last_name;
        $usertest = \Auth::user();   
        $add_by =$usertest->id;

        $doctor = Doctor::create([
           'first_name' => $request->first_name,
           'last_name' => $request->last_name,
           'email' =>  $request->email,
           'house_number' => $request->house_number,
           'dob' => $request->dob,
           'address' => $request->address,
           'postcode' => $request->postcode,
           'city_name' => $request->city_name,
           'phone' => $request->phone,
           'comment' => $request->comment,
           'add_by' => $add_by,  
        ]); 
        return redirect()->route('admin.doctors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
        if (! Gate::allows('doctor_view')) {
            return abort(401);
        }
        $relations = [ ];
        $doctor = Doctor::findOrFail($id);
        return view('admin.doctors.show', compact('doctor') + $relations);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('doctor_edit')) {
            return abort(401);
        }
        $doctor = Doctor::findOrFail($id);
        //dd($doctor);
        $relations = [
            'locations' => \App\Location::get()
        ];
        return view('admin.doctors.edit', compact('doctor')+$relations);
    
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
        if (! Gate::allows('doctor_edit')) {
            return abort(401);
        }
        $doctor = Doctor::findOrFail($id);
        $name = $request->first_name.' '.$request->last_name;
        $doctor->update($request->all());
        return redirect()->route('admin.doctors.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('doctor_delete')) {
            return abort(401);
        }
       
        $doctor = Doctor::findOrFail($id)->forceDelete();
            return redirect()->route('admin.doctors.index');
    }
     public function copy($id)
    {
        if (! Gate::allows('doctor_create')) { return abort(401); }
        $client = Doctor::findOrFail($id);
        if(!empty($client))
        {
          $RsData=array('message'=>'success','client'=>$client);
        }
        else
        {
          $RsData=array('message'=>'No Client Information Exist');
        }
        return json_encode($RsData,true);
        exit;
        
        
    }
}
