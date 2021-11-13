<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Crypt;
use App\Appointment;
use App\Logdata;
use Carbon\Carbon;
use App\EmailTemplate;
use App\Client;
use App\Employee;
use App\Service;
use App\Location;
use App\Invoice;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAppointmentsRequest;
use App\Http\Requests\Admin\UpdateAppointmentsRequest;
use Moneybird;
use \App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Date;
use DB;
use Mail;
use DataTables;
use PDF;
use Mollie\Laravel\Facades\Mollie;
use Intervention\Image\ImageManagerStatic as Image;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of Appointment.
     * @return \Illuminate\Http\Response
     */

    public function exportexcel(Request $request)
    {
        $emailTemplate=array();
        $user = \Auth::user();
        $name = $request->searchByName;
        $month = $request->searchByMonth;
        $searchByYear = $request->SearchByYear;
        if(empty($searchByYear)){ $searchByYear = date("Y"); }
        $employee_id = 0;
        if($user->role_id == 3)
        {
            $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
            if(!empty($therapist[0]->id)) { $employee_id = $therapist[0]->id;}
        }
        $appointmentCnt = Appointment::select('appointments.id','booking_status','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
            ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
            ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
            ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
            ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
            ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id');

        if($employee_id>0)
        { $appointmentCnt = $appointmentCnt->where('employee_id','=',$employee_id); }
        if(!empty($month))
        { $appointmentCnt=$appointmentCnt->whereMonth('start_time','=',$month); }
        if(!empty($searchByYear))
        { $appointmentCnt=$appointmentCnt->whereYear('start_time','=',$searchByYear); }


        if(!empty($name))
        { $appointmentCnt = $appointmentCnt->Where('employees.first_name', 'like', '%'.$name.'%') ->orwhere('employees.last_name', 'like', '%'.$name.'%'); }

        $appointmentCnt = $appointmentCnt->orderBy('start_time','asc')->get();

        $timestamp = time();
        $filename = 'Export_excel_' . $timestamp . '.xls';
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Pragma: no-cache');
        header('Expires: 0');



        $Strtable="";
        foreach ($appointmentCnt as $key => $appointments) {

            $first_name = isset($appointments->client->first_name) ? $appointments->client->first_name : '';
            $last_name = isset($appointments->client->last_name) ? $appointments->client->last_name : '';
            $name = $first_name." ".$last_name;
            $CustomerName = isset($name) ? $name : $appointments->customer_name;


            $location_name = isset($appointments->location->location_name) ? $appointments->location->location_name : $appointments->location_name;


            $therapistFName =  isset($appointments->employee->first_name) ? $appointments->employee->first_name : '';
            $therapistLName =  isset($appointments->employee->last_name) ? $appointments->employee->last_name : '';
            $therapistName = '';
            $therapistName = $therapistFName." ".$therapistLName;
            $therapistNamePrint =  !empty(trim($therapistName)) ? $therapistName : $appointments->therapist_name;
            $Strtable = $Strtable.
                "<tr>
              <td>".$appointments->booking_status."</td>
              <td>".date('d M Y H:i',strtotime($appointments->start_time))."</td>
              <td>".$appointments->price."</td>
              <td>".$CustomerName."</td>
              <td>".$location_name."</td>
              <td>".$therapistNamePrint."</td>
            </tr>";
		}
        echo " <table><tr>
              <th>Status</th>
              <th>start time</th>
              <th>price</th>
              <th>customer name</th>
              <th>location</th>
              <th>therapist name</th>
            </tr>".$Strtable."</table>";
        exit;
    }

    public function invoicedatatable(Request $request)
    {

        $Arrsearchfld = array();
        if(!empty($_REQUEST['form']))
        {
            foreach ($_REQUEST['form'] as $key => $value)
            {
                if(($value['name']!='_token') && !empty($value['value']))
                { $Arrsearchfld[$value['name']]  =$value['value']; }
            }
        }

        $user = \Auth::user();
        if(!empty($request->start)) { $start = $request->start; } else { $start = 0;}
        if(!empty($request->length)){ $length = $request->length; } else { $length = 100;}
        $employee_id=0;
        if($user->role_id == 3)
        {
            $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
            $employee_id = $therapist[0]->id;
        }
        $appointments = Appointment::select(
            "invoices.id as invoices_id", "appointments.id as appointments_id",
            DB::raw("CONCAT(invoices.inv_fyear,'-',invoices.inv_number) AS invoice_number"),'invoices.display_inv_no','invoices.inv_date','invoices.prd_description', 'invoices.price','invoices.baseamount','invoices.netamount','invoices.invoice_cancled', DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"), 'clients.city_name', 'locations.location_name','appointments.start_time','appointments.finish_time', 'appointments.status','status_rm_exp','appointments.booking_status','paid_waya','clients.phone','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id')

            ->join('invoices', 'invoices.appointment_id', '=', 'appointments.id')
            ->join('clients', 'clients.id', '=', 'appointments.client_id')
            ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
            ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
            ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
            ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id');
        if($employee_id>0)
        { $appointments = $appointments->where('appointments.employee_id','=',$employee_id); }
        foreach ($Arrsearchfld as $key => $value)
        {
            if($key=='invoice_no')
            {
                $appointments = $appointments->where("invoices.display_inv_no",'LIKE','%'.$value.'%');
                /*$appointments = $appointments->where(function ($query) use ($value) {
                      $query = $query->where(DB::raw("CONCAT(invoices.inv_fyear,'-',invoices.inv_number)"),'LIKE',"%$value%");
                    });*/
            }

            elseif($key=='customer_name')
            {
                $appointments = $appointments->where(function ($query) use ($value) {
                    $query = $query->where(DB::raw("CONCAT(clients.first_name,' ',clients.last_name)"),'LIKE',"%$value%");
                });
                /*$appointments = $appointments->where(function ($query) use ($value) { $query->where('clients.first_name', 'LIKE', "%$value%")
                                ->orWhere('clients.last_name', 'LIKE', "%$value%");
                    });*/
            }
            elseif($key=='inv_status')
            {
                if($value=="E")
                {
                    $appointments = $appointments->where('appointments.status_rm_exp', '=', 'E')->where('appointments.status', '=', 'booking_confirmed')->where('booking_status', '=', 'booking_unpaid');
                }
                elseif($value=="Cash" || $value=="Bank" || $value=="Mollie")
                {
                    $appointments = $appointments->where('appointments.booking_status', '=', 'booking_paid_pin')->where('appointments.paid_waya', '=', $value);
                }
                elseif($value=="booking_paid_pin")
                {
                    $appointments = $appointments->where('appointments.booking_status', '=', 'booking_paid_pin')
                        ->Where(
                            function ($appointments) use ($value){
                                return $appointments->Where('paid_waya', '=',  '')
                                    ->orwhere('paid_waya', '=', NULL);
                            });
                }
                elseif($value=="booking_paid_pin" || $value=="booking_unpaid" || $value=="partial_paid" || $value=="booking_confirmed")
                {
                    $appointments = $appointments->where('booking_status', '=', $value);
                }
            }
            elseif($key=='customer_phone')
            { $appointments = $appointments->where('clients.phone', 'LIKE', "%".$value."%"); }
            elseif($key=='therapist_phone')
            { $appointments = $appointments->where('employees.phone', 'LIKE', "%".$value."%"); }

            elseif($key=='customer_email')
            { $appointments = $appointments->where('clients.email', 'LIKE', "%".$value."%"); }

            elseif($key=='therapist_email')
            { $appointments = $appointments->where('employees.email', 'LIKE', "%".$value."%"); }


            elseif($key=='therapist_name')
            {
                $appointments = $appointments->where(function ($query) use ($value) {
                    $query = $query->where(DB::raw("CONCAT(employees.first_name,' ',employees.last_name)"),'LIKE',"%$value%");
                });
            }
            elseif($key=='therapy_name')
            { $appointments = $appointments->where('services.name','like','%'.$value.'%'); }
        }
        if(!empty($Arrsearchfld['original_unpaid']))
        {
            $appointments = $appointments->where('appointments.booking_status','!=','appointments.original_booking_status');

        }

        if(!empty($Arrsearchfld['invoice_date_from']) && !empty($Arrsearchfld['invoice_date_to']))
        {
            $date1 = date($Arrsearchfld['invoice_date_from']);
            $date2 = date($Arrsearchfld['invoice_date_to']);

            $appointments = $appointments->where(function ($query) use ($date1,$date2) { $query->whereBetween('invoices.inv_date' ,[$date1,$date2]);
            });
            /*$appointments = $appointments->whereBetween('invoices.inv_date' ,[$Arrsearchfld['invoice_date_from'],$Arrsearchfld['invoice_date_to']]);*/
        }

        $appointmentCnt  =$appointments->get();
        $appointments = $appointments->offset($start)
            ->limit($length)->orderBy('invoices.inv_fyear','desc')->orderBy('invoices.inv_number','desc');


        $cntClient = $appointmentCnt;
        return Datatables::of($appointments)
            ->setRowClass(function ($appointments) {
                if($appointments->booking_status=="booking_paid_pin")
                { return" booking_paid_pin "; }
                elseif($appointments->booking_status=="booking_unpaid")
                {
                    $ExpRem=" booking_unpaid ";
                    if($appointments->status=="booking_confirmed")
                    {
                        if($appointments->status_rm_exp=='E')
                        { $ExpRem = "booking_expired";}
                        elseif($appointments->status_rm_exp=='R')
                        { $ExpRem = "booking_remindered";}
                    }
                    return $ExpRem;
                }
            })
            ->editColumn('checkbox', function($appointments) { return $appointments->id;})
            ->editColumn('invoice_number', function($appointments) { return $appointments->display_inv_no; })
            ->editColumn('inv_date', function($appointments) {
                return date('d M Y',strtotime($appointments->inv_date));
            })
            /*->editColumn('price', function( $appointments) {return "€ ".$appointments->price; })*/
            ->editColumn('netamount', function( $appointments) {return "€ ".$appointments->netamount; })
            ->editColumn('customer_name', function($appointments) {
                $first_name = isset($appointments->client->first_name) ? $appointments->client->first_name : '';
                $last_name = isset($appointments->client->last_name) ? $appointments->client->last_name : '';
                $name = $first_name." ".$last_name;
                return isset($name) ? $name : $appointments->customer_name;
            })
            ->editColumn('booking_status', function($appointments) {
                if($appointments->status=="booking_confirmed")
                {
                    return "Confirmed";
                }
                elseif($appointments->status=="pending")
                { return "Pending";}
                elseif($appointments->status=="booking_cancled")
                { return "Cancled";}
                else
                { return $appointments->status; }
            })

            ->editColumn('status', function( $appointments) {
                if($appointments->booking_status=="booking_paid_pin")
                {
                    if($appointments->paid_waya=="")
                    { return "Paid Pin "; }
                    else
                    { return"Paid ".$appointments->paid_waya; }
                }
                elseif($appointments->booking_status=="booking_unpaid")
                {
                    $ExpRem="";
                    if($appointments->status=="booking_confirmed")
                    {
                        if($appointments->status_rm_exp=='E')
                        { $ExpRem = "(Expired)";}
                        elseif($appointments->status_rm_exp=='R')
                        { $ExpRem = "(Reminder)";}
                    }

                    return "Unpaid ".$ExpRem;
                }
                elseif($appointments->booking_status=="partial_paid")
                { return"Partial Paid"; }
                else
                { return $appointments->booking_status;  }



            })
            ->editColumn('therapy_name', function($appointments) {
                return isset($appointments->service->name) ? $appointments->service->name : $appointments->service_name;
            })
            ->editColumn('start_time', function($appointments) {
                return date('d M Y H:i',strtotime($appointments->start_time)) ;
            })
            ->editColumn('finish_time', function($appointments) {
                return date('d M Y H:i',strtotime($appointments->finish_time));
            })
            ->editColumn('therapist_name', function($appointments) {
                $therapistFName =  isset($appointments->employee->first_name) ? $appointments->employee->first_name : '';
                $therapistLName =  isset($appointments->employee->last_name) ? $appointments->employee->last_name : '';
                $therapistName = '';
                $therapistName = $therapistFName." ".$therapistLName;
                return !empty(trim($therapistName)) ? $therapistName : $appointments->therapist_name;
            })


            /* ->editColumn('phone', function($appointments) {
               return isset($appointments->client) ? $appointments->client->phone : $appointments->phone;
             })

             ->editColumn('location', function($appointments) {
               return isset($appointments->location->location_name) ? $appointments->location->location_name : $appointments->location_name;
             })

             ->editColumn('room_no', function($appointments) {
                       return isset($appointments->room) ? $appointments->room->room_name : $appointments->room_name;
                   })
             ->editColumn('created_by', function($appointments) {

                       return getUsername($appointments->add_by) ;
                   })
             ->editColumn('client_email_verified', function($appointments) {
                        if(isClientVerified($appointments->client_id)) { $verified = "Yes"; } else {$verified ="No";}
                       return $verified;
                   })
             ->editColumn('moneybird_status', function($appointments) {
                       return $appointments->booking_status;
                   })*/

            ->addColumn('action', function($appointments) {
                $StrApoiment = '<div class="btn-group"><a href="'.route('admin.appointments.show',[$appointments->appointments_id]).'" class="btn btn-xs btn-primary" title="View"><i class="fa fa-eye"></i></a>';
                $user = \Auth::user();
                if(( $appointments->booking_status == 'booking_unpaid' ) and ($user->role_id == 1))
                {
                    $StrApoiment = $StrApoiment.'<a 
                href="appointments/changeinvoicestatus/'.$appointments->appointments_id.'/cash_paid" 
                class="btn btn-xs btn-warning" title="Cash Paid"><i class="fa fa-money"></i></a>';
                    $StrApoiment = $StrApoiment.'<a 
                href="appointments/changeinvoicestatus/'.$appointments->appointments_id.'/bank_paid" 
                class="btn btn-xs btn-primary" title="Bank Paid"><i class="fa fa-bank"></i></a>';

                }

                if($user->role_id == 1)
                {
                    $StrApoiment = $StrApoiment.'  <a title="Delete" onclick="javascript: return confirm(\'Are you sure to Delete this Invoice?\')" href="'.route('admin.appointment_destroy',[$appointments->id]).'" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>';
                }
                elseif($user->role_id == 3)
                {
                    if(!( $appointments->booking_status == 'booking_paid_pin' || $appointments->booking_status == 'booking_unpaid'))
                    {
                        if( empty($appointments->status) || $appointments->status == 'pending' || $appointments->status == 'booking_confirmed')
                        {
                            $StrApoiment = $StrApoiment.'  <a title="Delete" onclick="javascript: return confirm(\'Are you sure to Delete this apointment?\')" href="'.route('admin.appointment_destroy',[$appointments->id]).'" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>';
                        }
                    }
                }
                if($user->role_id == 1 && ($appointments->status != 'booking_cancled'))
                {
                    $StrApoiment = $StrApoiment.'  <a onclick="javascript: return confirm(\'Are you sure to cancle this apointment?\')" title="Cancle" href="'.route('admin.appointment_cancle',[$appointments->appointments_id]).'" class="btn btn-xs btn-danger"><i class="fa fa-times-circle"></i></a>';
                }

                return $StrApoiment.'</div>';
            })->with(["recordsTotal" => $cntClient,"recordsFiltered" => $cntClient])->skipPaging()->toJson();
    }

    public function upcoming_bookings(Request $request){



        $upcoming_bookings = DB::table('upcoming_bookings')->orderBy('id','DESC');

        if($request['sort_by_date']){
            $upcoming_bookings->whereRaw('Year(invoice_date) = '.$request['sort_by_date']);
        }

        if($request['status']){
            $upcoming_bookings->where('status',$request['status']);
        }

        $upcoming_bookings = $upcoming_bookings->paginate(10);
      //  $upcoming_bookings = $upcoming_bookings->toSql();

    // echo "<pre>"; print_r($upcoming_bookings); die;

        return view('admin.appointments.upcoming_bookings', ['table' => view('admin.appointments.view_incoming_invoice_list',
            ['upcoming_bookings' => $upcoming_bookings]),'sort_by_date' => $request['sort_by_date'],'status' => $request['status']]);

    }


    public function create_upcoming_booking(){

        return view('admin.appointments.create_upcoming_booking');

    }

    public function edit_upcoming_booking($id){
        $upcoming_booking = DB::table('upcoming_bookings')->where('id',$id)->first();
        return view('admin.appointments.edit_upcoming_booking',['upcoming_booking' => $upcoming_booking]);
    }

    public function post_create_upcoming_booking(Request $request){

        $data = [
            'title' => $request['title'],
            'comment' => $request['comment'],
            'status' => $request['status'],
            'created_at' => date('Y-m-d'),
        ];

        if(!$request['amount']){
            $data['amount'] = 0;
        }else{
            $data['amount'] = $request['amount'];
        }

        if(!empty($request['invoice_date'])){
            $data['invoice_date'] = date('Y-m-d',strtotime($request['invoice_date']));
        }

        if($request->file('invoice_pdf')){

            $invoice_pdf = 'pdf_'.time().'.'.$request->invoice_pdf->getClientOriginalExtension();
            $request->invoice_pdf->move(public_path('/upload/'), $invoice_pdf);
            $data['invoice_pdf'] = $invoice_pdf;




        }
        if($request->file('invoice_image')){
            $invoice_image = 'image_'.time().'.'.$request->invoice_image->getClientOriginalExtension();
            $request->invoice_image->move(public_path('/upload/'), $invoice_image);
            $data['invoice_image'] = $invoice_image;

            //Making thumbnail
            $img = Image::make(public_path('/upload/').'/'.$invoice_image);
            $img->resize(100, 100);
            $img->save(public_path('/upload/').'/'.'thumb_'.$invoice_image, 60);
        }


         DB::table('upcoming_bookings')->insert($data);

        return redirect()->route('admin.upcoming.booking')->with(['message' => 'Upcoming Booking added!!']);

    }

    public function post_edit_upcoming_booking(Request $request){

        $data = [
           // 'id' => $request['id'],
            'title' => $request['title'],
            'comment' => $request['comment'],
            'status' => $request['status']
        ];
        if(!$request['amount']){
            $data['amount'] = 0;
        }else{
            $data['amount'] = $request['amount'];
        }
        if(!empty($request['invoice_date'])){
            $data['invoice_date'] = date('Y-m-d',strtotime($request['invoice_date']));
        }
        if($request->file('invoice_pdf')){

            $invoice_pdf = 'pdf_'.time().'.'.$request->invoice_pdf->getClientOriginalExtension();
            $request->invoice_pdf->move(public_path('/upload/'), $invoice_pdf);
            $data['invoice_pdf'] = $invoice_pdf;

        }
        if($request->file('invoice_image')){
            $invoice_image = 'image_'.time().'.'.$request->invoice_image->getClientOriginalExtension();
            $request->invoice_image->move(public_path('/upload/'), $invoice_image);
            $data['invoice_image'] = $invoice_image;
        }


        DB::table('upcoming_bookings')->where('id',$request['id'])->update($data);

        return redirect()->route('admin.upcoming.booking')->with(['message' => 'Upcoming Booking updated!!']);

    }
    public function delete_upcoming_booking($id){
        DB::table('upcoming_bookings')->where('id',$id)->delete();
        return redirect()->back();
    }

    public function datatables(Request $request)
    {
       // echo "<pre>"; print_r($request['searchByStatus']); die;
        $emailTemplate=array();
        $user = \Auth::user();
        $start = $request->start;
        $length = $request->length;
        $searchByStatus = $request['searchByStatus'];

        if($user->role_id == 3)
        {
            $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
            $employee_id = $therapist[0]->id;


            if(empty($request->input('search.value')))
            { //$appointment->paid_waya
                /*$appointments = Appointment::where('employee_id','=',$employee_id)->offset($start)
                          ->limit($length)->orderBy('id','desc')->get();*/


                if($request->input('searchByGender')=='by_email')
                {
                    $name=$request->input('searchByName');

                    $appointments = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->where('employee_id','=',$employee_id)
                        ->Where('clients.email', 'like', '%'.$name.'%');

                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }
                    $appointments = $appointments->orwhere('employees.email', 'like', '%'.$name.'%')
                        ->offset($start)
                        ->limit($length)->orderBy('id','desc');

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->where('employee_id','=',$employee_id);

                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }

                    $appointmentCnt = $appointmentCnt->Where('clients.email', 'like', '%'.$name.'%')
                        ->orwhere('employees.email', 'like', '%'.$name.'%')
                        ->orderBy('id','desc')->get();

                }
                elseif($request->input('searchByGender')=='by_customer_name')
                {
                    $name=$request->input('searchByName');

                    $appointments = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->where('employee_id','=',$employee_id);
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }

                    $appointments = $appointments->Where('clients.first_name', 'like', '%'.$name.'%')
                        ->orwhere('clients.last_name', 'like', '%'.$name.'%')
                        ->offset($start)
                        ->limit($length)->orderBy('id','desc');

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->where('employee_id','=',$employee_id);

                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }


                    $appointmentCnt = $appointmentCnt->Where('clients.first_name', 'like', '%'.$name.'%')
                        ->orwhere('clients.last_name', 'like', '%'.$name.'%')
                        ->orderBy('id','desc')->get();


                }
                elseif($request->input('searchByGender')=='by_therapy_name')
                {
                    $name=$request->input('searchByName');

                    $appointments = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->where('employee_id','=',$employee_id);
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }

                    $appointments = $appointments->Where('employees.first_name', 'like', '%'.$name.'%')
                        ->orwhere('employees.last_name', 'like', '%'.$name.'%')
                        ->offset($start)
                        ->limit($length)->orderBy('id','desc');

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->where('employee_id','=',$employee_id);

                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }
                    $appointmentCnt = $appointmentCnt->Where('employees.first_name', 'like', '%'.$name.'%')
                        ->orwhere('employees.last_name', 'like', '%'.$name.'%')
                        ->orderBy('id','desc')->get();


                }
                elseif($request->input('searchByGender')=='by_therapist_email')
                {
                    $name=$request->input('searchByName');

                    $appointments = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id');
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }
                    $appointments = $appointments->where('employee_id','=',$employee_id)
                        ->Where('employees.email', 'like', '%'.$name.'%')
                        ->orwhere('clients.email', 'like', '%'.$name.'%')
                        ->offset($start)
                        ->limit($length)->orderBy('id','desc');

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->where('employee_id','=',$employee_id);
                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }

                    $appointmentCnt= $appointmentCnt->Where('employees.email', 'like', '%'.$name.'%')
                        ->orwhere('clients.email', 'like', '%'.$name.'%')
                        ->orderBy('id','desc')->get();


                }
                elseif ($request->input('searchByGender')=='by_therapist_name_month') {

                    $name=$request->input('searchByName');
                    $month=$request->input('searchByMonth');
                    if(!empty($request->input('searchByYear')))
                    { $searchByYear = $request->input('searchByYear'); }
                    else
                    { $searchByYear = date("Y"); }

                    $appointments = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->where('employee_id','=',$employee_id);
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }
                    $appointments= $appointments->whereMonth('start_time', '=', $month)
                        ->whereYear('start_time', '=', $searchByYear)
                        /*->Where(
                                function ($appointments) use ($name){
                                  return $appointments->Where('employees.first_name', 'like', '%'.$name.'%')
                                  ->orwhere('employees.last_name', 'like', '%'.$name.'%');
                                })*/

                        ->offset($start)
                        ->limit($length)
                        ->orderBy('id','DESC');
                      //  ->orderBy('price','DESC')
                      //  ->orderBy('start_time', 'DESC');

                       // ->orderBy('id','desc');

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->where('employee_id','=',$employee_id);
                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }

                    $appointmentCnt = $appointmentCnt->whereMonth('start_time', '=', $month)
                        ->whereYear('start_time', '=', $searchByYear)
                        /*->Where('employees.email', 'like', '%'.$name.'%')
                        ->orwhere('clients.email', 'like', '%'.$name.'%')*/
                        ->orderBy('start_time','asc')->get();

                }
                else
                {
                    $appointments = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->where('employee_id','=',$employee_id);
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }

                    $appointments = $appointments ->offset($start)
                        ->limit($length)->orderBy('id','desc');

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id');
                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }
                    $appointmentCnt = $appointmentCnt->where('employee_id','=',$employee_id)
                        ->orderBy('id','desc')->get();
                }

                $appointmentCnt = count($appointmentCnt);

            }

        }
        else
        {

            if(empty($request->input('search.value')))
            {

                if($request->input('searchByGender')=='by_email')
                {
                    $name=$request->input('searchByName');

                    $appointments = Appointment::select('appointments.id','booking_status','paid_waya','start_time','status_rm_exp','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }
                    $appointments = $appointments->Where('clients.email', 'like', '%'.$name.'%')
                        ->orwhere('employees.email', 'like', '%'.$name.'%')
                        ->offset($start)
                        ->limit($length)->orderBy('id','desc');

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','paid_waya','start_time','status_rm_exp','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }
                    $appointmentCnt = $appointmentCnt->Where('clients.email', 'like', '%'.$name.'%')
                        ->orwhere('employees.email', 'like', '%'.$name.'%')
                        ->orderBy('id','desc')->get();

                }
                elseif($request->input('searchByGender')=='by_therapist_email')
                {
                    $name=$request->input('searchByName');

                    $appointments = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }

                    $appointments= $appointments->Where('employees.email', 'like', '%'.$name.'%')
                        ->orwhere('clients.email', 'like', '%'.$name.'%')
                        ->offset($start)
                        ->limit($length)->orderBy('id','desc');

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }


                    $appointmentCnt = $appointmentCnt->Where('employees.email', 'like', '%'.$name.'%')
                        ->orwhere('clients.email', 'like', '%'.$name.'%')
                        ->orderBy('id','desc')->get();


                }
                elseif ($request->input('searchByGender')=='by_therapist_name_month') {

                    $name=$request->input('searchByName');

                    $month=$request->input('searchByMonth');
                    if(!empty($request->input('searchByYear')))
                    { $searchByYear = $request->input('searchByYear'); }
                    else
                    { $searchByYear = date("Y"); }

                    $appointments = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }

                    $appointments = $appointments->whereMonth('start_time', '=', $month)
                        ->whereYear('start_time', '=', $searchByYear)
                        ->Where(
                            function ($appointments) use ($name){
                                return $appointments->Where('employees.first_name', 'like', '%'.$name.'%')
                                    ->orwhere('employees.last_name', 'like', '%'.$name.'%');
                            })
                        /*->Where('employees.first_name', 'like', '%'.$name.'%')
                        ->orwhere('employees.last_name', 'like', '%'.$name.'%')*/
                        ->offset($start)->limit($length)
                        ->orderBy('start_time','asc');
                      //  ->orderBy('price','DESC')
                      //  ->orderBy('start_time', 'DESC');
                     //   ->orderBy('start_time','asc')/*->orderBy('id','asc')*/;

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','status_rm_exp','paid_waya','start_time','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }

                    $appointmentCnt = $appointmentCnt->whereMonth('start_time', '=', $month)
                        ->whereYear('start_time', '=', $searchByYear)
                        ->Where(
                            function ($appointmentCnt) use ($name){
                                return $appointmentCnt->Where('employees.first_name', 'like', '%'.$name.'%')
                                    ->orwhere('employees.last_name', 'like', '%'.$name.'%');
                            })
                        ->orderBy('start_time','asc')->get();
                    /*->orderBy('id','asc')*/
                }
                elseif($request->input('searchByGender')=='by_customer_name')
                {
                    $name=$request->input('searchByName');

                    $appointments = Appointment::select('appointments.id','booking_status','paid_waya','start_time','status_rm_exp','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }

                    $appointments = $appointments->Where('clients.first_name', 'like', '%'.$name.'%')
                        ->orwhere('clients.last_name', 'like', '%'.$name.'%')
                        ->offset($start)
                        ->limit($length)->orderBy('id','desc');

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','paid_waya','start_time','status_rm_exp','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }
                    $appointmentCnt = $appointmentCnt->Where('clients.first_name', 'like', '%'.$name.'%')
                        ->orwhere('clients.last_name', 'like', '%'.$name.'%')
                        ->orderBy('id','desc')->get();


                }
                elseif($request->input('searchByGender')=='by_therapy_name')
                {
                    $name=$request->input('searchByName');

                    $appointments = Appointment::select('appointments.id','booking_status','paid_waya','start_time','status_rm_exp','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }
                    $appointments = $appointments->Where('employees.first_name', 'like', '%'.$name.'%')
                        ->orwhere('employees.last_name', 'like', '%'.$name.'%')
                        ->offset($start)
                        ->limit($length)->orderBy('id','desc');

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','paid_waya','start_time','status_rm_exp','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }
                    $appointmentCnt  = $appointmentCnt->Where('employees.first_name', 'like', '%'.$name.'%')
                        ->orwhere('employees.last_name', 'like', '%'.$name.'%')
                        ->orderBy('id','desc')->get();


                }
                else
                {

                    $appointments = Appointment::select('appointments.id','booking_status','paid_waya','start_time','status_rm_exp','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointments = $appointments->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointments = $appointments->where('appointments.status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){

                        $appointments = $appointments->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointments = $appointments->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointments = $appointments->where('appointments.paid_waya','Cash');
                    }

                    $appointments  = $appointments->offset($start)
                        ->limit($length)->orderBy('id','desc');

                    $appointmentCnt = Appointment::select('appointments.id','booking_status','paid_waya','start_time','status_rm_exp','finish_time','price',DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'clients.phone','locations.location_name as location_name','services.name as service_name',DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"),'rooms.room_name as room_name','appointments.add_by','client_id','appointments.status')
                        ->leftJoin('clients', 'clients.id', '=', 'appointments.client_id')
                        ->leftJoin('employees', 'employees.id', '=', 'appointments.employee_id')
                        ->leftJoin('services', 'services.id', '=', 'appointments.service_id')
                        ->leftJoin('locations', 'locations.id', '=', 'appointments.location_id')
                        ->leftJoin('rooms', 'rooms.id', '=', 'appointments.room_id')
                        ->whereRaw("1 = 1");
                    if($searchByStatus == 'booking_unpaid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_unpaid");
                    }
                    if($searchByStatus == 'mollie_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya',"Mollie");
                    }
                    if($searchByStatus == 'booking_confirmed'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_confirmed");
                    }
                    if($searchByStatus == 'booking_pending'){
                        $appointmentCnt = $appointmentCnt->where('appointments.status',"booking_pending");
                    }
                    if($searchByStatus == 'paid_pin'){
                        $appointmentCnt = $appointmentCnt->where('appointments.booking_status',"booking_paid_pin");
                    }
                    if($searchByStatus == 'bank_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Bank');
                    }
                    if($searchByStatus == 'cash_paid'){
                        $appointmentCnt = $appointmentCnt->where('appointments.paid_waya','Cash');
                    }
                    $appointmentCnt = $appointmentCnt ->orderBy('id','desc')->get();
                }

                $appointmentCnt = count($appointmentCnt);


            }
            else
            {


            }

        }

        $cntClient = $appointmentCnt;
        return Datatables::of($appointments)

            ->editColumn('checkbox', function($appointments) { return $appointments->id;})
            ->editColumn('status', function( $appointments) {
                $bookingstatus ='';



                $booking_status= array('pending'=>'Pending','booking_confirmed'=>'Booking Confirm','booking_paid_pin'=>'Booking Paid Pin','cash_paid'=>'Cash Paid', 'booking_unpaid'=>'Booking Unpaid');

                $statusList='';

                foreach($booking_status as $key => $booking_statu)
                {
                    $selected='';
                    if($appointments->status == $key)
                    {$selected='selected';}
                    $statusList.='<option value="'.$key.'" '.$selected.' >'.$booking_statu.'</option>';
                }


                if($appointments->booking_status == 'booking_confirmed' || empty($appointments->booking_status))
                {
                    $bookingstatus ='<select id="appointment_status" name="appointment_status" class="form-control select2 appointment_status" required rel="'.$appointments->id.'">
                                        <option value="">Please select</option>
                                        "'.$statusList.'"
                                    </select>';
                }
                else
                {
                    if($appointments->booking_status=="booking_paid_pin")
                    {
                        if($appointments->paid_waya=="")
                        { return "Paid Pin "; }
                        else
                        { return"Paid ".$appointments->paid_waya; }
                    }
                    elseif($appointments->booking_status=="booking_unpaid")
                    { return"Unpaid"; }
                    elseif($appointments->booking_status=="partial_paid")
                    { return"Partial Paid"; }
                    else
                    { return $appointments->booking_status;  }
                }


                return  $bookingstatus;
            })
            ->editColumn('start_time', function($appointments) {
                $price = date('d M Y H:i',strtotime($appointments->start_time)) ;
                return  $price;
            })
            ->editColumn('finish_time', function($appointments) {
                return date('d M Y H:i',strtotime($appointments->finish_time));
            })
            ->editColumn('price', function( $appointments) {return "€ ".$appointments->price; })
            ->editColumn('therapist_price', function ($appointments) {
                $price_fee = DB::table('price_fee')->get();
                $price_arr = [];
                $fee_arr = [];

                foreach ($price_fee as $key => $value) {
                    $price_arr[$key] = $value->price;
                    $fee_arr[$value->price] = $value->fee;
                }
                $therapist_price = 0;
                $in_arr = ['booking_paid_pin', 'cash_paid'];
                if (in_array($appointments->price, $price_arr) && in_array($appointments->booking_status, $in_arr)) {

                    $therapist_price = $fee_arr[$appointments->price];
                }

                return "€ " . $therapist_price;
            })
            ->editColumn('customer_name', function($appointments) {
                $first_name = isset($appointments->client->first_name) ? $appointments->client->first_name : '';
                $last_name = isset($appointments->client->last_name) ? $appointments->client->last_name : '';
                $name = $first_name." ".$last_name;

                return isset($name) ? $name : $appointments->customer_name;
            })
            ->editColumn('phone', function($appointments) {
                return isset($appointments->client) ? $appointments->client->phone : $appointments->phone;
            })
            ->editColumn('location', function($appointments) {
                return isset($appointments->location->location_name) ? $appointments->location->location_name : $appointments->location_name;
            })
            ->editColumn('therapy_name', function($appointments) {
                return isset($appointments->service->name) ? $appointments->service->name : $appointments->service_name;
            })
            ->editColumn('therapist_name', function($appointments) {
                $therapistFName =  isset($appointments->employee->first_name) ? $appointments->employee->first_name : '';
                $therapistLName =  isset($appointments->employee->last_name) ? $appointments->employee->last_name : '';
                $therapistName = '';
                $therapistName = $therapistFName." ".$therapistLName;
                return !empty(trim($therapistName)) ? $therapistName : $appointments->therapist_name;
            })
            ->editColumn('room_no', function($appointments) {
                return isset($appointments->room) ? $appointments->room->room_name : $appointments->room_name;
            })
            ->editColumn('created_by', function($appointments) {

                return getUsername($appointments->add_by) ;
            })
            ->editColumn('client_email_verified', function($appointments) {
                if(isClientVerified($appointments->client_id)) { $verified = "Yes"; } else {$verified ="No";}
                return $verified;
            })
            ->editColumn('moneybird_status', function($appointments) {
                if($appointments->booking_status=="booking_paid_pin")
                {
                    if($appointments->paid_waya=="")
                    { return "Paid Pin "; }
                    else
                    { return"Paid ".$appointments->paid_waya; }
                }

                elseif($appointments->booking_status=="booking_unpaid")
                {
                    $ExpRem="";
                    if($appointments->status=="booking_confirmed")
                    {
                        if($appointments->status_rm_exp=='E')
                        { $ExpRem = "(Expired)";}
                        elseif($appointments->status_rm_exp=='R')
                        { $ExpRem = "(Reminder)";}
                    }
                    return "Unpaid ".$ExpRem;
                }
                elseif($appointments->booking_status=="partial_paid")
                { return"Partial Paid"; }
                else
                { return $appointments->booking_status;  }
            })
            ->editColumn('booking_status', function($appointments) {
                if($appointments->status=="booking_confirmed")
                { return "Confirmed";}
                elseif($appointments->status=="booking_pending")
                { return "Pending";}
                elseif($appointments->status=="booking_cancled")
                { return "Cancled";}
                else
                {return $appointments->status;}
            })
            ->addColumn('action', function($appointments) {
                $StrApoiment = '<div class="btn-group"><a target="_blank" href="'.route('admin.appointments.copy',[$appointments->id]).'" class="btn btn-xs 
btn-primary" title="Copy"><i class="fa fa-copy"></i></a><a href="'.route('admin.appointments.show',[$appointments->id]).'" 
class="btn btn-xs btn-primary" title="View"><i class="fa fa-eye"></i></a>';
                $user = \Auth::user();
                if($user->role_id == 1)
                {
                    $StrApoiment = $StrApoiment.'  <a title="Delete" onclick="javascript: return confirm(\'Are you sure to Delete this apointment?\')" href="'.route('admin.appointment_destroy',[$appointments->id]).'" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>';
                }
                elseif($user->role_id == 3)
                {
                    if(!( $appointments->booking_status == 'booking_paid_pin' || $appointments->booking_status == 'booking_unpaid'))
                    {
                        if( empty($appointments->status) || $appointments->status == 'pending' || $appointments->status == 'booking_confirmed')
                        {
                            $StrApoiment = $StrApoiment.'  <a title="Delete" onclick="javascript: return confirm(\'Are you sure to Delete this apointment?\')" href="'.route('admin.appointment_destroy',[$appointments->id]).'" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>';
                        }
                    }
                }




                /*$invoice=Invoice::where('appointment_id','=',$appointments->id)->first();
                if(!empty($invoice->inv_number))
                { */

                $StrApoiment = $StrApoiment.' <a title="Invoice" class="btn btn-xs btn-success" onclick="viewinvoice('.$appointments->id.')" href="javascript:void(0)"><i class="fa fa-file"></i></a>';

                //}
                return $StrApoiment.'</div>';
            })
            ->filterColumn('location_name', function($query, $keyword) { $query->Where('locations.location_name', 'like', '%'.$keyword.'%');})
            ->filterColumn('start_time', function($query, $keyword) { $query->Where('appointments.start_time', 'like', '%'.$keyword.'%');})
            ->filterColumn('finish_time', function($query, $keyword) { $query->Where('appointments.finish_time', 'like', '%'.$keyword.'%'); })
            ->filterColumn('customer_name', function($query, $keyword) {
                $query->Where('clients.first_name', 'like', '%'.$keyword.'%')->Where('clients.last_name', 'like', '%'.$keyword.'%');
            })->with([ "recordsTotal"    => $cntClient,
                "recordsFiltered"  => $cntClient,])
            ->rawColumns([ 'status', 'action'])
            ->skipPaging()
            ->toJson();
        //--- Returning Json Data To Client Side
        /*
        return Datatables::of($clients)
        ->addIndexColumn()
        ->addColumn('action', function($row){
               $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';
               $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';
               return $btn;
        })->rawColumns(['action'])->make(true);
        */
        // }

    }
    
    public function copy($id)
    {
        $cpy_appointment = Appointment::findOrFail($id);

        $appointment = new Appointment;
        $appointment->client_id = $cpy_appointment->client_id;
        $appointment->room_id = $cpy_appointment->room_id;
        $appointment->repeat_appointment = $cpy_appointment->repeat_appointment;
        $appointment->repeat_appointment_no = isset($cpy_appointment->repeat_appointment_no) ? $cpy_appointment->repeat_appointment_no : 0;
        $appointment->employee_id = $cpy_appointment->employee_id;
        $appointment->location_id = $cpy_appointment->location_id;
        $appointment->service_id = $cpy_appointment->service_id;
        $appointment->switched_off_reminder_email = $cpy_appointment->switched_off_reminder_email;
        $appointment->switched_off_confirmed_email = $cpy_appointment->switched_off_confirmed_email;
        $appointment->start_time = $cpy_appointment->start_time;
        $appointment->finish_time = $cpy_appointment->finish_time;
        $appointment->comments = $cpy_appointment->comments;
        $appointment->extra_price_comment = $cpy_appointment->extra_price_comment;
        $appointment->price = $cpy_appointment->price;
        $appointment->booking_status = $cpy_appointment->booking_status;
        $appointment->original_booking_status = $cpy_appointment->original_booking_status;
        $appointment->add_by = $cpy_appointment->id; //

        $appointment->status = 'booking_confirmed';

        $appointment->save();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        $userL = \Auth::user();
        $Logdata = new Logdata;
        $Logdata->log_from = "Appoinment";
        $Logdata->refrance_id = $appointment->id;
        $Logdata->log_datetime = date("Y-m-d h:i:s");
        $Logdata->tr_by = $userL->id;
        $Logdata->message = "Create Confirm Booking";
        $Logdata->save();

        return redirect()->route('admin.appointments.show',[$appointment->id]);


    }

    public function invoices()
    {
        if (! Gate::allows('invoice_access')) { return abort(401); }
        $user = \Auth::user();
        if($user->role_id == 3)
        {
            $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
            $employee_id = $therapist[0]->id;

            $appointments = Appointment::where('employee_id','=',$employee_id)->orderBy('id','desc')->get();
        }
        else
        { $appointments = Appointment::orderBy('id','desc')->get(); }

        $status= array('pending'=>'Pending','booking_confirmed'=>'Booking Confirm','booking_paid_pin'=>'Booking Paid Pin','booking_unpaid'=>'Booking Unpaid','cash_paid'=>'Cash payment');

        for ($i=date("Y")-2; $i < date("Y")+3; $i++) { $yearlist[$i] = $i; }

        $LoginUser = \Auth::user();

        $relations = [
            'clients' => \App\Client::get(),
            'employees' => \App\Employee::get(),
            'services' => \App\Service::get(),
            'location' => \App\Location::get(),
            'room' => \App\Room::get(),
            /*'booking_status' => $status,
            'yearlist'=>$yearlist,*/
            'CUser' => $LoginUser
        ];
        return view('admin.appointments.invoices', compact('appointments') + $relations);
    }

    public function index()
    {
        if (! Gate::allows('appointment_access')) { return abort(401); }

        $this->updateExpireInvoices();

        $user = \Auth::user();
        if($user->role_id == 3)
        {
            $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
            $employee_id = $therapist[0]->id;

            $appointments = Appointment::where('employee_id','=',$employee_id)->with('client')->orderBy('id','desc')->get();

        }
        else
        {$appointments = Appointment::orderBy('id','desc')->with('client')->where('deleted_at','=',NULL)->get();}

        $status= array('pending'=>'Pending','booking_confirmed'=>'Booking Confirm','booking_paid_pin'=>'Booking Paid Pin','booking_unpaid'=>'Booking Unpaid','cash_paid'=>'Cash payment');

        for ($i=date("Y")-2; $i < date("Y")+3; $i++) {
            $yearlist[$i] = $i;
        }

        $relations = [
            'clients' => \App\Client::get(),
            'employees' => \App\Employee::get(),
            'services' => \App\Service::get(),
            'location' => \App\Location::get(),
            'room' => \App\Room::get(),
            'booking_status' => $status,
            'yearlist'=>$yearlist
        ];



        return view('admin.appointments.index', compact('appointments') + $relations);
    }

    /**
     * Show the form for creating new Appointment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        if (! Gate::allows('appointment_create')) {
            return abort(401);
        }

        if(!empty($request->client_id))
        { $client_id = $request->client_id; }
        else
        { $client_id = 0; }

        $user = \Auth::user();
        $employee_id=0;
        if($user->role_id == 3)
        {
            $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
            $employee_id = $therapist[0]->id;
            $clients = Client::where('deleted_at','=',NULL)->where('add_by','=',$employee_id)->get();
            $clientsOther = App\Appointment::join('clients', 'clients.id', '=', 'appointments.client_id')->select('clients.id','clients.first_name','clients.last_name','clients.phone','clients.email','moneybird_contact_id')->where('employee_id','=',$employee_id)->get();
            $result = $clients->merge($clientsOther);
            $clients = $result->all();
        }
        else
        {
            $clients =  \App\Client::get();
            $employee_id=0;
        }

        $parentClient = Client::select(DB::raw("CONCAT(first_name,' ',last_name) AS name"),'id')->where('deleted_at','=',NULL)->where('parent_id','=',0);
        if($employee_id>0)
        { $parentClient = $parentClient->where('add_by','=',$employee_id); }
        if($client_id>0)
        { $parentClient = $parentClient->where('id','=',$client_id); }

        $parentClient = $parentClient->get()->pluck('name', 'id')->prepend('Please select', 0);

        $DoctorList = \App\Doctor::select('id',DB::raw("CONCAT(first_name,' ',last_name) AS doctor_name"))->get()->pluck('doctor_name', 'id')->prepend('Please select Doctor', '');


        $relations = [
            'clients' => $clients,
            'client_id' =>$client_id,
            'employees' => \App\Employee::get(),
            'services' => \App\Service::get(),
            'locations' => \App\Location::get(),
            'parentClient' => $parentClient,
            'DoctorList'=>$DoctorList
        ];
        //dd($relations);

        return view('admin.appointments.create', $relations);
    }

    /**
     * Store a newly created Appointment in storage.
     *
     * @param  \App\Http\Requests\StoreAppointmentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAppointmentsRequest $request)
    {

        if (! Gate::allows('appointment_create')) {
            return abort(401);
        }

        $employee = \App\Employee::withTrashed()->where('id',$request->employee_id)->first();

        /* echo "Date ". date("d", strtotime($request->date));
         echo "<br>";
         echo " Time ".date("H:i:s", strtotime("".$request->starting_time.":00"));
         echo "<br>";
         echo "Employee Id ".$request->employee_id;*/

        $working_hours     = \App\WorkingHour::where('employee_id', $request->employee_id)->whereDate('date', '=', date("Y-m-d", strtotime($request->date)))->whereTime('start_time', '<=', date("H:i:s", strtotime("".$request->starting_time.":00")))->get();

        $working_custom_hours     = \App\EmployeeCustomtiming::where('employee_id', $request->employee_id)->whereDate('date', '=', date("Y-m-d", strtotime($request->date)))->whereTime('start_time', '<=', date("H:i:s", strtotime("".$request->starting_time.":00")))->get();

