<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use App\Location;
use App\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientsRequest;
use App\Http\Requests\Admin\UpdateClientsRequest;
use App\User;
use App\EmailTemplate;
use DB;
use \App;
use JeroenDesloovere\VCard\VCard;
use Moneybird;
use Date;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use DataTables;
use Illuminate\Filesystem\Filesystem;

class ClientsController extends Controller
{
    /**
     * Display a listing of Client.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatables(Request $request)
    {
        // if ($request->ajax()) {

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
        $search = "";

        if(!empty($request->input('search')))
        { $search = $request->input('search'); }

        /*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX*/
        $clients = Client::leftJoin('appointments', 'clients.id', '=', 'appointments.client_id')->select('clients.id','clients.first_name','clients.last_name','clients.phone','clients.email','moneybird_contact_id','clients.comment','clients.parent_id','clients.created_at');


        $clients = $clients->where('moneybird_contact_id','=','1');

        if($employee_id>0)
        {
            $clients = $clients->Where(
                function ($clients) use ($employee_id){
                    return $clients->Where('employee_id', '=', $employee_id)
                        ->orwhere('clients.add_by', '=', $employee_id);
                });
        }
        if(!empty($search['value']))
        {
            $SCondition = $search['value'];
            $clients = $clients->Where(
                function ($clients) use ($SCondition){
                    return $clients->Where('first_name', 'LIKE', '%'.$SCondition.'%')
                        ->orwhere('last_name', 'LIKE', '%'.$SCondition.'%')
                        ->orwhere('phone', 'LIKE', '%'.$SCondition.'%')
                        ->orwhere('email', 'LIKE', '%'.$SCondition.'%')
                        ->orwhere('comment', 'LIKE', '%'.$SCondition.'%')
                        ->orwhere('clients.created_at', 'LIKE', '%'.$SCondition.'%');
                });
        }
        $clients = $clients->orderBy('created_at', 'desc')->offset($start)->limit($length)->groupBy('clients.id');



        /*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX*/

        $clientsTot = Client::leftJoin('appointments', 'clients.id', '=', 'appointments.client_id')->select('clients.id','clients.first_name','clients.last_name','clients.phone','clients.email','moneybird_contact_id','clients.comment','clients.parent_id','clients.created_at');
        $clientsTot = $clientsTot->where('moneybird_contact_id','=','1');
        if($employee_id>0)
        {
            $clientsTot = $clientsTot->Where(
                function ($clientsTot) use ($employee_id){
                    return $clientsTot->Where('employee_id', '=', $employee_id)
                        ->orwhere('clients.add_by', '=', $employee_id);
                });
        }
        if(!empty($search['value']))
        {
            $SCondition = $search['value'];
            $clientsTot = $clientsTot->Where(
                function ($clientsTot) use ($SCondition){
                    return $clientsTot->Where('first_name', 'LIKE', '%'.$SCondition.'%')
                        ->orwhere('last_name', 'LIKE', '%'.$SCondition.'%')
                        ->orwhere('phone', 'LIKE', '%'.$SCondition.'%')
                        ->orwhere('email', 'LIKE', '%'.$SCondition.'%')
                        ->orwhere('comment', 'LIKE', '%'.$SCondition.'%')
                        ->orwhere('clients.created_at', 'LIKE', '%'.$SCondition.'%');
                });
        }
        $clientsTot = $clientsTot->groupBy('clients.id');
        $cntClient = count($clientsTot->get());



        /*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX*/


        return Datatables::of($clients)
            ->editColumn('checkbox', function(Client $data) {
                return  $data->id ;
            })