//dd($working_hours);
        // dd($employee);
        if(!$employee->provides_service($request->service_id))
        {
            return redirect()->back()->withErrors("This employee doesn't provide your selected service")->withInput();
        }

        if($working_hours->isEmpty() && $working_custom_hours->isEmpty()) return redirect()->back()->withErrors("This employee isn't working at your selected time")->withInput();

        $clientName = \App\Client::find($request->client_id);

        $client_name = $clientName->first_name." ".$clientName->last_name;
        $clientemail = $clientName->email;
        $clientphone = $clientName->phone;
        $client_house_number = $clientName->house_number;
        $client_address = $clientName->adderss;
        $moneybird_contact_id = $clientName->moneybird_contact_id;

        $thrapist_name = $employee->first_name." ".$employee->last_name;
        $thrapist_email = $employee->email;
        $therapisttelephone = $employee->phone;

        /*if(empty($moneybird_contact_id))
        {

          $contactSearchObject = Moneybird::contact();
          //  $moneybird_contact_id='271375336926610863';

          // $moneybird_contact_id='271375336926610863';
         $contactSearchObject = $contactSearchObject->search($clientName->email);
        // echo "<pre>";print_r($contactSearchObject);exit;
         if(empty($contactSearchObject))
            {
              $contactObject = Moneybird::contact();
               $contactObject->company_name = $clientName->company_name;
               $contactObject->firstname = $clientName->first_name;
               $contactObject->lastname = $clientName->last_name;
               $contactObject->send_estimates_to_email = $clientName->email;
               $contactObject->send_invoices_to_email = $clientName->email;
               //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
               $addressSend="";
               if(!empty($clientName->address))
                   { $addressSend = $clientName->address; }

               if(!empty($clientName->house_number))
                   {
                     if($addressSend!=""){ $addressSend.=' ';}
                     $addressSend .= $clientName->house_number;
                   }
               if(!empty($addressSend)) { $contactObject->address1 = $addressSend; }

              if(!empty($clientName->phone))
               {$contactObject->phone = $clientName->phone;}
             if(!empty($clientName->city_name))
               {$contactObject->city = $clientName->city_name;}
              if(!empty($clientName->postcode))
               {$contactObject->zipcode = $clientName->postcode;}
               //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

               $contactObject->save();
              $customer_moneybrid =   $contactObject->id;
            }
           else
           {
             $customer_moneybrid = $contactSearchObject[0]->id;
           }
         $clientName->moneybird_contact_id= $customer_moneybrid;
         $clientName->save();
         }*/

        // $contactSearchObject = Moneybird::contact();
        // $moneybird_contact_id='271375336926610863';

        // $moneybird_contact_id='271375336926610863';


        //$contactSearchObject = $contactSearchObject->search(trim($clientName->email));
        //if(empty($contactSearchObject))
        //{
        /*$contactObject = Moneybird::contact();
        $contactObject->company_name = $clientName->company_name;
        $contactObject->firstname = $clientName->first_name;
        $contactObject->lastname = $clientName->last_name;
        $contactObject->send_estimates_to_email = $clientName->email;
        $contactObject->send_invoices_to_email = $clientName->email;
        $contactObject->save();

        $clientName->moneybird_contact_id= $contactObject->id;
        $clientName->save();*/
        //}

        if(empty($moneybird_contact_id))
        {
            $clientName->moneybird_contact_id= '1';
            $clientName->save();
        }
        if($clientName->email_verified==0)
        {
            $clientName->email_verified= 1;
            $clientName->save();
        }
        $tharpy = \App\Service::find($request->service_id);


        $block_timing  = $tharpy->booking_block_duration;
        $tharpy_registration_no='';
        if(isset($employee->registration_no))
        {$tharpy_registration_no  = $employee->registration_no;}

        $therapistdes='';$therapistdes2='';
        if(isset($tharpy->description))
        {$therapistdes  = $tharpy->description;}
        if(isset($tharpy->description_second))
        {$therapistdes2  = $tharpy->description_second;}

        $no_of_block  = $request->no_of_block;


        $block_timing  = $tharpy->booking_block_duration;
        $no_of_block  = $request->no_of_block;
        $TimeTakenbytherapy = $no_of_block * $block_timing;


//echo "".$request->date." ".$request->starting_time.":00";exit;
        $appointment = new Appointment;
        $appointment->client_id = $request->client_id;
        $appointment->room_id = $request->room_id;
        $appointment->repeat_appointment = $request->repeat_appointment;
        $appointment->repeat_appointment_no = isset($request->repeat_appointment_no) ? $request->repeat_appointment_no : 0;
        $appointment->employee_id = $request->employee_id;
        $appointment->location_id = $request->location_id;
        $appointment->service_id = $request->service_id;
        $appointment->switched_off_reminder_email = $request->switched_off_reminder_email;
        $appointment->switched_off_confirmed_email = $request->switched_off_confirmed_email;
        $appointment->start_time = "".$request->date." ".$request->starting_time.":00";
        $appointmentstart_time = "".$request->date." ".$request->starting_time.":00";
        $endTime = date("H:i:s",strtotime("+".$TimeTakenbytherapy." minutes", strtotime($request->starting_time.":00")));
        $appointment->finish_time = "".$request->date." ".$endTime."";
        $appointment->comments = $request->comments;
        if(!empty($request->extra_price_comment))
        { $appointment->extra_price_comment = $request->extra_price_comment; }

        $appointment->price = $request->price;
        $appointment->booking_status = '';
        $appointment->original_booking_status = '';
        $userL = \Auth::user();
        $appointment->add_by = $userL->id; //

        $appointment->status = 'booking_confirmed';

        $appointment->save();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        $Logdata = new Logdata;
        $Logdata->log_from="Appoinment";
        $Logdata->refrance_id = $appointment->id;
        $Logdata->log_datetime = date("Y-m-d h:i:s");
        $Logdata->tr_by = $userL->id;
        $Logdata->message = "Create Confirm Booking";
        $Logdata->save();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data

        $dateArray=array();
        if( isset($request->repeated_number) &&  $request->repeated_number > 0)
        {

            for($i=1; $i <= $request->repeated_number; $i++)
            {
                $appointmentL = new Appointment;
                $appointmentL->client_id = $request->client_id;
                $appointmentL->repeat_appointment = '-1';//'';
                $appointmentL->repeat_appointment_no = $appointment->id;//'-1';
                $appointmentL->employee_id = $request->employee_id;
                $appointmentL->location_id = $request->location_id;
                $appointmentL->service_id = $request->service_id;
                $appointmentL->room_id = $request->room_id;
                $appointmentL->add_by = $userL->id; //
                $appointmentL->switched_off_reminder_email = $request->switched_off_reminder_email;
                $appointmentL->switched_off_confirmed_email = $request->switched_off_confirmed_email;
                if($request->repeat_appointment=='weekly')
                {
                    $days = 7 * $i;
                    $appointmentstart_time = date('Y-m-d', strtotime($request->date. ' + '.$days.' days'));
                    $appointmentend_time = date('Y-m-d', strtotime($request->date. ' + '.$days.' days'));
                    $appointmentL->start_time = "".$appointmentstart_time." ".$request->starting_time.":00";
                    $appointmentL->finish_time = "".$appointmentend_time." ".$endTime."";
                }
                else if($request->repeat_appointment=='daily')
                {
                    $days = 1 * $i;
                    $appointmentstart_time = date('Y-m-d', strtotime($request->date. ' + '.$days.' days'));
                    $appointmentend_time = date('Y-m-d', strtotime($request->date. ' + '.$days.' days'));
                    $appointmentL->start_time = "".$appointmentstart_time." ".$request->starting_time.":00";
                    $appointmentL->finish_time = "".$appointmentend_time." ".$endTime."";
                }
                else if($request->repeat_appointment=='monthly')
                {
                    $days = 30 * $i;
                    $appointmentstart_time = date('Y-m-d', strtotime($request->date. ' + '.$days.' days'));
                    $appointmentend_time = date('Y-m-d', strtotime($request->date. ' + '.$days.' days'));
                    $appointmentL->start_time = "".$appointmentstart_time." ".$request->starting_time.":00";
                    $appointmentL->finish_time = "".$appointmentend_time." ".$endTime."";
                }
                else if($request->repeat_appointment=='gap')
                {
                    $days = 14 * $i;
                    $appointmentstart_time = date('Y-m-d', strtotime($request->date. ' + '.$days.' days'));
                    $appointmentend_time = date('Y-m-d', strtotime($request->date. ' + '.$days.' days'));
                    $appointmentL->start_time = "".$appointmentstart_time." ".$request->starting_time.":00";
                    $appointmentL->finish_time = "".$appointmentend_time." ".$endTime."";
                }
                $timeDate =$appointmentL->start_time;
                $appointmentL->comments = $request->comments;
                $appointmentL->price = $request->price;
                $appointmentL->booking_status = '';
                $appointment->original_booking_status = '';
                $appointmentL->status = 'booking_confirmed';

                $working_hours     = \App\WorkingHour::where('employee_id', $request->employee_id)->where('date', '=', $appointmentstart_time)->whereTime('start_time', '<=', date("H:i", strtotime("".$request->starting_time.":00")))->get();
                //echo "Working Hour ".$working_hours;


                $employee_leave  = \App\EmployeeLeave::where('employee_id', $request->employee_id)->where('leave_date', '=', $appointmentstart_time)->get();

                $appointment_date = \App\Appointment::where('employee_id', $request->employee_id)->whereDay('start_time', '=', $timeDate)->
                get();

                if($working_hours->isEmpty() || count($employee_leave) > 0 || count($appointment_date) > 0)
                {

                    $dateArray[]=$appointmentstart_time;
                }
                else {

                    $appointmentL->save();      # code...
                }

            }
        }
        $tharpy_price = DB::table('services')
            ->leftjoin('service_extra_cost','services.id','=','service_extra_cost.service_id')
            ->where('services.id', '=', $request->service_id)
            ->get();
        //dd($tharpy_price);
        $totalCost=0;
        foreach($tharpy_price as $tharpy_price)
        {
            $extrCost= true;
            $serviceName = $tharpy_price->name;
            $duration_block = $tharpy_price->booking_block_duration;
            $no_of_block = $tharpy_price->min_block_duration;
            $block_cost = $tharpy_price->block_cost;
            $block_types = $tharpy_price->booking_series_type;
            $extra_cost_unit = $tharpy_price->booking_block_cost_duration_type_unit;
            $extra_cost_price_startTime = $tharpy_price->booking_pricing_time_from;
            $sessionCost = $tharpy_price->block_cost *  $no_of_block;
            if($extrCost)
            { $totalCost = $tharpy_price->block_cost *  $no_of_block;}


            if($request->starting_time.":00" > $extra_cost_price_startTime && $extrCost)
            {
                $extrCost=false;
                $totalCost = $totalCost  + ($tharpy_price->booking_block_pricing *  $no_of_block);
            }

            $extra_cost_price_endTime = $tharpy_price->booking_pricing_time_to;
        }
        $tharpyname = $tharpy->name;
        $locations= \App\Location::find($request->location_id);
        $locationname = $locations->location_name;
        $location_address = $locations->location_address;
        $locationdesc = $locations->location_description;

        $email_customer_template = DB::table('email_templates')
            ->select('*')
            ->where('email_templates.email_type', '=', 'confirmation_customer_email')->get();
        $matter = $email_customer_template[0]->email_content;
        $email_subject = $email_customer_template[0]->email_subject;
        $bcc_email_id = $email_customer_template[0]->email_id;

        if(!empty(trim($client_name)))
        {$matter = str_replace("{clientname}",$client_name,$matter);}
        else
        {$matter = str_replace("{clientname}",'',$matter);}

        if(!empty(trim($thrapist_name)))
        {$matter = str_replace("{therapistname}",$thrapist_name,$matter);}
        else
        {$matter = str_replace("{therapistname}",'',$matter);}

        if(!empty(trim($clientemail)))
        {$matter = str_replace("{customeremail}",$clientemail,$matter);}
        else
        {$matter = str_replace("{customeremail}",'',$matter);}

        if(!empty(trim($clientphone)))
        {$matter = str_replace("{customertelephonenumber}",$clientphone,$matter);}
        else
        {$matter = str_replace("{customertelephonenumber}",'',$matter);}


        if(!empty(trim($therapistdes)))
        {$matter = str_replace("{therapistdes}",$therapistdes,$matter);}
        else
        {$matter = str_replace("{therapistdes}",'',$matter);}

        if(!empty(trim($tharpy_registration_no)))
        {$matter = str_replace("{therapistregistrations}",$tharpy_registration_no,$matter);}
        else
        {$matter = str_replace("{therapistregistrations}",'',$matter);}


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

        if(!empty(trim($totalCost)))
        {$matter = str_replace("{tharpycost}",$totalCost,$matter);}
        else
        {$matter = str_replace("{tharpycost}",'',$matter);}

        if(!empty(trim($locationdesc)))
        {$matter = str_replace("{route_directions}",$locationdesc,$matter);}
        else
        {$matter = str_replace("{route_directions}",'',$matter);}

        if(!empty(trim($location_address)))
        {$matter = str_replace("{location_address}",$location_address,$matter);}
        else
        {$matter = str_replace("{location_address}",'',$matter);}

        if(!empty(trim($therapisttelephone)))
        {$matter = str_replace("{therapisttelephone}",$therapisttelephone,$matter);}
        else{
            {$matter = str_replace("{therapisttelephone}",'',$matter);}
        }
        if(!empty(trim($sessionCost)))
        {$matter = str_replace("{session_costs_for_an_hour}",$sessionCost,$matter);}
        else
            $matter = str_replace("{session_costs_for_an_hour}",'',$matter);

        Date::setLocale('nl');
        //$matter = str_replace("{booking_date}","".date('l d F Y',strtotime($request->date))." ".$request->starting_time,$matter);

        $matter = str_replace("{booking_date}","".Date::parse($request->date)->format('l j F Y'),$matter);
        $matter = str_replace("{booking_time}","".$request->starting_time,$matter);


        $email_therapist_template = DB::table('email_templates')
            ->select('*')
            ->where('email_templates.email_type', '=', 'confirmation_therapist_email')->get();
        $therapist_matter = $email_therapist_template[0]->email_content;
        $email_therapist_subject = $email_therapist_template[0]->email_subject;

        if(!empty(trim($client_name)))
        {$therapist_matter = str_replace("{clientname}",$client_name,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{clientname}",'',$therapist_matter);}

        if(!empty(trim($thrapist_name)))
        {$therapist_matter = str_replace("{therapistname}",$thrapist_name,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{therapistname}",'',$therapist_matter);}

        if(!empty(trim($tharpy_registration_no)))
        {$therapist_matter = str_replace("{therapistregistrations}",$tharpy_registration_no,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{therapistregistrations}",'',$therapist_matter);}

        if(!empty(trim($clientemail)))
        {$therapist_matter = str_replace("{customeremail}",$clientemail,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{customeremail}",'',$therapist_matter);}

        if(!empty(trim($clientphone)))
        {$therapist_matter = str_replace("{customertelephonenumber}",$clientphone,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{customertelephonenumber}",'',$therapist_matter);}




        if(!empty(trim($therapistdes)))
        {$therapist_matter = str_replace("{therapistdes}",$therapistdes,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{therapistdes}",'',$therapist_matter);}

        if(!empty(trim($therapistdes2)))
        {$therapist_matter = str_replace("{therapistdes2}",$therapistdes2,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{therapistdes2}",'',$therapist_matter);}

        if(!empty(trim($tharpyname)))
        {$therapist_matter = str_replace("{thrapyname}",$tharpyname,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{thrapyname}",'',$therapist_matter);}

        if(!empty(trim($locationname)))
        {$therapist_matter = str_replace("{location}",$locationname,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{location}",'',$therapist_matter);}

        if(!empty(trim($totalCost)))
        {$therapist_matter = str_replace("{tharpycost}",$totalCost,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{tharpycost}",'',$therapist_matter);}

        if(!empty($locationdesc))
        {$therapist_matter = str_replace("{route_directions}",$locationdesc,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{route_directions}",'',$therapist_matter);}

        if(!empty($location_address))
        {$therapist_matter = str_replace("{location_address}",$location_address,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{location_address}",'',$therapist_matter);}

        if(!empty($therapisttelephone))
        {$therapist_matter = str_replace("{therapisttelephone}",$therapisttelephone,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{therapisttelephone}",'',$therapist_matter);}

        if(!empty($sessionCost))
        {$therapist_matter = str_replace("{session_costs_for_an_hour}",$sessionCost,$therapist_matter);}
        else
        {$therapist_matter = str_replace("{session_costs_for_an_hour}",'',$therapist_matter);}

        //$therapist_matter = str_replace("{booking_date}","".date('l d F Y',strtotime($request->date))." ".$request->starting_time,$therapist_matter);

        $therapist_matter = str_replace("{booking_date}","".Date::parse($request->date)->format('l j F Y'),$therapist_matter);
        $therapist_matter = str_replace("{booking_time}","".$request->starting_time,$therapist_matter);

        /*$data = array('name'=>"Virat Gandhi");
          Mail::send(['text'=>'mail'], $matter, function($message) {
             $message->to('bohra.shard@gmail.com', 'Tutorials Point')->subject
                ('Laravel HTML Testing Mail');
             $message->from('info@ecybertech.com','Sharad');
          });*/
        $matter = Appointment::EmailContentTLT($matter);
        $therapist_matter = Appointment::EmailContentTLT($therapist_matter);
        if(empty($request->switched_off_confirmed_email))
        {
            /* Log::info('Log Created: Cleint Email '.$clientemail);
             Log::info('Log Created: Subject Email '.$email_subject);
             Log::info('Log Created: Matter '.$matter);
            */

           try {
			    Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
                $message->to($clientemail)
                    ->from("info@praktijk-afspraken.nl")
                    ->subject($email_subject)
                    ->setBody($matter, 'text/html'); // for HTML rich messages
            });
		   } catch (\Throwable $th) {
			   //throw $th;
		   }
        }

        if(!empty($bcc_email_id))
        {

            Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
                $message->to($clientemail)
                    ->from("info@praktijk-afspraken.nl")
                    ->subject($email_subject)
                    ->setBody($matter, 'text/html'); // for HTML rich messages
            });


            Mail::send([], [], function ($message) use ($thrapist_email,$email_therapist_subject,$therapist_matter) {
                $message->to($thrapist_email)
                    ->from("info@praktijk-afspraken.nl")
                    ->subject($email_therapist_subject)
                    ->setBody($therapist_matter, 'text/html'); // for HTML rich messages
            });

        }

		try {
			Mail::send([], [], function ($message) use ($thrapist_email,$email_therapist_subject,$therapist_matter) {
				$message->to($thrapist_email)
					->from("info@praktijk-afspraken.nl")
					->subject($email_therapist_subject)
					->setBody($therapist_matter, 'text/html'); // for HTML rich messages
			});
		} catch (\Throwable $th) {
			//throw $th;
		}
        if(count($dateArray) > 0)
        {
            $dates = implode(',', $dateArray);
            return redirect()->route('admin.appointments.index')->with('msg', 'Appointment did not created on dates due to therpist leave or bussy with another appointment'.$dates);
        }
        else
        { return redirect()->route('admin.appointments.index'); }
    }

    /**
     * Show the form for editing Appointment.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        return redirect()->route('admin.appointments.show',$id);
        exit;
        if (! Gate::allows('appointment_edit')) { return abort(401); }
        $relations = [
            'clients' => \App\Client::get()->pluck('first_name', 'id')->prepend('Please select', ''),
            'employees' => \App\Employee::withTrashed()->get()->pluck('first_name', 'id')->prepend('Please select', ''),
            'services' => \App\Service::get()->pluck('name', 'id')->prepend('Please select', ''),
            'locations' => \App\Location::get()->pluck('location_name', 'id')->prepend('Please select', ''),
        ];
        $appointment = Appointment::findOrFail($id);
        if(isset($appointment->booking_status) && ($appointment->booking_status !='booking_paid_pin' &&  $appointment->booking_status !='cash_paid'))
        { return view('admin.appointments.edit', compact('appointment') + $relations); }
        else
        { return redirect()->route('admin.appointments.index'); }
    }

    /**
     * Update Appointment in storage.
     *
     * @param  \App\Http\Requests\UpdateAppointmentsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAppointmentsRequest $request, $id)
    {
        if (! Gate::allows('appointment_edit')) { return abort(401); }
        $appointment = Appointment::findOrFail($id);
        $appointment->client_id = $request->client_id;
        $appointment->employee_id = $request->employee_id;
        $appointment->location_id = $request->location_id;
        $appointment->service_id = $request->service_id;

        $appointment->price = $request->price;
        $appointment->start_time = "".$request->date." ".$request->starting_time.":00";
        $endTime = date("H:i:s",strtotime("+60 minutes", strtotime($request->starting_time.":00")));
        $appointment->finish_time = "".$request->date." ".$endTime."";
        $appointment->comments = $request->comments;
        $appointment->update();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        $Logdata = new Logdata;
        $Logdata->log_from="Appoinment";
        $Logdata->refrance_id = $id;
        $Logdata->log_datetime = date("Y-m-d h:i:s");
        $userL = \Auth::user();
        $Logdata->tr_by = $userL->id;
        $Logdata->message = "Modifield Booking";
        $Logdata->save();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        return redirect()->route('admin.appointments.index');
    }

    public function sendinvoice(Request $request)
    {
        $invoiceemail = $request->invoiceemail;
        $id = $request->appointment_id;
//        $client_first_name= $request->client_first_name;
//        $client_last_name= $request->client_last_name;
//        $emp_first_name= $request->emp_first_name;
//        $emp_last_name= $request->emp_last_name;
        $client_id = $request->client_id;

        if(!empty($request->client_name)){
             $this->sendinvoiceEmail($invoiceemail,$client_id,$request->client_name,$id);
        }else{
            $this->sendinvoiceEmail($invoiceemail,$client_id,"",$id);
        }
        return redirect()->back()->with('success', 'Mail Sucessfully send!');
    }

    public function invdownload($id)
    {
        if (! Gate::allows('appointment_view')) { return abort(401); }
        $FieName=$id.".pdf";
        $PdfUrl = url('/public/upload/'.$FieName);
        return redirect($PdfUrl);
    }

    public function sendinvoiceEmail($invoiceemail,$client_id,$client_name,$id)
    {
        if(!empty($invoiceemail) && $id>0)
        {
            $appointment = Appointment::findOrFail($id);
            if(!empty($client_name)){
                $prev_client = Client::where('id',$appointment->client_id)->first();

                $MsgStatusNameChanged = 'Client name changed to '.$client_name.' '.' previously named as '.$prev_client->first_name.' '.$prev_client->last_name;
                //die($MsgStatusNameChanged);
                $Logdata = new Logdata;
                $Logdata->log_from="Appoinment";
                $Logdata->refrance_id = $id;
                $Logdata->log_datetime = date("Y-m-d h:i:s");
                $userL = \Auth::user();
                $Logdata->tr_by = $userL->id;
                $Logdata->message = $MsgStatusNameChanged;
                $Logdata->save();
                $appointment->client_id = $client_id;
                $appointment->update();
            }
            $View = Appointment::ApInvoicePrintView($id,0);



            $email_filename_attachment = "inv_".date("Ymdhis").'.pdf';

            $pdf = PDF::loadHTML($View);
           // $pdf = $pdf->render();

            $Strpath = public_path('/upload/'.$email_filename_attachment);
            $pdf->save($Strpath);
            
            $PdfUrl = url('/public/upload/'.$email_filename_attachment);
            //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
            $RetMessageArr = Appointment::GetInvoiceEmailContent($id,"",0);

            $email_subject =$RetMessageArr['email_subject'];
            $matter =$RetMessageArr['email_mtr'];
            if(!empty($RetMessageArr['ExtraEmail']))
            { $invoiceemail=$invoiceemail.",".$RetMessageArr['ExtraEmail']; }
            //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

            $clientName = Client::find($appointment->client_id);
            $clientemail = $clientName->email;

            $email_attachment_path = $Strpath;
            $ArrfileExtArr = explode(".", $email_attachment_path);
            $ext = end($ArrfileExtArr);
            //$info = pathinfo($email_attachment_path);
            //$ext = $info['extension'];
            $application = 'application/'.$ext;

            $ArrClientEmail = explode(",",$invoiceemail);
            $clientemail = $ArrClientEmail[0];
            $matter = Appointment::EmailContentTLT($matter);

            if(count($ArrClientEmail)==1)
            {

				try {
					Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter,$email_attachment_path,$email_filename_attachment,$application) {

						$message->to($clientemail)
							->subject($email_subject)
							->from("info@praktijk-afspraken.nl")
							->attach($email_attachment_path, [
								'as' => $email_filename_attachment,
								'mime' => $application,
							])->setBody($matter, 'text/html')
							->setContentType('text/html');
	
					});
				} catch (\Throwable $th) {
					//throw $th;
				}
            }
            elseif(count($ArrClientEmail)>1)
            {
                $StremaiBcc=[];
                foreach ($ArrClientEmail as $key => $value)
                { if($key>0 && $value!="") { $StremaiBcc[] = $value; } }
               try {
				Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter,$email_attachment_path,$email_filename_attachment,$application,$StremaiBcc) {

                    $message->to($clientemail)
                        ->cc($StremaiBcc)
                        ->from("info@praktijk-afspraken.nl")
                        ->subject($email_subject)
                        ->attach($email_attachment_path, [
                            'as' => $email_filename_attachment,
                            'mime' => $application,
                        ])->setBody($matter, 'text/html')
                        ->setContentType('text/html'); // for HTML rich messages

                    //dd($message->getHeaders()); exit;
                });
			   } catch (\Throwable $th) {
				   //throw $th;
			   }
            }

            $Filename=str_replace(".pdf","", $email_filename_attachment);
            $PdfUrl = url('/appointmentinv/'.$Filename);

            $MsgStatus="Send mail <a href='".$PdfUrl."'>pdf file</a><br>".$invoiceemail;
            //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data

            if(!empty($MsgStatus)){
                $Logdata = new Logdata;
                $Logdata->log_from="Appoinment";
                $Logdata->refrance_id = $id;
                $Logdata->log_datetime = date("Y-m-d h:i:s");
                $userL = \Auth::user();
                $Logdata->tr_by = $userL->id;
                $Logdata->message = $MsgStatus;
                $Logdata->save();
            }






            //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        }
    }

    public function pview($id)
    {
        $invDetail = Appointment::ApInvoiceDetail($id);
        if(!empty($invDetail->inv_number))
        {

            $View = Appointment::ApInvoicePrintView($id,1);
            echo $View;
            ?>
            <script type="text/javascript">
                window.print();
            </script>
            <?php
        }
        else
        { echo "Invoice not created";  }
        exit;
    }
    /**
     * Display Appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Date::setLocale('nl');

        if (! Gate::allows('appointment_view')) { return abort(401); }

        Date::setLocale('nl');

        $appointment = Appointment::findOrFail($id);
        $ArrEmail=array('employee'=>'','client'=>'','doctor'=>'');
        $employee = \App\Employee::findOrFail($appointment->employee_id);
        if(!empty($employee->email)){ $ArrEmail['employee']=$employee->email;}


        $clientD = \App\Client::findOrFail($appointment->client_id);
        if(!empty($clientD->email))
        {
            $CEmail="";
            $CEmail =$clientD->email;
            if(!empty($clientD->email_invoice))
            {
                if($CEmail!=""){ $CEmail = $CEmail.",";}
                $CEmail = $CEmail.$clientD->email_invoice;
            }
            if(!empty($CEmail)){ $ArrEmail['client'] = $CEmail; }
        }
        if(!empty($clientD->doctor_id))
        {
            $DoctorData = \App\Doctor::findOrFail($clientD->doctor_id);
            if(!empty($DoctorData->email)) { $ArrEmail['doctor'] = $DoctorData->email; }
        }



        $employee = \App\Employee::findOrFail($appointment->employee_id);

        $employee_service = \App\EmployeeService::where('service_id','=',$appointment->service_id)->where('employee_id','=',$appointment->employee_id)->whereNull('deleted_at')->get();

        $TaxData = \App\TaxRate::where('active',"=",'1')->where('tax_rate_type',"=",'sales_invoice')->get();

        $CmpDtl = \App\TblCompanies::find($appointment->company_id);
        $InvoiceDetail = Appointment::ApInvoiceDetail($id);

        if(!empty($InvoiceDetail->inv_number))
        {
            /*$inv_numberStr = $inv_number = $InvoiceDetail->inv_number;
            if(strlen($inv_number)==1){$inv_numberStr = "000".$inv_number;}
            elseif(strlen($inv_number)==2){$inv_numberStr = "00".$inv_number;}
            elseif(strlen($inv_number)==3){$inv_numberStr = "0".$inv_number;}
            $inv_numberStr = $InvoiceDetail->inv_fyear.'-'.$inv_numberStr;*/

            $inv_numberStr = $InvoiceDetail->display_inv_no;
            if(!empty($InvoiceDetail->payment_received))
            {
                $InvoiceDetail->payment_received = json_decode($InvoiceDetail->payment_received);
            }
            else
            {
                $payment_received['I']['amount']="";
                $payment_received['I']['date']="";
                $payment_received['II']['amount']="";
                $payment_received['II']['date']="";

                $InvoiceDetail->payment_received=json_decode(json_encode($payment_received));
            }


        }
        else
        {
            $employee_service2=\App\EmployeeService::where('service_id','=',$appointment->service_id)->where('employee_id','=',$appointment->employee_id)->whereNull('deleted_at')->first();

            $InvoiceDetail = New Appointment();
            $inv_numberStr = $inv_number = "";
            $InvoiceDetail->inv_date=date("Y-m-d");

            $InvoiceDetail->due_date = $due_date = date("Y-m-d");
            if( $appointment->booking_status != "booking_paid_pin" )
            {
                $expired_days = $CmpDtl->expired_days;
                $InvoiceDetail->due_date = date('Y-m-d', strtotime($due_date. ' + '.$expired_days.' days'));
            }

            $InvoiceDetail->price = $appointment->price;

            $payment_received['I']['amount']="";
            $payment_received['I']['date']="";
            $payment_received['II']['amount']="";
            $payment_received['II']['date']="";
            $InvoiceDetail->payment_received=json_decode(json_encode($payment_received));

            $descriptadd = "";
            if(!empty($employee_service2->moneybird_username))
            { $descriptadd = $employee_service2->moneybird_username; }
            $dateM = $appointment->start_time;
            $linedescription = $descriptadd."<br/> Afspraakdatum: ".Date::parse($appointment->start_time)->format('d F Y');

            $service_tax_rate = \DB::table('services')->where('id','=',$appointment->service_id)->whereNotNull('tax_rate_id_moneybrid')->first();
            if(!empty($service_tax_rate->tax_rate_id_moneybrid))
            {
                $tax_rate_id = $service_tax_rate->tax_rate_id_moneybrid;

                $TaxData2 = \DB::table('tax_rates')->where('moneybird_tax_id',$tax_rate_id)->first();
            }
            $InvoiceDetail->prd_description=$linedescription;
            $taxamount = 0;
            if(!empty($TaxData2))
            {
                if( $appointment->price > 0 && $TaxData2->percentage > 0 )
                {
                    $Rate = $TaxData2->percentage;
                    $taxamount = ($price*($Rate/100)) ;
                }
                $InvoiceDetail->taxid = $TaxData2->id;
                if($TaxData2->percentage!="") { $InvoiceDetail->taxrate = $TaxData2->percentage; }
                $InvoiceDetail->taxrate_title = $TaxData2->name;
                $InvoiceDetail->taxamount = $taxamount;
            }
            $InvoiceDetail->baseamount = $appointment->price;
            $InvoiceDetail->taxamount = $taxamount;
            $InvoiceDetail->netamount = $appointment->price + $taxamount;
        }

        $InvLog = Logdata::leftJoin('users', 'users.id', '=', 'logdatas.tr_by')->where('log_from','=','Appoinment')->where('refrance_id','=',$id)->orderBy('logdatas.id','desc')->get();


        $email_templates_doctor = \App\EmailTemplate::where(function ($query)  { $query->where('id','=',29)->orWhere('email_user_type', '=', 5);
        })->pluck('email_subject', 'id')->prepend('Please select email template', '');

        $user = \Auth::user();
        /*
        $email_templates_doctor = array();
        if($user->role_id == 1)
          {
              $email_templates_doctor = \App\EmailTemplate::where(function ($query)  { $query->where('id','=',29)->orWhere('email_user_type', '=', 5);
                    })->pluck('email_subject', 'id')->prepend('Please select email template', '');
              }
        elseif($user->role_id == 3)
          {
            $email_templates_doctor = \App\EmailTemplate::whereIn('id',['29','30'])->get()->pluck('email_subject', 'id')->prepend('Please select email template', '');
          }*/


        $clients = Client::where('deleted_at','=',NULL)->select(DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),'id')->pluck('customer_name','id');

        $relations = [
            'employee_service' => $employee_service,
            'InvLog' => $InvLog,
            'ArrEmail'=>$ArrEmail,
            'clients'=>$clients,

            'email_templates_doctor' => $email_templates_doctor,

            'email_templates' => \App\EmailTemplate::whereNull('email_type')->get()->pluck('email_subject', 'id')->prepend('Please select email template', ''),

            "TaxData" => $TaxData,
            "inv_numberStr" => $inv_numberStr,
            "InvoiceDetail" => $InvoiceDetail,
            'CurrUser' => $user

        ];
        return view('admin.appointments.show', compact('appointment') + $relations);
    }


    /**
     * Remove Appointment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function cancleinv($id)
    {
        if (! Gate::allows('appointment_delete'))  { return abort(401); }
        $entries = Invoice::where('appointment_id', $id)->first();
        $entries->invoice_cancled ='1';
        $entries->save();
        $appointment = Appointment::findOrFail($id);
        $appointment->status = "booking_cancled";
        $appointment->paid_waya = "";
        $appointment->save();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        $Logdata = new Logdata;
        $Logdata->log_from="Appoinment";
        $Logdata->refrance_id = $id;
        $Logdata->log_datetime = date("Y-m-d h:i:s");
        $userL = \Auth::user();
        $Logdata->tr_by = $userL->id;
        $Logdata->message = "Cancel Invoice";
        $Logdata->save();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        return redirect()->route('admin.appointments.index');
    }

    public function destroy($id)
    {
        if (! Gate::allows('appointment_delete')) { return abort(401); }

        /*$appointment = Appointment::findOrFail($id);
        $appointment->status = "";
        $appointment->booking_status = "";
        $appointment->original_booking_status = "";
        $appointment->paid_waya = "";
        $appointment->moneybird_invoice_id = "";
        $appointment->save();*/

        $Invoice = Invoice::where(['appointment_id'=>$id]);
        $Invoice->delete();

        $appointment = Appointment::where(['id'=>$id]);
        /*$appointment = Appointment::findOrFail($id);*/
        $appointment->delete();

        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        $Logdata = new Logdata;
        $Logdata->log_from="Appoinment";
        $Logdata->refrance_id = $id;
        $Logdata->log_datetime = date("Y-m-d h:i:s");
        $userL = \Auth::user();
        $Logdata->tr_by = $userL->id;
        $Logdata->message = "Delete invoice and appointment";
        $Logdata->save();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Delete all selected Appointment at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('appointment_delete')) { return abort(401); }
        if ($request->input('ids'))
        {
            $entries = Appointment::whereIn('id', $request->input('ids'))->get();
            foreach ($entries as $entry) { $entry->delete(); }
        }
    }


    public function sendcustomemaildoctorbackup(Request $request)
    {
        $appid = $request->appointment_id;
        $TltId = $request->email_templates_doctor;
        $invoiceemail = $request->email_doctor;


        $EmailTemplate = \App\EmailTemplate::findOrFail($TltId);
        $email_type = $EmailTemplate->email_type;
        $ArrEmail = Appointment::GetInvoiceEmailContent($appid,$email_type,1);
        $email_subject = $ArrEmail['email_subject'];
        if(!empty($ArrEmail['email_id']))
        { $invoiceemail=$invoiceemail.",".$ArrEmail['email_id']; }

        $matter = str_replace("\r\n", "", $ArrEmail['email_mtr']) ;



        if($TltId==29)
        {
            $email_filename_attachment = "th_".date("Ymdhis").'.pdf';
            $ArrEmailP = Appointment::GetInvoiceEmailContent($appid,$email_type,0);
            $matterP = str_replace("\r\n", "", $ArrEmailP['email_mtr']) ;



            $pdf = PDF::loadHTML($matterP);
            $Strpath = public_path('/upload/'.$email_filename_attachment);
            $pdf->save($Strpath);
            $PdfUrl = url('/public/upload/'.$email_filename_attachment);

            $email_attachment_path = $Strpath;
            $info = pathinfo($email_attachment_path);
            $ext = $info['extension'];
            $application = 'application/'.$ext;


        }


        $ArrClientEmail = explode(",",$invoiceemail);
        $clientemail = $ArrClientEmail[0];
        $matter = Appointment::EmailContentTLT($matter);


        if(count($ArrClientEmail)==1)
        {
            if($TltId==29)
            {
                Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter,$email_attachment_path,$email_filename_attachment,$application) {
                    $message->to($clientemail)
                        ->from("info@praktijk-afspraken.nl")
                        ->subject($email_subject)
                        ->attach($email_attachment_path, [
                            'as' => $email_filename_attachment,
                            'mime' => $application,
                        ])
                        ->setBody($matter, 'text/html')
                        ->setContentType('text/html')
                    ; // for HTML rich messages
                });
            }
            else
            {
                Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
                    $message->to($clientemail)
                        ->from("info@praktijk-afspraken.nl")
                        ->subject($email_subject)
                        ->setBody($matter, 'text/html')
                        ->setContentType('text/html'); // for HTML rich messages
                });
            }
        }
        elseif(count($ArrClientEmail)>1)
        {
            $StremaiBcc=[];
            foreach ($ArrClientEmail as $key => $value) {
                if($key>0 && $value!="") { $StremaiBcc[] = $value; }
            }

            /*$StremaiBcc="";
            foreach ($ArrClientEmail as $key => $value) {
              if($key>0 && $value!="")
              {
                if($StremaiBcc!=""){ $StremaiBcc=$StremaiBcc.",";}
                $StremaiBcc = $StremaiBcc.$value;
              }

            }*/
            if($TltId==29)
            {
                Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter,$email_attachment_path,$email_filename_attachment,$application,$StremaiBcc) {

                    $message->to($clientemail)
                        ->from("info@praktijk-afspraken.nl")
                        ->bcc($StremaiBcc)
                        ->subject($email_subject)
                        ->attach($email_attachment_path, [
                            'as' => $email_filename_attachment,
                            'mime' => $application])
                        ->setBody($matter, 'text/html')
                        ->setContentType('text/html'); // for HTML rich messages
                });
            }
            else
            {
                Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter,$StremaiBcc) {

                    $message->to($clientemail)
                        ->from("info@praktijk-afspraken.nl")
                        ->bcc($StremaiBcc)
                        ->subject($email_subject)
                        ->setBody($matter, 'text/html')
                        ->setContentType('text/html'); // for HTML rich messages

                });
            }

        }

        return redirect()->route('admin.appointments.index');

    }

    public function sendcustomemaildoctor(Request $request)
    {
        $appid = $request->appointment_id;
        $TltId = $request->email_templates_doctor;
        $invoiceemail = $request->email_doctor;


        $EmailTemplate = EmailTemplate::findOrFail($TltId);
        $email_type = $EmailTemplate->email_type;
        $ArrEmail = Appointment::GetInvoiceEmailContent($appid,$email_type,1);
        $email_subject = $ArrEmail['email_subject'];
        if(!empty($ArrEmail['email_id']))
        { $invoiceemail=$invoiceemail.",".$ArrEmail['email_id']; }

        $matter = str_replace("\r\n", "", $ArrEmail['email_mtr']) ;



        if($TltId==29)
        {
            $email_filename_attachment = "th_".date("Ymdhis").'.pdf';
            $ArrEmailP = Appointment::GetInvoiceEmailContent($appid,$email_type,0);
            $matterP = str_replace("\r\n", "", $ArrEmailP['email_mtr']) ;



            $pdf = PDF::loadHTML($matterP);
            $Strpath = public_path('/upload/'.$email_filename_attachment);
            $pdf->save($Strpath);
            $PdfUrl = url('/public/upload/'.$email_filename_attachment);

            $email_attachment_path = $Strpath;
            $info = pathinfo($email_attachment_path);
            $ext = $info['extension'];
            $application = 'application/'.$ext;


        }


        $ArrClientEmail = explode(",",$invoiceemail);
        $clientemail = $ArrClientEmail[0];
        $matter = Appointment::EmailContentTLT($matter);


        return response()->json(['html' => '<div class="swiper-slide"><textarea class="form-control edi" name="edi" rows="10" cols="60">'.$matter.'</textarea></div>']);

    }
    public function sendcustomemaildoctorconfirmed(Request $request)
    {
        try {


            $appid = $request->appointment_id;
            $TltId = $request->email_templates_doctor;
            $invoiceemail = $request->email_doctor;


            $EmailTemplate = EmailTemplate::findOrFail($TltId);
            $email_type = $EmailTemplate->email_type;
            $ArrEmail = Appointment::GetInvoiceEmailContent($appid, $email_type, 1);
            $email_subject = $ArrEmail['email_subject'];
            if (!empty($ArrEmail['email_id'])) {
                $invoiceemail = $invoiceemail . "," . $ArrEmail['email_id'];
            }

            //$matter = str_replace("\r\n", "", $ArrEmail['email_mtr']) ;
            $matter = $request->input('matter');


            if ($TltId == 29) {
                $email_filename_attachment = "th_" . date("Ymdhis") . '.pdf';
                $ArrEmailP = Appointment::GetInvoiceEmailContent($appid, $email_type, 0);
                $matterP = str_replace("\r\n", "", $ArrEmailP['email_mtr']);


                $pdf = PDF::loadHTML($matterP);
                $Strpath = public_path('/upload/' . $email_filename_attachment);
                $pdf->save($Strpath);
                $PdfUrl = url('/public/upload/' . $email_filename_attachment);

                $email_attachment_path = $Strpath;
                $info = pathinfo($email_attachment_path);
                $ext = $info['extension'];
                $application = 'application/' . $ext;


            }


            $ArrClientEmail = explode(",", $invoiceemail);
            $clientemail = $ArrClientEmail[0];
            //  $matter = Appointment::EmailContentTLT($matter);


            if (count($ArrClientEmail) == 1) {
                if ($TltId == 29) {
                    Mail::send([], [], function ($message) use ($clientemail, $email_subject, $matter, $email_attachment_path, $email_filename_attachment, $application) {
                        $message->to($clientemail)
                            ->from("info@praktijk-afspraken.nl")
                            ->subject($email_subject)
                            ->attach($email_attachment_path, [
                                'as' => $email_filename_attachment,
                                'mime' => $application,
                            ])
                            ->setBody($matter, 'text/html')
                            ->setContentType('text/html'); // for HTML rich messages
                    });
                } else {
                    Mail::send([], [], function ($message) use ($clientemail, $email_subject, $matter) {
                        $message->to($clientemail)
                            ->from("info@praktijk-afspraken.nl")
                            ->subject($email_subject)
                            ->setBody($matter, 'text/html')
                            ->setContentType('text/html'); // for HTML rich messages
                    });
                }
            } elseif (count($ArrClientEmail) > 1) {
                $StremaiBcc = [];
                foreach ($ArrClientEmail as $key => $value) {
                    if ($key > 0 && $value != "") {
                        $StremaiBcc[] = $value;
                    }
                }

                /*$StremaiBcc="";
                foreach ($ArrClientEmail as $key => $value) {
                  if($key>0 && $value!="")
                  {
                    if($StremaiBcc!=""){ $StremaiBcc=$StremaiBcc.",";}
                    $StremaiBcc = $StremaiBcc.$value;
                  }

                }*/
                if ($TltId == 29) {
                    Mail::send([], [], function ($message) use ($clientemail, $email_subject, $matter, $email_attachment_path, $email_filename_attachment, $application, $StremaiBcc) {

                        $message->to($clientemail)
                            ->bcc($StremaiBcc)
                            ->subject($email_subject)
                            ->attach($email_attachment_path, [
                                'as' => $email_filename_attachment,
                                'mime' => $application])
                            ->setBody($matter, 'text/html')
                            ->setContentType('text/html'); // for HTML rich messages
                    });
                } else {

                    Mail::send([], [], function ($message) use ($clientemail, $email_subject, $matter, $StremaiBcc) {

                        $message->to($clientemail)
                            ->from("info@praktijk-afspraken.nl")
                            ->bcc($StremaiBcc)
                            ->subject($email_subject)
                            ->setBody($matter, 'text/html')
                            ->setContentType('text/html'); // for HTML rich messages

                    });
                }

            }
            return response()->json(['message' => 'Email sent successfully','status' => true]);
        }catch (\Exception $exception){
            return response()->json(['message' => 'Error sending email. Please refresh page or select another.','status' => false]);
        }


    }

    public function sendcustomemailinvoice(Request $request)
    {

        $email_templates_id = $request->invoice_email_type;
        $appointment_id = $request->appointment_id;

        Date::setLocale('nl');
        $email_type = 'reminder_invoice_email_client_'.$email_templates_id;
        $content_Arr =  Appointment::GetInvoiceEmailContent($appointment_id,$email_type,0);

        return response()->json(['html' => $content_Arr['email_mtr']]);

    }
    public function sendcustomemailinvoiceconfirmed(Request $request)
    {

        try {

            $email_templates_id = $request->invoice_email_type;
            $appointment_id = $request->appointment_id;
            $matter = $request->matter;
            $email_type = 'reminder_invoice_email_client_'.$email_templates_id;
            $content_Arr =  Appointment::GetInvoiceEmailContent($appointment_id,$email_type,0);
            Mail::send([], [], function ($message) use ($content_Arr,$matter) {
              $message->to($content_Arr['client_email'])
                //   $message->to('engrsk60@gmail.com')
                    ->cc(['emailpraktijkafspraken@gmail.com'])
                    ->from("info@praktijk-afspraken.nl")
                    ->subject($content_Arr['email_subject'])
                    ->setBody($matter, 'text/html'); // for HTML rich messages
            });

            $Logdata = new Logdata;
            $Logdata->log_from="Appoinment";
            $Logdata->refrance_id = $appointment_id;
            $Logdata->log_datetime = date("Y-m-d h:i:s");
            $userL = \Auth::user();
            $Logdata->tr_by = $userL->id;
            $Logdata->message = "Invoice email type ".$email_templates_id." sent to ".$content_Arr['client_email'];
            $Logdata->save();

            return response()->json(['message' => 'Email sent successfully','status' => true]);
        }catch (\Exception $exception){
            return response()->json(['message' => 'Error sending email. Please refresh page or select another.','status' => false]);
        }
    }

    private function dutch_strtotime($datetime) {
        $days = array(
            "maandag"   => "Monday",
            "dinsdag"   => "Tuesday",
            "woensdag"  => "Wednesday",
            "donderdag" => "Thursday",
            "vrijdag"   => "Friday",
            "zaterdag"  => "Saturday",
            "zondag"    => "Sunday"
        );

        $months = array(
            "januari"   => "January",
            "februari"  => "February",
            "maart"     => "March",
            "april"     => "April",
            "mei"       => "May",
            "juni"      => "June",
            "juli"      => "July",
            "augustus"  => "August",
            "september" => "September",
            "oktober"   => "October",
            "november"  => "November",
            "december"  => "December"
        );

        $array = explode(" ", $datetime);
        $array[0] = $days[strtolower($array[0])];
        $array[2] = $months[strtolower($array[2])];
        return strtotime(implode(" ", $array));
    }
    public function sendcustomemailbackup(Request $request)
    {

        $email_templates_id = $request->email_templates;
        $appointment_id = $request->appointment_id;

        $appointment = Appointment::where('id','=', $appointment_id)->get();

        $client_id = $appointment[0]->client_id;
        $employee_id = $appointment[0]->employee_id;
        $location_id = $appointment[0]->location_id;
        $booking_date = date('d-m-Y',strtotime($appointment[0]->start_time));
        $service_id = $appointment[0]->service_id;
        $clientdetail = Client::where('id','=', $client_id)->get();

        $clientname   = $clientdetail[0]->first_name ." ". $clientdetail[0]->last_name;
        $clientemail = $clientdetail[0]->email;
        $address = $clientdetail[0]->address;
        $clientphone = $clientdetail[0]->phone;

        $employeedetail = Employee::where('id','=', $employee_id)->get();

        $therapistname  = $employeedetail[0]->first_name." ". isset($employeedetail[0]->first_name) ? $employeedetail[0]->first_name.' '.$employeedetail[0]->last_name : '';

        $therapisttelephone = $employeedetail[0]->phone;
        $locationstreetname = $employeedetail[0]->address;
        $thrapist_email = $employeedetail[0]->email;
        $therapistregistrations = $employeedetail[0]->registration_no;
        $servicedetail = Service::where('id','=', $service_id)->get();

        $thrapyname   = $servicedetail[0]->name;
        $therapistdes   = $servicedetail[0]->description;
        $therapistdes2   = $servicedetail[0]->description_second;
        $locationdetail = Location::where('id','=', $location_id)->get();
        $location   = $locationdetail[0]->location_name;
        $location_address   = $locationdetail[0]->location_address;
        $route_directions   = $locationdetail[0]->location_description  ;


        $email_templates = EmailTemplate::where('id','=', $email_templates_id)->get();

        $matter = $email_templates[0]->email_content;
        $email_subject = $email_templates[0]->email_subject;
        $email_attachment_path='';
        $email_attachment_url="";
        $email_filename_attachment='';
        if(isset($email_templates[0]->attachment))
        {
            $email_filename_attachment = $email_templates[0]->attachment;
            $email_attachment_path = public_path('/upload/'.$email_filename_attachment);
            $email_attachment_url = url('/public/upload/'.$email_filename_attachment);
        }
        /*  1 Customer name = {clientname}
 2 booking time and date = {booking_date}
 3 therapist name = {therapistname}
 4 therapy name = {thrapyname}
 5 therapist title = {therapistitle}
 6 therapist telephone number = {therapisttelephone}
 7 location streetname = {locationstreetname}
 8 location city = {location}
 9 route direction to location = {route_directions}
 10 therapist registrations = {therapistregistrations}
 11 therapy discription = {therapistdes} */

        $tharpy_prices = DB::table('services')
            ->leftjoin('service_extra_cost','services.id','=','service_extra_cost.service_id')
            ->where('services.id', '=', $service_id)
            ->get();

        //dd($tharpy_price);
        $totalCost=0; $no_of_block=4;
        foreach($tharpy_prices as $tharpy_price)
        {
            $extrCost= true;
            $serviceName = $tharpy_price->name;
            $duration_block = $tharpy_price->booking_block_duration;
            $no_of_block = $tharpy_price->min_block_duration;
            $block_cost = $tharpy_price->block_cost;
            $block_types = $tharpy_price->booking_series_type;
            $extra_cost_unit = $tharpy_price->booking_block_cost_duration_type_unit;
            $extra_cost_price_startTime = $tharpy_price->booking_pricing_time_from;
            $sessionCost = $tharpy_price->block_cost *  $no_of_block;
            if($extrCost)
            { $totalCost = $tharpy_price->block_cost *  $no_of_block;}


            if($request->starting_time.":00" > $extra_cost_price_startTime && $extrCost)
            {
                $extrCost=false;
                $totalCost = $totalCost  + ($tharpy_price->booking_block_pricing *  $no_of_block);
            }

            $extra_cost_price_endTime = $tharpy_price->booking_pricing_time_to;
        }

        if(!empty($clientname))
        { $matter = str_replace("{clientname}",$clientname,$matter); }
        else
        { $matter = str_replace("{clientname}",'',$matter); }

        if(!empty(trim($clientemail)))
        {$matter = str_replace("{customeremail}",$clientemail,$matter);}
        else
        {$matter = str_replace("{customeremail}",'',$matter);}

        if(!empty(trim($clientphone)))
        { $matter = str_replace("{customertelephonenumber}",$clientphone,$matter); }
        else
        { $matter = str_replace("{customertelephonenumber}",'',$matter); }
        if(!empty(trim($location_address)))
        {$matter = str_replace("{location_address}",$location_address,$matter);}
        else
        {$matter = str_replace("{location_address}",'',$matter);}
        if(!empty($therapistname))
        {$matter = str_replace("{therapistname}",$therapistname,$matter);}
        else
        {$matter = str_replace("{therapistname}",'',$matter);}

        if(!empty($tharpyname))
        {$matter = str_replace("{thrapyname}",$tharpyname,$matter);}
        else
        {$matter = str_replace("{thrapyname}",'',$matter);}

        if(!empty($location))
        {$matter = str_replace("{location}",$location,$matter);}
        else
        {$matter = str_replace("{location}",'',$matter);}

        if(!empty($route_directions))
        {$matter = str_replace("{route_directions}",$route_directions,$matter);}
        else
        {$matter = str_replace("{route_directions}",'',$matter);}

        if(!empty($therapisttelephone))
        {$matter = str_replace("{therapisttelephone}",$therapisttelephone,$matter);}
        else
        {$matter = str_replace("{therapisttelephone}",'',$matter);}

        if(!empty($therapistregistrations))
        {$matter = str_replace("{therapistregistrations}",$therapistregistrations,$matter);}
        else
        {$matter = str_replace("{therapistregistrations}",'',$matter);}

        if(!empty($therapistname))
        {$matter = str_replace("{therapistname}",$therapistname,$matter);}
        else
        {$matter = str_replace("{therapistname}",'',$matter);}

        if(!empty($thrapist_email))
        {$matter = str_replace("{therapistemail}",$thrapist_email,$matter);}
        else
        {$matter = str_replace("{therapistemail}",'',$matter);}

        /*19 Go to calandar booking date = {r_calandar_booking_date}
        20 Booking view = {go_booking_view}*/
        // go_booking_view
        $showView =  "<a href=".url('admin/appointments/'.$appointment_id).">Show View</a>";

        $matter = str_replace("{go_booking_view}",$showView,$matter);


        if(!empty(trim($sessionCost)))
        {$matter = str_replace("{session_costs_for_an_hour}",$sessionCost,$matter);}
        else
            $matter = str_replace("{session_costs_for_an_hour}",'',$matter);

        Date::setLocale('nl');
        $dateT = explode(' ', $appointment[0]->start_time);

        $dateT[1] = date('H:i',strtotime($dateT[1]));
 

        $matter = str_replace("{booking_date}","".Date::parse($dateT[0])->format('l j F Y'),$matter);
        $matter = str_replace("{booking_time}","".$dateT[1],$matter);



        if(!empty($therapistdes))
        {$matter = str_replace("{therapistdes}",$therapistdes,$matter);}
        else
        {$matter = str_replace("{therapistdes}",'',$matter);}

        if(!empty($therapistdes2))
        {$matter = str_replace("{therapistdes2}",$therapistdes2,$matter);}
        else
        {$matter = str_replace("{therapistdes2}",'',$matter);}

        $matter = Appointment::EmailContentTLT($matter);
      //  echo "<pre>"; print_r($matter); die;
        //$email_attachment_path='';
        //$email_filename_attachment='';
        if($email_attachment_path=='')
        {

            //  $clientemail="sharad@ecybertech.com";
            // $clientemail = "engrsk60@gmail.com";


            Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
                $message->to($clientemail)
                    ->from("info@praktijk-afspraken.nl")
                    ->subject($email_subject)
                    ->setBody($matter, 'text/html')->setContentType('text/html'); // for HTML rich messages
            });
        }
        else
        {
            $info = pathinfo($email_attachment_path);


            $ext = isset($info['extension']) ? $info['extension'] : "";


            if(!empty($ext)){
           //     $clientemail = "engrsk60@gmail.com";
               $email_attachment_path = URL::to('/').'/public/upload/'.$email_filename_attachment;
           //     echo "<pre>"; print_r($email_attachment_path); die;
                $application = 'application/'.$ext;
               /// $application = 'application/pdf';
                Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter,$email_attachment_path,$email_filename_attachment,$application) {
                    $message->to($clientemail)
                    ->from("info@praktijk-afspraken.nl")
                        ->subject($email_subject)
                        ->attach($email_attachment_path, [
                            'as' => $email_filename_attachment,
                            'mime' => $application,
                        ])
                        ->setBody($matter, 'text/html')->setContentType('text/html'); // for HTML rich messages
                });

            }else{
               // $clientemail = "engrsk60@gmail.com";
                Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
                    $message->to($clientemail)
                        ->from("info@praktijk-afspraken.nl")
                        ->subject($email_subject)
                        ->setBody($matter, 'text/html')->setContentType('text/html'); // for HTML rich messages
                });
            }

        }

        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        $Logdata = new Logdata;
        $Logdata->log_from="Appoinment";
        $Logdata->refrance_id = $request->appointment_id;
        $Logdata->log_datetime = date("Y-m-d h:i:s");
        $userL = \Auth::user();
        $Logdata->tr_by = $userL->id;
        if(!empty($email_attachment_url))
        { $Logdata->message = "Send Appointment Mail"; }
        else
        { $Logdata->message = "Send Appointment Mail <a href='".$email_attachment_url."'>".$email_attachment_url."</a>"; }
        $Logdata->save();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
       return redirect()->route('admin.appointments.index');
//        return response()->json(
//                [
//                        'message' => 'Email sent Successfully',
//                        'status' => true
//                ]
//        );
    }
    public function sendcustomemail(Request $request)
    {

        $email_templates_id = $request->email_templates;
        $appointment_id = $request->appointment_id;

        $appointment = Appointment::where('id','=', $appointment_id)->get();

        $client_id = $appointment[0]->client_id;
        $employee_id = $appointment[0]->employee_id;
        $location_id = $appointment[0]->location_id;
        $booking_date = date('d-m-Y',strtotime($appointment[0]->start_time));
        $service_id = $appointment[0]->service_id;
        $clientdetail = Client::where('id','=', $client_id)->get();

        $clientname   = $clientdetail[0]->first_name ." ". $clientdetail[0]->last_name;
        $clientemail = $clientdetail[0]->email;
        $address = $clientdetail[0]->address;
        $clientphone = $clientdetail[0]->phone;

        $employeedetail = Employee::where('id','=', $employee_id)->get();

        $therapistname  = $employeedetail[0]->first_name." ". isset($employeedetail[0]->first_name) ? $employeedetail[0]->first_name.' '.$employeedetail[0]->last_name : '';
        $therapisttelephone = $employeedetail[0]->phone;
        $locationstreetname = $employeedetail[0]->address;
        $thrapist_email = $employeedetail[0]->email;
        $therapistregistrations = $employeedetail[0]->registration_no;
        $servicedetail = Service::where('id','=', $service_id)->get();

        $thrapyname   = $servicedetail[0]->name;
        $therapistdes   = $servicedetail[0]->description;
        $therapistdes2   = $servicedetail[0]->description_second;
        $locationdetail = Location::where('id','=', $location_id)->get();
        $location   = $locationdetail[0]->location_name;
        $location_address   = $locationdetail[0]->location_address;
        $route_directions   = $locationdetail[0]->location_description  ;


        $email_templates = EmailTemplate::where('id','=', $email_templates_id)->get();

        $matter = $email_templates[0]->email_content;
        $email_subject = $email_templates[0]->email_subject;
        $email_attachment_path='';
        $email_attachment_url="";
        $email_filename_attachment='';
        if(isset($email_templates[0]->attachment))
        {
            $email_filename_attachment = $email_templates[0]->attachment;
            $email_attachment_path = public_path('/upload/'.$email_filename_attachment);
            $email_attachment_url = url('/public/upload/'.$email_filename_attachment);
        }


        $tharpy_prices = DB::table('services')
            ->leftjoin('service_extra_cost','services.id','=','service_extra_cost.service_id')
            ->where('services.id', '=', $service_id)
            ->get();

        //dd($tharpy_price);
        $totalCost=0; $no_of_block=4;
        foreach($tharpy_prices as $tharpy_price)
        {
            $extrCost= true;
            $serviceName = $tharpy_price->name;
            $duration_block = $tharpy_price->booking_block_duration;
            $no_of_block = $tharpy_price->min_block_duration;
            $block_cost = $tharpy_price->block_cost;
            $block_types = $tharpy_price->booking_series_type;
            $extra_cost_unit = $tharpy_price->booking_block_cost_duration_type_unit;
            $extra_cost_price_startTime = $tharpy_price->booking_pricing_time_from;
            $sessionCost = $tharpy_price->block_cost *  $no_of_block;
            if($extrCost)
            { $totalCost = $tharpy_price->block_cost *  $no_of_block;}


            if($request->starting_time.":00" > $extra_cost_price_startTime && $extrCost)
            {
                $extrCost=false;
                $totalCost = $totalCost  + ($tharpy_price->booking_block_pricing *  $no_of_block);
            }

            $extra_cost_price_endTime = $tharpy_price->booking_pricing_time_to;
        }

        if(!empty($clientname))
        { $matter = str_replace("{clientname}",$clientname,$matter); }
        else
        { $matter = str_replace("{clientname}",'',$matter); }

        if(!empty(trim($clientemail)))
        {$matter = str_replace("{customeremail}",$clientemail,$matter);}
        else
        {$matter = str_replace("{customeremail}",'',$matter);}

        if(!empty(trim($clientphone)))
        { $matter = str_replace("{customertelephonenumber}",$clientphone,$matter); }
        else
        { $matter = str_replace("{customertelephonenumber}",'',$matter); }
        if(!empty(trim($location_address)))
        {$matter = str_replace("{location_address}",$location_address,$matter);}
        else
        {$matter = str_replace("{location_address}",'',$matter);}
        if(!empty($therapistname))
        {$matter = str_replace("{therapistname}",$therapistname,$matter);}
        else
        {$matter = str_replace("{therapistname}",'',$matter);}

        if(!empty($tharpyname))
        {$matter = str_replace("{thrapyname}",$tharpyname,$matter);}
        else
        {$matter = str_replace("{thrapyname}",'',$matter);}

        if(!empty($location))
        {$matter = str_replace("{location}",$location,$matter);}
        else
        {$matter = str_replace("{location}",'',$matter);}

        if(!empty($route_directions))
        {$matter = str_replace("{route_directions}",$route_directions,$matter);}
        else
        {$matter = str_replace("{route_directions}",'',$matter);}

        if(!empty($therapisttelephone))
        {$matter = str_replace("{therapisttelephone}",$therapisttelephone,$matter);}
        else
        {$matter = str_replace("{therapisttelephone}",'',$matter);}

        if(!empty($therapistregistrations))
        {$matter = str_replace("{therapistregistrations}",$therapistregistrations,$matter);}
        else
        {$matter = str_replace("{therapistregistrations}",'',$matter);}

        if(!empty($therapistname))
        {$matter = str_replace("{therapistname}",$therapistname,$matter);}
        else
        {$matter = str_replace("{therapistname}",'',$matter);}

        if(!empty($thrapist_email))
        {$matter = str_replace("{therapistemail}",$thrapist_email,$matter);}
        else
        {$matter = str_replace("{therapistemail}",'',$matter);}

        /*19 Go to calandar booking date = {r_calandar_booking_date}
        20 Booking view = {go_booking_view}*/
        // go_booking_view
        $showView =  "<a href=".url('admin/appointments/'.$appointment_id).">Show View</a>";

        $matter = str_replace("{go_booking_view}",$showView,$matter);


        if(!empty(trim($sessionCost)))
        {$matter = str_replace("{session_costs_for_an_hour}",$sessionCost,$matter);}
        else
            $matter = str_replace("{session_costs_for_an_hour}",'',$matter);

        Date::setLocale('nl');
        $dateT = explode(' ', $appointment[0]->start_time);

        $dateT[1] = date('H:i',strtotime($dateT[1]));
        $matter = str_replace("{booking_date}","".Date::parse($dateT[0])->format('l j F Y'),$matter);
        $matter = str_replace("{booking_time}","".$dateT[1],$matter);



        if(!empty($therapistdes))
        {$matter = str_replace("{therapistdes}",$therapistdes,$matter);}
        else
        {$matter = str_replace("{therapistdes}",'',$matter);}

        if(!empty($therapistdes2))
        {$matter = str_replace("{therapistdes2}",$therapistdes2,$matter);}
        else
        {$matter = str_replace("{therapistdes2}",'',$matter);}

        $matter = Appointment::EmailContentTLT($matter);
        return response()->json(['html' => '<div class="swiper-slide"><textarea class="form-control edi" name="edi" rows="10" cols="60">'.$matter.'</textarea></div>']);



    }
    public function sendcustomemailconfirmed(Request $request)
    {
       try {
            $email_templates_id = $request->email_templates;

            $appointment_id = $request->appointment_id;
            $email_templates = EmailTemplate::where('id','=', $email_templates_id)->get();

            $matter = $request['matter'];
            $email_subject = $email_templates[0]->email_subject;
            $appointment = Appointment::where('id','=', $appointment_id)->get();
            $client_id = $appointment[0]->client_id;

            $clientdetail = Client::where('id','=', $client_id)->get();
            $clientemail = $clientdetail[0]->email;
         //   $clientemail = "engrsk60@gmail.com";
            $email_attachment_path='';
            $email_attachment_url="";
          // $clientemail="engrsk60@gmail.com";
            $email_filename_attachment='';
            if(isset($email_templates[0]->attachment))
            {
                $email_filename_attachment = $email_templates[0]->attachment;
                $email_attachment_path = public_path('/upload/'.$email_filename_attachment);
                $email_attachment_url = url('/public/upload/'.$email_filename_attachment);
            }
            if($email_attachment_path=='')
            {


                Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
                    $message->to($clientemail)
                        ->from("info@praktijk-afspraken.nl")
                        ->subject($email_subject)
                        ->setBody($matter, 'text/html')->setContentType('text/html'); // for HTML rich messages
                });
            }
            else
            {
                $info = pathinfo($email_attachment_path);


                $ext = isset($info['extension']) ? $info['extension'] : "";


                if(!empty($ext)){

                    $email_attachment_path = URL::to('/').'/public/upload/'.$email_filename_attachment;
                    //     echo "<pre>"; print_r($email_attachment_path); die;
                    $application = 'application/'.$ext;
                    /// $application = 'application/pdf';
                    Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter,$email_attachment_path,$email_filename_attachment,$application) {
                        $message->to($clientemail)
                            ->from("info@praktijk-afspraken.nl")
                            ->subject($email_subject)
                            ->attach($email_attachment_path, [
                                'as' => $email_filename_attachment,
                                'mime' => $application,
                            ])
                            ->setBody($matter, 'text/html')->setContentType('text/html'); // for HTML rich messages
                    });

                }else{

                    Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
                        $message->to($clientemail)
                            ->from("info@praktijk-afspraken.nl")
                            ->subject($email_subject)
                            ->setBody($matter, 'text/html')->setContentType('text/html'); // for HTML rich messages
                    });
                }

            }
           return response()->json(['message' => 'Email sent successfully','status' => true]);
        }catch (\Exception $exception){
             return response()->json(['message' => 'Error sending email. Please refresh page or select another.','status' => false]);
        }
    }
    public function changeinvoicestatusP(Request $request)
    {
        Date::setLocale('nl');
		
		if($request->is_save == 'save'){

			if(!$request->client_name){
				return back();
			}
			$appointment = Appointment::findOrFail($request->appointment_id);


			$prev_client = Client::where('id',$request->client_id)->first();

			$MsgStatusNameChanged = 'Client name changed to '.$request->client_name.' '.' previously named as '.$prev_client->first_name.' '.$prev_client->last_name;
			//die($MsgStatusNameChanged);
			$Logdata = new Logdata;
			$Logdata->log_from="Appoinment";
			$Logdata->refrance_id = $appointment->id;
			$Logdata->log_datetime = date("Y-m-d h:i:s");
			$userL = \Auth::user();
			$Logdata->tr_by = $userL->id;
			$Logdata->message = $MsgStatusNameChanged;
			$Logdata->save();
			$appointment->client_id = $request->client_id;
			$appointment->update();

			return redirect()->back();

		}




			
        /*if($request->booking_status=="booking_paid_pin")
          { $status = "paid"; }
        elseif($request->booking_status=="cash_paid")
          { $status = "cash_paid"; }
        elseif($request->booking_status=="booking_unpaid")
          { $status = "unpaid"; }  */

        $appointment = Appointment::findOrFail($request->appointment_id);

        $CmpDtl = \App\TblCompanies::find($appointment->company_id);

        $Oldbooking_status = $appointment->booking_status;
        //$Oldbooking_status = $appointment->booking_status;
        $MsgStatus = "";
        if( $Oldbooking_status != $request->booking_status )
        { $MsgStatus=$MsgStatus."Change Payment Status from ".$Oldbooking_status." to ".$request->booking_status; }

        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
        $InvoiceDetail = Invoice::where('appointment_id', $request->appointment_id)->first();
        if(empty($InvoiceDetail->inv_date))
        {

            $InvoiceDetail = Appointment::ApInvoice($request->appointment_id);
            $InvoiceDetail = Invoice::where('appointment_id', $request->appointment_id)->first();
        }

        $ArrInvoice['inv_date'] = $request->inv_date;
        if(!empty($request->display_inv_no))
        {
            if($InvoiceDetail->display_inv_no != $request->display_inv_no)
            { $ArrInvoice['display_inv_no'] = $request->display_inv_no; }
        }

        $ArrInvoice['due_date'] = $request->due_date;

        if(!empty($request->payment_received))
        { $ArrInvoice['payment_received'] = json_encode($request->payment_received); }

        $ArrInvoice['price'] = $request->price;
        $ArrInvoice['prd_description'] = $request->prd_description;
        $ArrInvoice['tax_inc_exc'] = $request->tax_inc_exc;
        $ArrInvoice['taxid'] = $request->taxid;
        $ArrInvoice['taxrate'] = $request->taxrate;
        $ArrInvoice['taxrate_title'] = $request->taxrate_title;
        $ArrInvoice['taxamount'] = $request->taxamount;
        $ArrInvoice['baseamount'] = $request->baseamount;
        $ArrInvoice['netamount'] = $request->netamount;

        /*if($request->booking_status == "booking_paid_pin")
          { $ArrInvoice['footer_description'] = $CmpDtl->cmp_paid_inv_desc; }
        elseif($request->booking_status == "cash_paid")
          { $ArrInvoice['footer_description'] = $CmpDtl->cmp_cashpaid_inv_desc; }
        elseif($request->booking_status == "booking_unpaid")
          { $ArrInvoice['footer_description'] = $CmpDtl->cmp_unpaid_inv_desc; }*/
        $ArrInvoice['updated_at'] = date("Y-m-d h:i:s");
        if(!empty($InvoiceDetail->inv_date))
        {
            foreach ($ArrInvoice as $key => $value) {

                if($InvoiceDetail->$key!=$value)
                {
                    if( ($key!="payment_received") && ($key!="taxid") && ($key!="taxrate") && ($key!="footer_description")  && ($key!="updated_at"))
                    {
                        if($MsgStatus!=""){ $MsgStatus = $MsgStatus."<br>"; }
                        $MsgStatus = $MsgStatus."Change ".$key." from ".$InvoiceDetail->$key." to ".$value;
                    }
                }
                $InvoiceDetail->$key = $value;
            }
            $InvoiceDetail->update();

            $CmpDtl = \App\TblCompanies::find($appointment->company_id);
            $inv_number = $InvoiceDetail->inv_number;
            if(strlen($inv_number)==1){$inv_number="000".$inv_number;}
            elseif(strlen($inv_number)==2){$inv_number="00".$inv_number;}
            elseif(strlen($inv_number)==3){$inv_number="0".$inv_number;}
            $appointment->moneybird_invoice_id = $InvoiceDetail->inv_fyear."-".$inv_number;

        }


        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

        $appointment->price = $request->netamount;

        $appointment->booking_status =$request->booking_status;
        $appointment->paid_waya =$request->paid_waya;

        /*if(($request->booking_status=="partial_paid") || ($request->booking_status=="booking_unpaid") || ($request->booking_status=="booking_paid_pin"))
        {
          $appointment->booking_status = $request->booking_status;
          if($request->booking_status=="booking_paid_pin")
            {
              if($appointment->paid_waya!="Mollie")
              { $appointment->paid_waya  = ""; }
            }
          else
            { $appointment->paid_waya  = ""; }
        }
        elseif(($request->booking_status=="cash_paid") || ($request->booking_status=="bank_paid"))
        {
          $appointment->booking_status = 'booking_paid_pin';
          if($request->booking_status=="cash_paid")
            { $appointment->paid_waya = 'Cash'; }
          else
            { $appointment->paid_waya = 'Bank'; }
        }*/

        if(empty($appointment->original_booking_status))
        { $appointment->original_booking_status = $request->booking_status; }

        if(isset($request->extra_price_comment))
        { $appointment->extra_price_comment = $request->extra_price_comment; }

        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

        /* if($request->edittable== '1')
           { $dateM = $request->sdate;}
         else
           { $dateM = $appointment->start_time; }*/
        if($request->edittable=='1')
        {
            $startD = date('Y-m-d', strtotime($request->sdate));
            $startT = date('H:i:s',strtotime($request->start_time));
            $startF = date('H:i:s',strtotime($request->finish_time));
            $appointmentStartTime =  $startD." ".$startT;
            $appointmentFinishTime =  $startD." ".$startF;

            if($appointment->start_time != $appointmentStartTime)
            {
                if($MsgStatus!=""){ $MsgStatus = $MsgStatus."<br>"; }
                $MsgStatus="Change Start time from ".$appointment->start_time." To ".$appointmentStartTime;
            }
            $appointment->start_time = $appointmentStartTime;

            if($appointment->finish_time != $appointmentFinishTime)
            { if($MsgStatus!=""){$MsgStatus = $MsgStatus."<br>";}
                $MsgStatus="Change Finish time from ".$appointment->finish_time." To ".$appointmentFinishTime;
            }
            $appointment->finish_time = $appointmentFinishTime;
        }
        $appointment->update();
        $PdfUrl="";

        if(!empty($MsgStatus)){
            $Logdata = new Logdata;
            $Logdata->log_from="Appoinment";
            $Logdata->refrance_id = $request->appointment_id;
            $Logdata->log_datetime = date("Y-m-d h:i:s");
            $userL = \Auth::user();
            $Logdata->tr_by = $userL->id;
            $Logdata->message = $MsgStatus;
            $Logdata->save();
        }

        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        if(!empty($request->invoice_send_to))
        {
            $invoiceemail = $request->invoice_send_to;
            if(!empty($request->client_name)){
              //  die('asd');
                $this->sendinvoiceEmail($invoiceemail,$request->client_id,$request->client_name,$request->appointment_id);
            }else{
              //  die('asassd');
                $this->sendinvoiceEmail($invoiceemail,$appointment->client_id,"",$request->appointment_id);
            }

        }else{
            if(!empty($request->client_name)){

                $prev_client = Client::where('id',$appointment->client_id)->first();

                $MsgStatusNameChanged = 'Client name changed to '.$request->client_name.' '.' previously named as '.$prev_client->first_name.' '.$prev_client->last_name;
                //die($MsgStatusNameChanged);
                $Logdata = new Logdata;
                $Logdata->log_from="Appoinment";
                $Logdata->refrance_id = $appointment->id;
                $Logdata->log_datetime = date("Y-m-d h:i:s");
                $userL = \Auth::user();
                $Logdata->tr_by = $userL->id;
                $Logdata->message = $MsgStatusNameChanged;
                $Logdata->save();
                $appointment->client_id = $request->client_id;
                $appointment->update();
                
            }
        }

        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
        return redirect()->back();
    }

    public function changeinvoicestatus($id,$status)
    {

        if (! Gate::allows('appointment_view')) { return abort(401); }
        $InvoiceDetail = Invoice::where('appointment_id', $id)->first();
        if(empty($InvoiceDetail->id))
        {
            $InvoiceDetail = Appointment::ApInvoice($id);
            //$InvoiceDetail = Invoice::where('appointment_id', $id)->first();
        }
        $appointment = Appointment::findOrFail($id);
        $Oldbooking_status = $appointment->booking_status;
        $MsgStatus = "";
        if( $Oldbooking_status == $status )
        { $MsgStatus=$MsgStatus."Change Payment Status from ".$Oldbooking_status." to ".$status; }


        Date::setLocale('nl');
        $totalPayment = $InvoiceDetail->price;

        if($status=='booking_paid_pin')
        { $appointment->booking_status = 'booking_paid_pin'; }
        else if($status=='bank_paid')
        {
            $appointment->booking_status = 'booking_paid_pin';
            $appointment->paid_waya  = "Bank";
        }
        else if($status=='cash_paid')
        {
            $appointment->booking_status = 'booking_paid_pin';
            $appointment->paid_waya  = "Cash";
        }
        else
        {
            $appointment->booking_status = 'booking_unpaid';
            $appointment->paid_waya  = "";
        }

        if(empty($appointment->original_booking_status))
        { $appointment->original_booking_status= $appointment->booking_status;}
        $appointment->save();
        $PdfUrl = "";
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
        if( ($appointment->booking_status=="booking_paid_pin") || ($appointment->booking_status=="booking_unpaid") )
        {
            //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX


            $PrintMetter = Appointment::ApInvoicePrintView($id,0);
            $email_filename_attachment = $appointment->moneybird_invoice_id."_".date("Y-m-dhis").'.pdf';



            $pdf = PDF::loadHTML($PrintMetter);
            $Strpath = public_path('/upload/'.$email_filename_attachment);
            $pdf->save($Strpath);
            $PdfUrl = url('/public/upload/'.$email_filename_attachment);
            //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
            //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
            $RetMessageArr = Appointment::GetInvoiceEmailContent($id,"",0);
            $email_subject =$RetMessageArr['email_subject'];
            $matter =$RetMessageArr['email_mtr'];
            //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
            $clientName = Client::find($appointment->client_id);
            $clientemail = $clientName->email;
            if(!empty($clientName->email_invoice))
            { $email_invoice = $clientName->email_invoice; }
            else
            { $email_invoice="";}

            $email_attachment_path = $Strpath;
            $info = pathinfo($email_attachment_path);
            $ext = $info['extension'];
            $application = 'application/'.$ext;
            $matter = Appointment::EmailContentTLT($matter);

            Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter,$email_attachment_path,$email_filename_attachment,$application,$email_invoice) {
                if($email_invoice=="")
                {
                    $message->to($clientemail)
                        ->from("info@praktijk-afspraken.nl")
                        ->subject($email_subject)
                        ->attach($email_attachment_path, [
                            'as' => $email_filename_attachment,
                            'mime' => $application,
                        ])->setBody($matter, 'text/html')
                        ->setContentType('text/html');
                }
                else
                {
                    $message->to($clientemail)
                        ->cc($email_invoice)
                        ->from("info@praktijk-afspraken.nl")
                        ->subject($email_subject)
                        ->attach($email_attachment_path, [
                            'as' => $email_filename_attachment,
                            'mime' => $application,
                        ])->setBody($matter, 'text/html')
                        ->setContentType('text/html');
                }

            });



            if(!empty($email_filename_attachment))
            {
                if($MsgStatus!=""){$MsgStatus = $MsgStatus."<br>";}

                $Filename=str_replace(".pdf","", $email_filename_attachment);
                $PdfUrl = url('/appointmentinv/'.$Filename);

                $MsgStatus=$MsgStatus."Send mail <a href='".$PdfUrl."'>pdf file</a>";
            }
        }

        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        $Logdata = new Logdata;
        $Logdata->log_from="Appoinment";
        $Logdata->refrance_id = $id;
        $Logdata->log_datetime = date("Y-m-d h:i:s");
        $userL = \Auth::user();
        $Logdata->tr_by = $userL->id;
        $Logdata->message = $MsgStatus;
        $Logdata->save();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data

        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

        //return redirect()->route('admin.appointments.index');
        return redirect()->back();

    }
    public function UpdateAppointmentStatus(Request $request)
    {
        $appointmentId =  $request->appointment_id;
        $appointment_status =  $request->appointment_status;
        $appointment = Appointment::findOrFail($appointmentId);
        $lodStatus = $appointment->status;
        $appointment->status = $request->appointment_status;
        $customerId = $appointment->client_id;
        $appointment->update();

        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        if($lodStatus != $request->appointment_status )
        {
            $Logdata = new Logdata;
            $Logdata->log_from="Appoinment";
            $Logdata->refrance_id = $appointmentId;
            $Logdata->log_datetime = date("Y-m-d h:i:s");
            $userL = \Auth::user();
            $Logdata->tr_by = $userL->id;
            $Logdata->message = "Status change from ".$lodStatus." to ".$request->appointment_status;
            $Logdata->save();
        }
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data


        /*$clientName = \App\Client::find($customerId);
        $moneybird_contact_id = $clientName->moneybird_contact_id;

       if(empty($moneybird_contact_id))
        {

          $contactSearchObject = Moneybird::contact();
          //  $moneybird_contact_id='271375336926610863';

          // $moneybird_contact_id='271375336926610863';
         $contactSearchObject = $contactSearchObject->search($clientName->email);
        // echo "<pre>";print_r($contactSearchObject);exit;
         if(empty($contactSearchObject))
            {
              $contactObject = Moneybird::contact();
               $contactObject->company_name = $clientName->company_name;
               $contactObject->firstname = $clientName->first_name;
               $contactObject->lastname = $clientName->last_name;
               $contactObject->send_estimates_to_email = $clientName->email;
               $contactObject->send_invoices_to_email = $clientName->email;
               $contactObject->save();
              $customer_moneybrid =   $contactObject->id;
            }
           else
           {
             $customer_moneybrid = $contactSearchObject[0]->id;
           }
         $clientName->moneybird_contact_id= $customer_moneybrid;
         $clientName->save();
         }*/
        return "success";

    }
    public function afterMoneybirdAuth(Request $request)
    {
        //dd($request);
        $code = $request['code'];


        //  7c425890af924c32635efde7a0be03212a940c5ec1aa0bff8f4c1a9ccb9d05bc
        // $connection->setAuthorizationCode($code);
        //  dd(Moneybird::connect());
        /*   $connection = Moneybird::connect();
           $connection->setRedirectUrl(config('moneybird.redirect_uri'));
           $connection->setClientId(config('moneybird.client_id'));
           $connection->setClientSecret(config('moneybird.client_secret'));
           $connection->setAuthorizationCode($code);
             try {
               $connection->connect();
           } catch (\Exception $e) {
               throw new Exception('Could not connect to Moneybird: ' . $e->getMessage());
           }
          $connection->setAccessToken($connection->getAccessToken());
           // Save the new tokens for next connections
          $moneybird = new Moneybird($connection);
          $administrations = $moneybird->administration()->getAll();
          dd($administrations);
          return $moneybird;*/

    }

    public function appointmentjson(Request $request)
    {
       // echo "<pre>"; print_r($request->all()); die;
        if(isset($request->therapist_id) && $request->therapist_id != 0){

            $employee = App\Employee::where('id',$request->therapist_id)->first();
            $user = App\User::where('id',$employee->user_id)->first();
        }else{
            $user =   Auth::getUser();
        }
        // die($user['id']);
        /*
    $therapist = App\Employee::where('deleted_at','=',NULL)->where('user_id','=',$user->id)->get();
         $employee_id = $therapist[0]->id;
        */
        // echo  $request->Dates;exit;
        $startDateArray =  explode("T", $request->Dates);
        $start_time = $startDateArray[0];
        $endDateArray =  explode("T", $request->end);
        $end_time = $endDateArray[0];

        $working_hours =  DB::table('employees')->select('working_hours.employee_id','working_hours.date'
            ,'start_time','finish_time','working_hours.days','working_hours.location_id','employees_rooms.room_id')
            ->where('user_id',$user->id)->whereBetween('date',[$start_time,$end_time])
            ->join('working_hours','working_hours.employee_id','employees.id')
            ->join('employees_rooms','employees_rooms.employee_id','employees.id')
            ->get();
        $leaves = [];
        $custom_timings = [];
        if(isset($request->therapist_id) && $request->therapist_id != 0){
            $leaves =  DB::table('employee_leaves')->select('employee_leaves.leave_date','leave_to_date','employees_rooms.room_id as room_id')
                ->where('employee_leaves.employee_id',$request->therapist_id)
                ->join('employees_rooms','employees_rooms.employee_id','employee_leaves.employee_id')
                ->get();

            $custom_timings =  DB::table('employee_customtimings')->select('employee_customtimings.id','employee_customtimings.employee_id','employee_customtimings.date','employee_customtimings.timing_type'
                ,'employee_customtimings.end_time','employee_customtimings.start_time','employees_rooms.location_id','employees_rooms.room_id')
                ->where('employees_rooms.employee_id',$request->therapist_id)->whereBetween('employee_customtimings.date',[$start_time,$end_time]);
//            if(isset($request->location_id)){
//                $custom_timings = $custom_timings->where('employee_customtimings.location_id',$request->location_id);
//            }
            $custom_timings = $custom_timings->join('employees_rooms','employees_rooms.employee_id','employee_customtimings.employee_id')
                ->get();
           //echo "<pre>"; print_r($custom_timings); die;
        }



        $rooms = DB::table('rooms')->select('id')->get();
        $temp = [];
        foreach ($rooms as $k => $v){
            $temp[$k] = $v->id;
        }




        //  die($user->id);

        if($request->location_id==0)
        {

            $appointments = DB::table('appointments')->join('clients', 'appointments.client_id', '=',
                'clients.id')->join('locations', 'appointments.location_id', '=', 'locations.id')->
            join('employees', 'appointments.employee_id', '=', 'employees.id')
                ->join('services', 'services.id', '=', 'appointments.service_id')
                ->select('appointments.id','clients.first_name','clients.last_name',
                    'appointments.room_id','locations.location_name','appointments.start_time',
                    'appointments.finish_time','employees.first_name AS emp_f_name',
                    'employees.last_name AS emp_l_name','services.name as service_name','employees.small_info')->
                whereRaw("DATE(appointments.start_time) >= '".$start_time."'")
                ->whereRaw("DATE(appointments.finish_time) <= '".$end_time."'");

            if(isset($request->therapist_id) && $request->therapist_id != 0){

                $appointments = $appointments->where('employees.user_id',$user->id);
            }


            $appointments = $appointments->where('appointments.deleted_at','=',NULL)->get();

            //die($appointments);
        }
        else{

            $appointments = DB::table('appointments')->join('clients', 'appointments.client_id', '=', 'clients.id')
                ->join('locations', 'appointments.location_id', '=', 'locations.id')->
                join('employees', 'appointments.employee_id', '=', 'employees.id')
                ->join('services', 'services.id', '=', 'appointments.service_id')
                ->select('appointments.id','clients.first_name','clients.last_name','appointments.room_id',
                    'locations.location_name','appointments.start_time','appointments.finish_time',
                    'employees.first_name AS emp_f_name','employees.last_name AS emp_l_name',
                    'services.name as service_name','employees.small_info')->
                whereRaw("DATE(appointments.start_time) >= '".$start_time."'")->
                whereRaw("DATE(appointments.finish_time) <= '".$end_time."'");
            if(isset($request->therapist_id) && $request->therapist_id != 0){
                $appointments = $appointments->where('employees.user_id',$user->id);
            }

            $appointments =  $appointments->where('appointments.location_id','=',$request->location_id)
                ->where('appointments.deleted_at','=',NULL)->get();
        }

        // echo "<pre>"; print_r($appointments); die;

        $dataJsonArray = array();
        foreach($appointments as $key => $value)
        { $fname=''; $lname=''; $emp_fname='';$emp_lname='';

            if(isset($value->first_name))
            {$fname=$value->first_name;}
            if(isset($value->last_name))
            {$lname=$value->last_name;}

            if(isset($value->emp_f_name))
            {$emp_fname=$value->emp_f_name;}
            if(isset($value->emp_l_name))
            {$emp_lname=$value->emp_l_name;}
            $LastString =  isset($emp_lname[0]) ? strtoupper($emp_lname[0]):'';

            $thapistName = strtoupper($emp_fname[0])."".$LastString;

            $smallInfoStr = "";
            if(!empty($value->small_info))
            { $smallInfoStr ='<br> <span style="color:#D64535;font-weight:bold">Short Info : </span> <b>'.$value->small_info.'</b>'; }

            $desc = '<span style="color:#D64535;font-weight:bold">Client Name : </span> <b>'.$fname.' '.$lname.'</b><br> <span style="color:#D64535;font-weight:bold">Therpist Name : </span><b>'. $emp_fname.' '.$emp_lname.'</b> <br> <span style="color:#D64535;font-weight:bold">Tharpy : </span> <b>'.$value->service_name.'</b> <br> <span style="color:#D64535;font-weight:bold">Location : </span> <b>'.$value->location_name.'</b>'.$smallInfoStr;
            $dataJsonArray[] =
                array('resourceId'=>$value->room_id,'title'=> $thapistName." - ".$fname.' '.$lname.' - '.$value->location_name,'description'=> $desc,'start'=>$value->start_time,'end'=>$value->finish_time,'url'=> "appointments/$value->id");

        }

        // echo json_encode($dataJsonArray,true); die;

//
//        if (count($leaves) > 0) {
//           foreach ($working_hours as $key => $value) {
//                foreach ($leaves as $k => $v) {
//                    $start_leave_date = date('Y-m-d', strtotime($v->leave_date));
//                    $end_leave_date = date('Y-m-d', strtotime($v->leave_to_date));
//                    if (($value->date >= $start_leave_date) && ($value->date <= $end_leave_date)) {
//                        unset($working_hours[$key]);
//                    }
//                }
//            }
//        }


        if(isset($request->therapist_id) && $request->therapist_id != 0){
            foreach ($leaves as $k => $value) {

                $dataJsonArray[] =
                    array('title' => "Leaves", 'description' => "Leaves", 'start' => date("Y-m-d\TH:i:s", strtotime($value->leave_date)),
                        'end' => date("Y-m-d\TH:i:s", strtotime($value->leave_to_date)),
                        'color' => "#ffcccb",
                        'resourceId' => $value->room_id );
            }


            foreach ($custom_timings as $key => $value) {

                if($value->timing_type == 'unavailable'){
                    $dataJsonArray[] =
                        array('title' => "Custom Timing", 'description' => "Not Available", 'start' => date("Y-m-d\TH:i:s", strtotime("" . $value->date . " " . $value->start_time . "")),
                            'end' => date("Y-m-d\TH:i:s", strtotime("" . $value->date . " " . $value->end_time . "")),
                            'color' => "#FFA500",
                            'resourceId'=> $value->room_id,
                            'loc' => $value->location_id,
                            'url' =>
                                URL::to('/') . '/admin/employees_customtiming/edit/' . $value->id);
                }else{
                    $dataJsonArray[] =
                        array('title' => "Custom Timing", 'description' => "Available", 'start' => date("Y-m-d\TH:i:s", strtotime("" . $value->date . " " . $value->start_time . "")),
                            'end' => date("Y-m-d\TH:i:s", strtotime("" . $value->date . " " . $value->end_time . "")),
                            'color' => "#008000",
                            'resourceId'=> $value->room_id,
                            'loc' => $value->location_id,
                            'url' =>
                                URL::to('/').'/admin/appointments/create?start_time='.$value->start_time.'&end_time='.$value->end_time.'&date='.$value->date.'&roomId='.$value->room_id.'&therapistId='.$request->therapist_id);
                }


            }


        }

        foreach ($working_hours as $key => $value) {

            if(in_array($value->room_id,$temp)){

                if(isset($request->therapist_id) && $request->therapist_id != 0){
                    $url = URL::to('/').'/admin/appointments/create?start_time='.$value->start_time.'&end_time='.$value->finish_time.'&date='.$value->date.'&roomId='.$value->room_id.'&therapistId='.$request->therapist_id;

                }else{
                    $url = URL::to('/').'/admin/appointments/create?start_time='.$value->start_time.'&end_time='.$value->finish_time.'&date='.$value->date.'&roomId='.$value->room_id.'&therapistId=';

                }

                $dataJsonArray[] =
                    array('title'=> "Available",'description'=> "Working hours",'start'=>  date("Y-m-d\TH:i:s", strtotime("".$value->date." ".$value->start_time."")),
                        'end'=>date("Y-m-d\TH:i:s", strtotime("".$value->date." ".$value->finish_time."")),
                        'rendering' => 'background',
                        'color' =>  "#54bf18",
                        'resourceId'=>$value->room_id,
                        'url'=>
                            $url );

            }

        }



        //  echo json_encode($dataJsonArray,true); die;
        return json_encode($dataJsonArray,true);
    }


    public function preparePayment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $InvoiceDetail = Appointment::ApInvoiceDetail($id);
        $inv_numberStr = $inv_number = $InvoiceDetail->inv_number;
        if(strlen($inv_number)==1){$inv_numberStr = "000".$inv_number;}
        elseif(strlen($inv_number)==2){$inv_numberStr = "00".$inv_number;}
        elseif(strlen($inv_number)==3){$inv_numberStr = "0".$inv_number;}
        $inv_numberStr = $InvoiceDetail->inv_fyear.'-'.$inv_numberStr;
        $prd_description=$InvoiceDetail->inv_fyear;

        $PaidAmt=0;
        if(!empty($InvoiceDetail->payment_received))
        {
            $Arrdata = json_decode($InvoiceDetail->payment_received);
            if(!empty($Arrdata->I->amount))
            { $PaidAmt = $PaidAmt + $Arrdata->I->amount; }
            if(!empty($Arrdata->II->amount))
            { $PaidAmt = $PaidAmt + $Arrdata->II->amount; }
        }
        $DueAmount =number_format($InvoiceDetail->netamount - $PaidAmt,2);
        //$DueAmount = $InvoiceDetail->netamount;
        $MOLLIE_KEY = config("custom.mollie_key");
        $webhook = route('webhooks.mollie');
        $ReturnUrl = route('admin.appointments.show',[$id]);
        Mollie::api()->setApiKey($MOLLIE_KEY); // your mollie

        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => 'EUR', /*Type of currency you want to send*/
                'value' => $DueAmount, /*You must send the correct number of decimals, thus we enforce the use of strings*/
            ],
            'description' => $inv_numberStr." \n ".$prd_description,
            'redirectUrl' => $ReturnUrl, // after the payment completion where you to redirect
            'webhookUrl' => $webhook,
            "metadata" => [ "order_id" => $id ]
        ]);

        $payment = Mollie::api()->payments()->get($payment->id);

        // redirect customer to Mollie checkout page
        return redirect($payment->getCheckoutUrl(), 303);
    }
    function updateExpireInvoices()
    {

        Appointment::where(function ($query)   { $query->where('status', '=', "booking_confirmed")->orWhere('status', '=', "booking_paid_pin");
        })
            ->where('status_rm_exp', '!=', 'P')
            ->where('booking_status', '=', 'booking_paid_pin')
            ->update(['status_rm_exp' => 'P']);


        Appointment::where('status', '=', 'booking_cancled')
            ->where('status_rm_exp', '!=', 'C')
            ->update(['status_rm_exp' => 'C']);





        $clientName = \App\TblCompanies::find(1);
        if(!empty($clientName->reminders_days))
        {
            $ArrrmDays = explode(",", $clientName->reminders_days);
            $ExpDays = end($ArrrmDays);
            if($ExpDays>0)
            {
                $ExpDays = $ExpDays+1;
                $ArrdateRm=date('Y-m-d', strtotime("-".$ExpDays." days"));

                $apmtExp = Appointment::select(
                    "appointments.id as appointments_id")->join('invoices','invoices.appointment_id','=','appointments.id')
                    ->whereDate("invoices.due_date",'<=',$ArrdateRm)
                    ->whereIn("appointments.booking_status",["booking_unpaid","booking_confirmed",'partial_paid'])->where('status', '=', 'booking_confirmed')
                    ->whereNotIn('status_rm_exp', ['E','P'])->get();
                if(!empty($apmtExp))
                {
                    foreach($apmtExp as $appointment)
                    {
                        $ApmtId = $appointment->appointments_id;
                        $appointment = Appointment::findOrFail($ApmtId);
                        $appointment->status_rm_exp='E';
                        $appointment->save();
                    }
                }
            }
        }


    }

    /**
     * Page redirection after the successfull payment
     *
     * @return Response
     */



}