            ->editColumn('first_name', function(Client $data) {
                $first_name = $data->first_name;
                return  $first_name;
            })
            ->editColumn('last_name', function(Client $data) {
                $price = $data->last_name ;
                return  $price;
            })
            ->editColumn('phone', function(Client $data) {
                return $data->phone;
            })
            ->editColumn('email', function(Client $data) {
                return $data->email;
            })
            ->editColumn('created_at', function(Client $data) {
                return date('Y-m-d',strtotime($data->created_at));
            })
            ->editColumn('comment', function(Client $data) {
                return $data->comment;
            })
            ->editColumn('parent_id', function(Client $data) {
                $clientParent = Client::where('id','=',$data->parent_id)->get();
                $parentname = '-';
                if(isset($clientParent[0]->first_name))
                    $parentname = $clientParent[0]->first_name." ".$clientParent[0]->last_name;

                return $parentname;
            })
            /*->editColumn('moneybird_contact_id', function(Client $data) {

                return $data->moneybird_contact_id;
            })*/
            ->addColumn('action', function(Client $data)  {



                $StrVal =  '<div class="btn-group"><a href="'.route('admin.clients.show',[$data->id]).'" class="btn btn-xs btn-primary" title="View"><i class="fa fa-eye"></i></a>

                                  <a href="'.route('admin.clients.edit',[$data->id]).'" class="btn btn-xs btn-info" title="Edit"><i class="fa fa-pencil"></i></a>

                                  <a href="'.route('admin.client_destroy',[$data->id]).'" class="btn btn-xs btn-danger" title="Delete"><i class="fa fa-trash"></i></a>
                                  <a href="'.route('admin.client_vcard',['first_name' => $data->first_name,'last_name' => $data->last_name,'phone' => $data->phone,'email' => $data->email]).'" class="btn btn-xs btn-info" title="Vcard"><i class="fa fa-id-badge"></i></a>';

                /*$StrVal = $StrVal.'<a href="'.route('admin.client_copy',[$data->id]).'" class="btn btn-xs btn-success">Copy</a>';*/

                $StrVal = $StrVal.'<a onclick="loadcolydiew('.$data->id.')"  href="javascript:void(0)" data-toggle="modal" data-target="#calendarModal" class="btn btn-xs btn-info" title="Copy"><i class="fa fa-clone"></i></a>';


                if (Gate::allows('appointment_create'))
                {
                    $StrVal =  $StrVal.'<a href="'.route('admin.appointments.create',['client_id' =>$data->id]).'" class="btn btn-xs btn-info">New Booking</a>';
                }
                $StrVal =  $StrVal.'</div>';


                return $StrVal;
            })
            ->with([
                "recordsTotal"    => $cntClient,
                "recordsFiltered"  => $cntClient,
            ])->skipPaging()
            ->toJson();
        //--- Returning Json Data To Client Side



    }
    public function make_vcard($first_name,$last_name,$phone,$email){

        $file = new Filesystem();
        $file->cleanDirectory(storage_path().'/app/public/vcard');

        $vcard = new VCard();

// define variables

        $additional = '';
        $prefix = 'client-';
        $suffix = '';

// add personal data
        $vcard->addName($last_name, $first_name, $additional, $prefix, $suffix);

        $filename  = $prefix.str_replace(' ','-',strtolower($first_name)).'-'.str_replace(' ','-',strtolower($last_name)).'.vcf';

// add work data
        //   $vcard->addCompany('Siesqo');
        $vcard->addJobtitle('Client');
        $vcard->addRole('Client');
        $vcard->addEmail($email);
        $vcard->addPhoneNumber($phone, 'PREF;WORK');
        $vcard->addPhoneNumber($phone, 'WORK');
         $vcard->setSavePath(storage_path().'/app/public/vcard');
         $vcard->save();

        return response()->download(storage_path().'/app/public/vcard/'.$filename);



    }

    public function downloadExcel(){

        //DATA

        $clients = Client::select(['id','first_name','last_name','parent_id','created_at','email','comment','phone'])

            -> orderBy('created_at', 'desc')->get();

        //DAta ENDs



        $final_arr = [];
        $final_arr[0] = ['id','First name','Last name','Phone','Email','Created Date','Comment','Parent Name'];
        foreach ($clients as $key => $value){
            $parent_name = "--";
            if(!empty($value->parent_id) && $value->parent_id != 0){
                $parent = Client::select(['first_name','last_name'])
                    -> where('id',$value->parent_id)
                    ->first();
                $parent_name = $parent->first_name.' '.$parent->last_name;
            }
            $final_arr[$key+1] = [$value->id,$value->first_name,$value->last_name,$value->phone,$value->email,date('Y-m-d',strtotime($value->created_at)),$value->comment,$parent_name];
        }

        // echo "<pre>"; print_r($final_arr); die;

        Excel::create("customer_".time()."", function($excel) use ($final_arr) {

            $excel->sheet('Customer', function($sheet)  use ($final_arr) {

                $sheet->fromArray($final_arr);

            });

        })->download('csv');
    }

    public function index()
    {
        if (! Gate::allows('client_access')) {
            return abort(401);
        }
        /*Date::setLocale('nl');
        echo Date::parse('2019-12-27')->format('l j F Y');
      exit;*/

        //echo "Email ID ".$contactSearchObject->email;

        $clientsOther = $emailTemplate=array();
        $user = \Auth::user();

        $employee_id=0;

        if($user->role_id == 3)
        {
            $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
            $employee_id = $therapist[0]->id;

            $clients = Client::where('deleted_at','=',NULL)->where('add_by','=',$employee_id)->get();

            $clientsOther = DB::table('appointments')->join('clients', 'clients.id', '=', 'appointments.client_id')->select('clients.id','clients.first_name','clients.last_name','clients.phone','clients.email','moneybird_contact_id','clients.comment','clients.parent_id','clients.created_at')->where('employee_id','=',$employee_id)->groupBy('clients.email')->get();
            $emailTemplate = \App\EmailTemplate::whereNull('email_type')->get()->toJson();
        }
        else
        { $employee_id=0;
            $clients = Client::orderBy('created_at', 'desc')->get();
            $emailTemplate = \App\EmailTemplate::whereNull('email_type')->get()->toJson();
        }

        if($employee_id > 0)
        {
            $parentClient = Client::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"),'id')->where('deleted_at','=',NULL)->where('add_by','=',$employee_id)->where('parent_id','=',0)->get()->pluck('name', 'id')->prepend('Please select', 0);
        }
        else
        {
            $parentClient = Client::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"),'id')->where('deleted_at','=',NULL)->where('parent_id','=',0)->get()->pluck('name', 'id')->prepend('Please select', 0);
        }

        //dd($parentClient);

        $relations = ['clientsOther' => $clientsOther,'emailTemplate' =>$emailTemplate, 'parentClient'=>$parentClient];
        return view('admin.clients.index',compact('clients')+$relations);
    }

    /**
     * Show the form for creating new Client.
     *
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        if (! Gate::allows('client_create')) { return abort(401); }
        $client = Client::findOrFail($id);
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
    public function create(Request $request)
    {
        if (! Gate::allows('client_create')) {
            return abort(401);
        }

        $user = \Auth::user();
        $DoctorList = \App\Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) AS doctor_name"))->get()->pluck('doctor_name', 'id')->prepend('Please select Doctor', '');
        if($user->role_id == 3)
        {
            $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
            $employee_id = $therapist[0]->id;

            $relations = [
                'DoctorList'=>$DoctorList,
                'locations' => \App\Location::get()->pluck('location_name', 'id'),

                'parentClient' => Client::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"),'id')
                    ->where('deleted_at','=',NULL)->where('add_by','=',$employee_id)->where('parent_id','=',0)->get()->pluck('name', 'id')->prepend('Please select', 0)
            ];
        }
        else {


            $relations = [
                'locations' => \App\Location::get(),
                'DoctorList'=>$DoctorList,
                'parentClient' => Client::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"),'id')
                    ->where('deleted_at','=',NULL)->where('parent_id','=',0)->get()->pluck('name', 'id')->prepend('Please select', 0)
            ];
        }

        return view('admin.clients.create',$relations);


    }

    public function clientwithoutmoneybird()
    {
        if (!Gate::allows('client_without_moneybird')) {
            return abort(401);
        }

        $emailTemplate=array();
        $user = \Auth::user();
        //$clientsTot = $clientsTot->where('moneybird_contact_id','=','1');
        if($user->role_id == 3)
        {
            $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
            $employee_id = $therapist[0]->id;

            $clients = Client::where('deleted_at','=',NULL)->where('moneybird_contact_id','=','0')->where('add_by','=',$employee_id)->get();
            //dd($clients);
            $clientsOther = DB::table('appointments')->join('clients', 'clients.id', '=', 'appointments.client_id')->select('clients.id','clients.first_name','clients.last_name','clients.phone','clients.email','moneybird_contact_id','clients.comment','clients.parent_id','clients.created_at')->where('employee_id','=',$employee_id)->groupBy('clients.email')->get();
            $emailTemplate = \App\EmailTemplate::where('email_user_type',6)->get()->toJson();

            $relations = [
                'clientsOther' => $clientsOther,
                'user_role_id' => $user->role_id ,
                'emailTemplate' => $emailTemplate
            ];

        }
        else
        {
            $clients = Client::where('moneybird_contact_id','=','0')->orderBy('created_at', 'desc')->get();
            $emailTemplate = \App\EmailTemplate::where('email_user_type',6)->get()->toJson();
            $relations = [
                'clientsOther' => array(),
                'user_role_id' => $user->role_id ,
                'emailTemplate' =>$emailTemplate
            ];
        }

        return view('admin.clients.withoutmoneybird',compact('clients')+$relations);

    }

    public function postExcel(Request  $request){

        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);

        $fileName = time().'_customer_excel.'.$request->file->extension();

        $request->file->move(storage_path('app/public'), $fileName);

        $file_path = Storage::disk('public')->path($fileName);

        Excel::load($file_path, function($reader) {
            $arr = $reader->toArray();
            foreach ($arr as $key => $value){

                if (! Gate::allows('client_create')) { return abort(401); }

                $name = $value['first_name'].' '.$value['last_name'];

                $password = 'pass123';

                if(isset($value['password']) && !empty($value['password'])){
                    $password = $value['password'];
                }


                $user = User::where('email',$value['email'])->first();
                if(!$user){
                    $user = new User();
                    $user->name = $name;
                    $user->role_id = 4;
                    $user->password = bcrypt($password);
                    $user->save();
                }
                $user_id = $user->id;
                $emailId= $value['email'];


                $usertest = \Auth::user();
                //Hash::make($password);
                // $clinet = App\Client::where('deleted_at','=',NULL)->where('add_by','=',$user->role_id)->count();

                if($usertest->role_id == 3)
                {
                    $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$usertest->id)->get();
                    $add_by = $therapist[0]->id;
                }
                else{
                    $add_by =1;
                }

                $client = Client::where('email',$emailId)->first();

                if(!$client){
                    $client = new Client();
                    $client->email = $emailId;
                    $client->first_name = $value['first_name'];
                    $client->last_name = $value['last_name'];
                    $client->city_name = $value['city_name'];

                    if(isset($value['house_number']) && !empty($value['house_number'])){
                        $client->house_number = $value['house_number'];
                    }

                    if(isset($value['phone']) && !empty($value['phone'])){
                        $client->phone = $value['phone'];
                    }
                    if(isset($value['company_name']) && !empty($value['company_name'])){
                        $client->company_name = $value['company_name'];
                    }

                    if(isset($value['postcode']) && !empty($value['postcode'])){
                        $client->postcode = $value['postcode'];
                    }
                    if(isset($value['address']) && !empty($value['address'])){
                        $client->address = $value['address'];
                    }
                    if(isset($value['comment']) && !empty($value['comment'])){
                        $client->comment = $value['comment'];
                    }
                    if(isset($value['dob']) && !empty($value['dob'])){
                        $client->dob = $value['dob'];
                    }
                    $client->parent_id = 0;
                    $client->add_by = $add_by;
                    $client->save();

                }


                $contactSearchObject = Moneybird::contact();


                $contactSearchObject = $contactSearchObject->search($value['email']);




                if(empty($contactSearchObject))
                {
                    $contactObject = Moneybird::contact();
                    if(isset($value['company_name']))
                    {$contactObject->company_name = $value['company_name'];}
                    $contactObject->firstname = $value['first_name'];
                    $contactObject->lastname = $value['last_name'];

                    $contactObject->send_estimates_to_email = $emailId;
                    $contactObject->send_invoices_to_email = $emailId;

                    $addressSend=" ";

                    if(isset($value['address']))
                    {
                        if(isset($addressSend))
                        {$addressSend .= $value['address']." ";}

                    }
                    if(isset($value['house_number']))
                    {
                        $addressSend .= $value['house_number'];
                    }



                    if(isset($addressSend))
                    {
                        $contactObject->address1 = $addressSend;
                    }

                    if(isset($value['phone']))
                    {$contactObject->phone = $value['phone'];}
                    if(isset($value['city_name']))
                    {$contactObject->city = $value['city_name'];}
                    if(isset($value['postcode']))
                    {$contactObject->zipcode = $value['postcode'];}

                    $contactObject->save();
                    $clientUpdate = Client::find($client->id);
                    $clientUpdate->moneybird_contact_id= $contactObject->id;
                    $clientUpdate->save();

                }
                else
                {

                    $clientUpdate = Client::find($client->id);
                    $clientUpdate->moneybird_contact_id= $contactSearchObject[0]->id;
                    $clientUpdate->save();
                }


            }

        });
        unlink($file_path);

        return response()->json(['message' => "success",'status' => true]);


    }

    public function viewExcel(){
        return view('admin.clients.view_excel');
    }
    /**
     * Store a newly created Client in storage.
     *
     * @param  \App\Http\Requests\StoreClientsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function jsonstore(Request $request)
    {
        if (! Gate::allows('client_json_create')) {
            return abort(401);
        }
        if($request->parent_id == 0)
        {
            $rules = array( 'first_name' => 'required','last_name' => 'required',
                'postcode' => 'required',
                'phone' => 'required',
                'email' => 'required|email|unique:clients,email',
                'password' => 'required',
                'confirm_password' => 'required|same:password',);
            // $validator = Validator::make(Input::all(), $rules);
        }
        else
        {
            $rules = array( 'first_name' => 'required','last_name' => 'required',
                'postcode' => 'required',
                'phone' => 'required',
                'password' => 'required',
                'confirm_password' => 'required|same:password',);
            // $validator = Validator::make(Input::all(), $rules);
        }

        /*$rules = array( 'first_name' => 'required','last_name' => 'required',
                'postcode' => 'required',
                'phone' => 'required',
                'email' => 'required|email|unique:clients,email',
                'password' => 'required',
                'confirm_password' => 'required|same:password',);*/
        $validator = Validator::make(Input::all(), $rules);

// Validate the input and return correct response
        if ($validator->fails())
        {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 200); // 400 being the HTTP code for an invalid request.
            //return response()->json(['success' => false,'client_id'=>$client->id,'name'=>$name]);
        }


        $name = $request->first_name.' '.$request->last_name;

        /* $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'role_id'  => 4,
            'password' => bcrypt($request->password)
         ]);*/
        $usertest = \Auth::user();

        if($request->parent_id == 0)
        {
            $user = User::create([
                'name' => $name,
                'email' => $request->email,
                'role_id'  => 4,
                'password' => bcrypt($request->password)
            ]);
            $user_id = $user->id;
            $emailId= $request->email;
        }
        else
        {
            $user = Client::where('id','=',$request->parent_id)->get();

            $user_id = $user[0]->user_id;
            $emailId= $user[0]->email;
        }

        //Hash::make($password);
        // $clinet = App\Client::where('deleted_at','=',NULL)->where('add_by','=',$user->role_id)->count();

        if($usertest->role_id == 3)
        {
            $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$usertest->id)->get();
            $add_by = $therapist[0]->id;
        }
        else{
            $add_by =1;
        }

        if(!empty($request->email_invoice))
        { $email_invoice = $request->email_invoice;}
        else
        { $email_invoice = "";}


        if(!empty($request->doctor_id))
        { $doctor_id = $request->doctor_id; }
        else
        { $doctor_id = 0; }
        $client = Client::create([
            'user_id' => $user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' =>  $emailId,
            'email_invoice' =>  $email_invoice,
            'doctor_id' =>  $doctor_id,
            'postcode' => $request->postcode,
            'house_number' => $request->house_number,
            'address' => $request->address,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'city_name' => $request->city_name,
            'add_by' => $add_by,
            'comment' => isset($request->comment) ? $request->comment : '',

        ]);
        $contactSearchObject = Moneybird::contact();

        // $moneybird_contact_id='271375336926610863';

        // $moneybird_contact_id='271375336926610863';
        //$contactSearchObject = $contactSearchObject->search($request->email);
        if($request->parent_id == 0)
        {
            $contactSearchObject = $contactSearchObject->search($request->email);
        }
        else
        {
            $contactSearchObject =  array();
        }

        if(empty($contactSearchObject))
        {
            $contactObject = Moneybird::contact();

            if(isset($request->company_name))
            {$contactObject->company_name = $request->company_name;}
            $contactObject->firstname = $request->first_name;
            $contactObject->lastname = $request->last_name;
            $contactObject->send_estimates_to_email = $emailId;
            $contactObject->send_invoices_to_email = $emailId;

            $addressSend=" ";

            if(isset($request->address))
            {
                if(isset($addressSend))
                {$addressSend .= $request->address." ";}

            }
            if(isset($request->house_number))
            {
                $addressSend .= $request->house_number;
            }


            if(isset($addressSend))
            {
                $contactObject->address1 = $addressSend;
            }

            if(isset($request->phone))
            {$contactObject->phone = $request->phone;}
            if(isset($request->city_name))
            {$contactObject->city = $request->city_name;}
            if(isset($request->postcode))
            {$contactObject->zipcode = $request->postcode;}

            $contactObject->save();
            $clientUpdate = Client::find($client->id);
            $clientUpdate->moneybird_contact_id= $contactObject->id;
            $clientUpdate->save();
        }
        else
        {
            $clientUpdate = Client::find($client->id);
            $clientUpdate->moneybird_contact_id= $contactSearchObject[0]->id;
            $clientUpdate->save();
        }
        $fname = isset($client->first_name) ? $client->first_name :'';
        $lname = isset($client->last_name) ? $client->last_name :'';
        $name =  $fname." ".$lname;
        //return response()->json(['status' => 'Saved successfully','client_id'=>$client->id,'name'=>$name]);
        return response()->json(array('success' => true,'client_id'=>$client->id,'name'=>$name), 200);

    }
    public function opertorjsonstore (Request $request )
    {

        if($request->parent_id == 0)
        {
            $rules = array( 'first_name' => 'required','last_name' => 'required',
                'postcode' => 'required',
                'phone' => 'required',
                'email' => 'required|email|unique:clients,email',
                'password' => 'required',
                'confirm_password' => 'required|same:password',);
            // $validator = Validator::make(Input::all(), $rules);
        }
        else
        {
            $rules = array( 'first_name' => 'required','last_name' => 'required',
                'postcode' => 'required',
                'phone' => 'required',
                'password' => 'required',
                'confirm_password' => 'required|same:password',);
            // $validator = Validator::make(Input::all(), $rules);
        }

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 200); // 400 being the HTTP code for an invalid request.
            //return response()->json(['success' => false,'client_id'=>$client->id,'name'=>$name]);
        }


        $name = $request->first_name.' '.$request->last_name;
        /* $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'role_id'  => 4,
            'password' => bcrypt($request->password)
         ]);*/
        $usertest = \Auth::user();

        if($request->parent_id == 0)
        {
            $user = User::create([
                'name' => $name,
                'email' => $request->email,
                'role_id'  => 4,
                'password' => bcrypt($request->password)
            ]);
            $user_id = $user->id;
            $emailId= $request->email;
        }
        else
        {
            $user = Client::where('id','=',$request->parent_id)->get();

            $user_id = $user[0]->user_id;
            $emailId= $user[0]->email;
        }

        //Hash::make($password);
        // $clinet = App\Client::where('deleted_at','=',NULL)->where('add_by','=',$user->role_id)->count();


        $verify_client_token = md5(time().$emailId);

        /*Moneybird Existed*/
        $OldCommentArr = "";
        if(!empty($request->comment))
        {
            $userLogin = \Auth::user();
            $OldCommentArr .= "<li> ".$userLogin->name." ".date("d-m-Y h:i:s")." ".$request->comment."</li>";
        }


        if(!empty($request->doctor_id))
        { $doctor_id = $request->doctor_id; }
        else
        { $doctor_id = 0; }

        $client = Client::create([
            'user_id' => $user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' =>  $emailId,
            'doctor_id' =>  $doctor_id,
            'postcode' => $request->postcode,
            'house_number' => $request->house_number,
            'address' => $request->address,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'city_name' => $request->city_name,
            'add_by' => $usertest->id,
            'moneybird_contact_id' =>'0',
            'status' => 'pending',
            'email_verified'=> 0,
            'verify_link'  => $verify_client_token,
            'comment' => isset($request->comment) ? $request->comment : '',
            'comment_log' => isset($OldCommentArr) ? $OldCommentArr : ''
        ]);

        $fname = isset($client->first_name) ? $client->first_name :'';
        $lname = isset($client->last_name) ? $client->last_name :'';
        $name =  $fname." ".$lname;
        //return response()->json(['status' => 'Saved successfully','client_id'=>$client->id,'name'=>$name]);
        return response()->json(array('success' => true,'client_id'=>$client->id,'name'=>$name), 200);


    }
    public function store(Request $request)
    {
        if (! Gate::allows('client_create')) { return abort(401); }

        if($request->parent_id == 0)
        {
            $rules = array( 'first_name' => 'required','last_name' => 'required',
                'postcode' => 'required',
                'email' => 'required|email|unique:clients,email',
                'password' => 'required',
                'confirm_password' => 'required|same:password',);
            $validator = Validator::make(Input::all(), $rules);
        }
        else
        {
            $rules = array( 'first_name' => 'required','last_name' => 'required',
                'postcode' => 'required',
                'password' => 'required',
                'confirm_password' => 'required|same:password',);
            $validator = Validator::make(Input::all(), $rules);
        }


        if ($validator->fails())
        {
            return redirect('admin/clients/create')
                ->withErrors($validator)
                ->withInput();
        }

        $name = $request->first_name.' '.$request->last_name;
        if($request->parent_id == 0)
        {
            $user = User::create([
                'name' => $name,
                'email' => $request->email,
                'role_id'  => 4,
                'password' => bcrypt($request->password)
            ]);
            $user_id = $user->id;
            $emailId= $request->email;
        }
        else
        {
            $user = Client::where('id','=',$request->parent_id)->get();

            $user_id = $user[0]->user_id;
            $emailId= $user[0]->email;
        }


        $usertest = \Auth::user();



        if($usertest->role_id == 3)
        {
            $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$usertest->id)->get();
            $add_by = $therapist[0]->id;
        }
        else{
            $add_by =1;
        }
        if(!empty($request->email_invoice))
        { $email_invoice = $request->email_invoice;}
        else
        { $email_invoice = "";}
        if(!empty($request->doctor_id))
        { $doctor_id = $request->doctor_id; }
        else
        { $doctor_id = 0; }

        $client = Client::create([
            'user_id' => $user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' =>  $emailId,
            'doctor_id' =>  $doctor_id,

            'email_invoice' =>  $email_invoice,
            'moneybird_contact_id' => '1',
            'postcode' => $request->postcode,
            'house_number' => $request->house_number,
            'address' => $request->address,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'city_name' => $request->city_name,
            'parent_id' => $request->parent_id,
            'add_by' => $add_by,
        ]);

      //  if($request->file(''))
        /* $contactSearchObject = Moneybird::contact();

         if($request->parent_id == 0)
           {
               $contactSearchObject = $contactSearchObject->search($request->email);
           }
          else
           {
              $contactSearchObject =  array();
           }

        if(empty($contactSearchObject))
        {
           $contactObject = Moneybird::contact();
           if(isset($request->company_name))
            {$contactObject->company_name = $request->company_name;}
           $contactObject->firstname = $request->first_name;
           $contactObject->lastname = $request->last_name;

           $contactObject->send_estimates_to_email = $emailId;
           $contactObject->send_invoices_to_email = $emailId;

           $addressSend=" ";

            if(isset($request->address))
               {
                 if(isset($addressSend))
                   {$addressSend .= $request->address." ";}

               }
               if(isset($request->house_number))
               {
                 $addressSend .= $request->house_number;
               }


           if(isset($addressSend))
            {//$contactObject->address1 = $request->address;
             $contactObject->address1 = $addressSend;
            }

          if(isset($request->phone))
           {$contactObject->phone = $request->phone;}
         if(isset($request->city_name))
           {$contactObject->city = $request->city_name;}
          if(isset($request->postcode))
           {$contactObject->zipcode = $request->postcode;}

           $contactObject->save();
           $clientUpdate = Client::find($client->id);
           $clientUpdate->moneybird_contact_id= $contactObject->id;
           $clientUpdate->save();
        }
        else
         {




            $clientUpdate = Client::find($client->id);
            $clientUpdate->moneybird_contact_id= $contactSearchObject[0]->id;
            $clientUpdate->save();
         }*/
        return redirect()->route('admin.clients.index');
    }

    /**
     * Show the form for editing Client.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('client_edit')) {
            return abort(401);
        }
        $client = Client::findOrFail($id);
        $DoctorList = \App\Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) AS doctor_name"))->get()->pluck('doctor_name', 'id')->prepend('Please select Doctor', '');
    $notes = DB::table('notes')->where('client_id',$id)->get();
        $relations = [
            "DoctorList"=>$DoctorList,
            'locations' => \App\Location::get(),
            'notes' => $notes
        ];
        return view('admin.clients.edit', compact('client')+$relations);
    }

    public function removeNotes($id){

        DB::table('notes')->where('id',$id)->delete();
        return redirect()->back();
    }

    /**
     * Update Client in storage.
     *
     * @param  \App\Http\Requests\UpdateClientsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientsRequest $request, $id)
    {
        if (! Gate::allows('client_edit')) {
            return abort(401);
        }
      // echo "<pre>"; print_r($request->file('notes')); die;
        $client = Client::findOrFail($id);
        $user = User::where('id', $client->user_id)->get()->first();
        if(!empty($user))
        {
            $name = $request->first_name.' '.$request->last_name;
            $user->name = $name;
            $user->email = $request->email;
            if(!empty($request->password))
            {$user->password =  bcrypt($request->password);}
            $user->save();
        }

        if($request->file('notes')){
            foreach ($request->file('notes') as $item => $value){

                $filename = time().'.'.$value->getClientOriginalExtension();
                $value->move(storage_path('app/public/'), $filename);

                DB::table('notes')->insert([
                   'filename' => $filename,
                   'client_id' => $id
                ]);


            }
        }


        /*Money bird edit process*/

        //  $contactSearchObject = Moneybird::contact();
        // $contactSearchObject = $contactSearchObject->search($request->email);

        // $client->moneybird_contact_id= $contactObject->id;
        // $checkAlreadyExist =  Moneybird::findByCustomerId($client->moneybird_contact_id);
        //$emailId = $request->email;
        /*if(empty($contactSearchObject) || empty($client->moneybird_contact_id))
        {
           $contactObject = Moneybird::contact();
           if(isset($request->company_name))
            {$contactObject->company_name = $request->company_name;}
           $contactObject->firstname = $request->first_name;
           $contactObject->lastname = $request->last_name;

           $contactObject->send_estimates_to_email = $emailId;
           $contactObject->send_invoices_to_email = $emailId;

           $addressSend="";

            if(!empty($request->address))
               {
                 if(isset($addressSend))
                   {$addressSend .= $request->address." ";}

               }
               if(isset($request->house_number))
               {
                 $addressSend .= $request->house_number;
               }


           if(isset($addressSend))
            {//$contactObject->address1 = $request->address;
             $contactObject->address1 = $addressSend;
            }

          if(isset($request->phone))
           {$contactObject->phone = $request->phone;}
         if(isset($request->city_name))
           {$contactObject->city = $request->city_name;}
          if(isset($request->postcode))
           {$contactObject->zipcode = $request->postcode;}

           $contactObject->save();

        }
       else
       {
        // $contactObject = Moneybird::contact();
         $contactObject = $contactSearchObject->find($client->moneybird_contact_id);

           if(isset($request->company_name))
            {$contactObject->company_name = $request->company_name;}
           $contactObject->firstname = $request->first_name;
           $contactObject->lastname = $request->last_name;

           $contactObject->send_estimates_to_email = $emailId;
           $contactObject->send_invoices_to_email = $emailId;

           $addressSend=" ";

            if(isset($request->address))
               {
                 if(isset($addressSend))
                   {$addressSend .= $request->address." ";}

               }
               if(isset($request->house_number))
               {
                 $addressSend .= $request->house_number;
               }


           if(isset($addressSend))
            {
             $contactObject->address1 = $addressSend;
            }

          if(isset($request->phone))
           {$contactObject->phone = $request->phone;}
         if(isset($request->city_name))
           {$contactObject->city = $request->city_name;}
          if(isset($request->postcode))
           {$contactObject->zipcode = $request->postcode;}

           $contactObject->update();
       } */


        /*Moneybird Existed*/

        $client->update($request->all());
        return redirect()->route('admin.clients.index');
    }
    private function getAppointmentEmail($customer,$matter,$key){

          //return $matter;

        $clientname   = $customer->first_name ." ". $customer->last_name;
        $clientemail = $customer->email;
        $address = $customer->address;
        $clientphone = $customer->phone;


        if(!empty($clientname))
        {$matter = str_replace("{clientname}",$clientname,$matter);}
        else
        {$matter = str_replace("{clientname}",'',$matter);}

        if(!empty(trim($clientemail)))
        {$matter = str_replace("{customeremail}",$clientemail,$matter);}
        else
        {$matter = str_replace("{customeremail}",'',$matter);}

        if(!empty(trim($clientphone)))
        {$matter = str_replace("{customertelephonenumber}",$clientphone,$matter);}
        else
        {$matter = str_replace("{customertelephonenumber}",'',$matter);}

        $appointment = Appointment::whereRaw('DATE(start_time) < DATE(NOW())')
            ->where('client_id',$customer->id)->orderBy('start_time', 'desc')
            ->first();

        if($appointment){
            $employee = \App\Employee::withTrashed()->where('id' , $appointment->employee_id)->first();



            $service = \App\Service::find($appointment->service_id);

            // echo "<pre>"; print_r($service); die;

            $thrapist_name = $employee->first_name." ".$employee->last_name;

            $no_of_block = $service->min_block_duration;
            $sessionCost = $service->block_cost *  $no_of_block;
            $locations= \App\Location::find($appointment->location_id);
            $locationname = $locations->location_name;
            $locationdesc = $locations->location_description;
            $location_address = $locations->location_address;
            $tharpy_registration_no='';
            if(isset($employee->registration_no))
            {$tharpy_registration_no  = $employee->registration_no;}

            $dte = date('H:i',strtotime($appointment->starting_time));
            Date::setLocale('nl');
            $matter = str_replace("{booking_date}","".Date::parse($appointment->starting_time)->format('l j F Y'),$matter);
            $matter = str_replace("{booking_time}","".$dte,$matter);

            $therapisttelephone = $employee->phone;
            $therapistdes='';$therapistdes2='';
            if(isset($service->description))
            {$therapistdes  = $service->description;}
            if(isset($service->description_second))
            {$therapistdes2  = $service->description_second;}
            $tharpyname = $service->name;

            if(!empty(trim($therapistdes2)))
            {$matter = str_replace("{therapistdes2}",$therapistdes2,$matter);}
            else
            {$matter = str_replace("{therapistdes2}",'',$matter);}



            if(!empty(trim($tharpyname)))
            {$matter = str_replace("{thrapyname}",$tharpyname,$matter);}
            else
            {$matter = str_replace("{thrapyname}",'',$matter);}

            if(!empty(trim($locationname)))
            {$matter = str_replace("{location}",$locationname,$matter);}
            else
                $matter = str_replace("{location}",'',$matter);

            if(!empty(trim($location_address)))
            {$matter = str_replace("{location_address}",$location_address,$matter);}
            else
            {$matter = str_replace("{location_address}",'',$matter);}

            if(!empty(trim($tharpy_registration_no)))
            {$matter = str_replace("{therapistregistrations}",$tharpy_registration_no,$matter);}
            else
            {$matter = str_replace("{therapistregistrations}",'',$matter);}

            if(!empty(trim($therapistdes)))
            {$matter = str_replace("{therapistdes}",$therapistdes,$matter);}
            else
            {$matter = str_replace("{therapistdes}",'',$matter);}

            if(!empty(trim($therapisttelephone)))
            {$matter = str_replace("{therapisttelephone}",$therapisttelephone,$matter);}
            else{
                {$matter = str_replace("{therapisttelephone}",'',$matter);}
            }

            if(!empty(trim($locationdesc)))
            {$matter = str_replace("{route_directions}",$locationdesc,$matter);}
            else
            {$matter = str_replace("{route_directions}",'',$matter);}


            if(!empty(trim($sessionCost)))
            {$matter = str_replace("{session_costs_for_an_hour}",$sessionCost,$matter);}
            else
                $matter = str_replace("{session_costs_for_an_hour}",'',$matter);

            if(!empty(trim($thrapist_name)))
            {$matter = str_replace("{therapistname}",$thrapist_name,$matter);}
            else
            {$matter = str_replace("{therapistname}",'',$matter);}
        }



        return '<div class="swiper-slide"><textarea class="form-control edi" name="edi'.$key.'" rows="10" cols="60">'.$matter.'</textarea></div>';
    }

    /**
     * Display Client.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('client_view')) {
            return abort(401);
        }
        $relations = [

            'appointments' => \App\Appointment::where('client_id', $id)->get(),
        ];
        $Docname="";
        $client = Client::findOrFail($id);
        if($client->doctor_id>0)
        {
            $doctor = \App\Doctor::findOrFail($client->doctor_id);
            if(!empty($doctor->id))
            {
                $Docname = $doctor->first_name." ".$doctor->last_name;
            }
        }
        $client->doctor_id = $Docname;

        return view('admin.clients.show', compact('client') + $relations);
    }


    /**
     * Remove Client from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        if (! Gate::allows('client_delete')) {
            return abort(401);
        }

        $client = Client::findOrFail($id);

        $client_appointments = Appointment::where('client_id','=',$client->id)->whereNull('deleted_at')->get();
        if(count($client_appointments) == 0)
        {
            $Userdelete =  \App\User::where('id','=',$client->user_id)->forceDelete();
            $client->forceDelete();
            return redirect()->route('admin.clients.index');
        }
        else
        {

            return redirect()->back()->withErrors("This Customer alreday have an appointments, so please delete that appointment and then delete that customer")->withInput();
        }


    }

    /**
     * Delete all selected Client at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('client_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Client::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->forcedelete();
            }
        }
    }
    public function massSendEmail(Request $request)
    {
        // echo "<pre>";print_r($request->input()); die;

        if ($request->input('ids') && $request->input('email_template_id') > 0) {
            $entries = Client::whereIn('id', $request->input('ids'))->get();
            //echo $request->input('email_template_id');
            $email_templates = EmailTemplate::where('id','=', $request->input('email_template_id'))->get();
            //echo "<pre>";print_r($email_templates);
            $matter = $email_templates[0]->email_content;
            $email_subject = $email_templates[0]->email_subject;
            $email_attachment_path='';
            $email_filename_attachment='';
            if(!empty($email_templates[0]->attachment))
            {
                $email_filename_attachment = $email_templates[0]->attachment;
                $email_attachment_path = public_path('/upload/'.$email_filename_attachment);

            }


            $final_arr = [];
            foreach ($entries as $key => $entry) {

                // $entry->delete();

//                if($request->input('email_template_id') == 6){
//
//                    $final_arr[$key] = $this->getAppointmentEmail($entry,$matter,$key);
//
//                }else if($request->input('email_template_id') == 18){
//                    $final_arr[$key] = $this->getAppointmentEmail($entry,$matter,$key);
//                }else{
//                    $clientname   = $entry->first_name ." ". $entry->last_name;
//                    if(!empty($clientname))
//                    {$matter = str_replace("{clientname}",$clientname,$matter);}
//                    else
//                    {$matter = str_replace("{clientname}",'',$matter);}
//                    $final_arr[$key] = '<div class="swiper-slide"><textarea  class="form-control edi" name="edi'.$key.'" rows="10" cols="60">'.$matter.'</textarea></div>';
//                }

             //   if($request->input('email_template_id') == 6){

                    $final_arr[$key] = $this->getAppointmentEmail($entry,$matter,$key);
               // echo "<pre>"; print_r($final_arr); die;
//                }else if($request->input('email_template_id') == 18){
//                    $final_arr[$key] = $this->getAppointmentEmail($entry,$matter,$key);
//                }else{
//                    $clientname   = $entry->first_name ." ". $entry->last_name;
//                    if(!empty($clientname))
//                    {$matter = str_replace("{clientname}",$clientname,$matter);}
//                    else
//                    {$matter = str_replace("{clientname}",'',$matter);}
//                    $final_arr[$key] = '<div class="swiper-slide"><textarea  class="form-control edi" name="edi'.$key.'" rows="10" cols="60">'.$matter.'</textarea></div>';
//                }


            }

            return response()->json(['final_arr' => $final_arr]);
        }

    }
    public function massSendEmailConfirmed(Request $request)
    {
        //echo "<pre>";print_r($request->input()); die;
        try{
            $matter_arr =  json_decode($request->input('matter_arr'),true);

            if ($request->input('ids') && $request->input('email_template_id') > 0) {
                $entries = Client::whereIn('id', $request->input('ids'))->get();
                //echo $request->input('email_template_id');
                $email_templates = EmailTemplate::where('id','=', $request->input('email_template_id'))->select(['email_subject'])->get();


                //  $matter = $email_templates[0]->email_content;
                $email_subject = $email_templates[0]->email_subject;



                foreach ($entries as $key => $entry) {
                    //  echo "<pre>"; print_r($entry); die;
                    // $entry->delete();
                    $clientemail = $entry->email;
                    $matter = $matter_arr[$key];

                   // $clientemail = 'engrsk60@gmail.com';
                    //echo "<pre>";print_r($matter); die;
                    Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
                        $message->to($clientemail)
                            ->from("info@praktijk-afspraken.nl")
                            ->subject($email_subject)
                            ->setBody($matter, 'text/html'); // for HTML rich messages
                    });
                }



                return response()->json(['message' => 'Email sent successfully','status' => true]);

            }
        }catch (\Exception $exception){

            return response()->json(['message' => 'Error sending email. Please refresh page or select another.','status' => false]);
        }


    }

    public function GetLocation(Request $request)
    {
        $returnArray=array();
        $clients = DB::table('clients')->where('id', '=', $request->client_id)->get();
        $services = DB::table('appointments')->where('client_id', '=', $request->client_id)->where('deleted_at','=',NULL)->orderBy('id','desc') ->limit(1)->get();
        $have_prev_invoice  = [];

        $have_prev = DB::select(DB::raw("select  `appointments`.id, `invoices`.`due_date`,DATEDIFF( CURRENT_DATE , `invoices`.`due_date`) as dif
                     from `appointments` inner join `invoices` on `invoices`.`appointment_id` = `appointments`.`id` where `appointments`.`client_id` = $request->client_id and `appointments`.`booking_status` = 'booking_unpaid' and `appointments`.`deleted_at` IS NULL HAVING dif >= 20"));

        if(count($have_prev) > 0){
            $have_prev_invoice['invoice_no'] =  $have_prev[0]->id;
            $have_prev_invoice['days'] =  $have_prev[0]->dif;
        }
        if(count($services) > 0)
        { $returnArray=array('employee_id'=>$services[0]->employee_id,'location_id'=>$services[0]->location_id,'service_id'=>$services[0]->service_id,'isappointment'=>true);
        }
        else {
            $returnArray=array('employee_id'=>0,'location_id'=>$clients[0]->location_id,'service_id'=>0,'isappointment'=>false);
        }

        if(count($have_prev_invoice) > 0){
            $returnArray['have_prev_invoice'] = $have_prev_invoice;
        }


        return $returnArray;
    }
    function generatePassword()
    {
        $random = str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890!$%^&!$%^&');
        $password = substr($random, 0, 10);
        return $password;

    }
    public function getallcontactsave()
    {

        // all tax class grab from Moneybrid and inserted into our database so when we create therapy we can add that tax class into and if appointment is booked we can sent that tax class id with that appointment and it will automatically created VAT class for that appointment on Moneybird
        /* $allcontactsCount = Moneybird::contact()->getAll();
         $totalNumber_contact = count($allcontactsCount);
         $loops = $totalNumber_contact / 20;*/
        $loops=15;
        for($i=1; $i<=($loops+1);$i++)
        {


            $array=array('per_page'=>20,'page'=>$i);
            $contacts = Moneybird::contact()->get($array);


            foreach ($contacts as $contact) {

                $name = $contact->firstname.' '.$contact->lastname;
                if($contact->email!='' && $contact->firstname!='' && $contact->lastname!='')
                {

                    $emailAlredayExits = User::where('email','=',$contact->email)->get();
                    $parent_id=0;
                    $parenEmail= $contact->email;


                    // echo count($emailAlredayExits);
                    if(count($emailAlredayExits) == 0)
                    {
                        $user = User::create([
                            'name' => $name,
                            'email' => $contact->email,
                            'role_id'  => 4,
                            'password' => bcrypt('password')
                        ]);
                        $user_id=$user->id;
                    }
                    else
                    {
                        $clients = Client::where('email','=',$contact->email)->get();
                        $contact->email."<br>";
                        $parent_id= $clients[0]->id;
                        $parenEmail=$contact->email;
                        $user_id=$emailAlredayExits[0]->id;
                    }


                    $clientAlreadyExits = Client::where('moneybird_contact_id', '=', $contact->id)->get();
                    echo count($clientAlreadyExits);
                    echo "<br>";
                    if(count($clientAlreadyExits) > 0)
                    {
                        echo "<pre>";print_r($clientAlreadyExits);
                        echo "<br>";
                        //dd($clientAlreadyExits);
                    }
                    if(count($clientAlreadyExits) == 0)
                    {
                        $client = Client::create([
                            'user_id' => $user_id,
                            'first_name' => $contact->firstname,
                            'last_name' => $contact->lastname,
                            'email' => $parenEmail,
                            'postcode' => $contact->zipcode,
                            'address' => $contact->address1,
                            'phone' => $contact->phone,
                            'company_name' => $contact->company_name,
                            'add_by' => '1',
                            'parent_id' => $parent_id,
                            'city_name' => $contact->city,
                            'moneybird_contact_id' => $contact->id
                        ]);
                    }
                }
            }
        }
    }

    public function autocompleteAdd(Request $request)
    {

        //$pc   = "1234AB";
        $pc   = $request->text;
        $hn   = $request->house_number;
        $tv   = "a";

        /*$getadrlnk  = 'https://bwnr.nl/postcode.php?pc='.urlencode($pc).'&hn='.urlencode($hn).'&tv=&tg=data';*/

        $getadrlnk  = 'https://bwnr.nl/postcode.php?pc='.urlencode($pc).'&hn='.urlencode($hn).'&tv=&tg=data&ak=z400@V7p0l(0$R45Jf';

        $result = file_get_contents($getadrlnk);
        $jsonArr=array();
        if ($result=="Geen resultaat.") {$jsonArr=array('message'=>'error');} else {
            $adres = explode(";",$result);
            $str  = $adres[0];
            $pl = $adres[1];
            $lat  = $adres[2];
            $lon  = $adres[3];
            $gm = $adres[4];
            $jsonArr=array('address'=>$str,"city"=>ucwords($pl),'message'=>'success');
            //echo $str." ".$pl;
            /*echo "
            straat    : $str<br>
            plaats    : $pl<br>
            lat     : $lat<br>
            lon     : $lon<br>
            googlemaps  : $gm<br>";*/
        }
        echo json_encode($jsonArr);
    }
    /* WITH OUT MONEYBIRD ID UPDATE DELETE SEND EMAIL*/

    public function editwithoutmoneybird($id)
    {
        if (! Gate::allows('client_edit')) {
            return abort(401);
        }
        $client = Client::findOrFail($id);
        $relations = [
            'locations' => \App\Location::get()
        ];
        return view('admin.clients.withoutmoneybirdedit', compact('client')+$relations);
    }

    public function updatewithoutmoneybird(UpdateClientsRequest $request, $id)
    {
        if (! Gate::allows('client_edit')) {
            return abort(401);
        }

        $client = Client::findOrFail($id);
        $user = User::where('id', $client->user_id)->get()->first();
        $name = $request->first_name.' '.$request->last_name;
        $user->name = $name;
        $user->email = $request->email;
        if(!empty($request->password))
        {$user->password =  bcrypt($request->password);}
        $user->save();
        /*Moneybird Existed*/
        if(!empty($request->comment))
        {
            $OldCommentArr = "";
            if(!empty($client->comment_log)) {  $OldCommentArr = $client->comment_log; }
            $userLogin = \Auth::user();
            $OldCommentArr .= "<li>".$userLogin->name." ".date("d-m-Y h:i:s")." ".$request->comment."</li>";
            $request['comment_log'] = $OldCommentArr;
        }



        $client->update($request->all());
        return redirect()->route('admin.clients.clientwithoutmoneybird');
    }


    /**
     * Display Client.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showwithoutmoneybird($id)
    {
        if (! Gate::allows('client_view')) {
            return abort(401);
        }
        $relations = [
            'appointments' => \App\Appointment::where('client_id', $id)->get(),
        ];

        $client = Client::findOrFail($id);

        return view('admin.clients.withoutmoneybirdshow', compact('client') + $relations);
    }


    /**
     * Remove Client from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroywithoutmoneybird($id)
    {
        if (! Gate::allows('client_delete')) {
            return abort(401);
        }
        $appointmentCnt = Appointment::where('client_id','=',$id)->get();
        if(count($appointmentCnt)== 0)
        {
            $client = Client::findOrFail($id);
            $Userdelete =  \App\User::where('id','=',$client->user_id)->forceDelete();
            $client->forceDelete();
            return redirect()->route('admin.clients.clientwithoutmoneybird');
        }
        else
        {   $client = Client::findOrFail($id);
            $fname = $client->first_name;
            $lname = $client->last_name;
            $name = $fname." ".$lname;
            return redirect()->route('admin.clients.clientwithoutmoneybird')->with('msg', $name . ' alreday associate with appointments so first delete that appointment');
        }

    }

    /**
     * Delete all selected Client at once.
     *
     * @param Request $request
     */
    public function massDestroywithoutmoneybird(Request $request)
    {
        if (! Gate::allows('client_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Client::whereIn('id', $request->input('ids'))->get();
            $appointmentCnt = Appointment::where('client_id','=',$request->input('ids'))->get();
            if(count($appointmentCnt)== 0)
            {
                $client = Client::findOrFail($id);
                $client->forceDelete();
            }
            else
            {
                foreach ($entries as $entry) {
                    $entry->forceDelete();
                }
            }
        }
    }

    /*END WITHOUT MONEYBIRD*/

}
