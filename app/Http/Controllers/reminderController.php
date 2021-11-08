<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use App\Appointment;
use App\Logdata;
use App\Client;
use App\TblCompanies;
use App\EmailTemplate;
use Carbon\Carbon;
use DB;
use Mail;
use Date;
use Moneybird;
use PDF;
class reminderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      Date::setLocale('nl');

      $todatys_appointments = DB::table('appointments')
          ->select('appointments.id as appointment_id','clients.first_name as fname','clients.last_name as lname','clients.email as client_email','clients.phone as client_phone','clients.address as client_address','clients.address as client_address','clients.house_number','clients.postcode','clients.moneybird_contact_id','employees.*','services.*','locations.*','appointments.*')
         ->join('clients','appointments.client_id','=','clients.id')        
         ->join('employees','appointments.employee_id','=','employees.id')        
         ->join('services','appointments.service_id','=','services.id')        
         ->join('locations','appointments.location_id','=','locations.id') 
         ->where('appointments.deleted_at','=',NULL)       
         ->whereDate('start_time',Carbon::tomorrow())
         ->whereNull('switched_off_reminder_email')->
         get();
        
        
         

         foreach($todatys_appointments as $todatys_appointment)
               {

                   /*therpy pricing start*/
                     $tharpy_price = DB::table('services')
                     ->leftjoin('service_extra_cost','services.id','=','service_extra_cost.service_id')        
                     ->where('services.id', '=', $todatys_appointment->service_id)
                     ->get();
                //dd($tharpy_price);
                    $totalCost=0; 
                    $sessionCost=0;
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
                        
                         
                         if($todatys_appointment->start_time > $extra_cost_price_startTime && $extrCost)
                            { 
                              $extrCost=false;
                               $totalCost = $totalCost  + ($tharpy_price->booking_block_pricing *  $no_of_block);
                            }

                         $extra_cost_price_endTime = $tharpy_price->booking_pricing_time_to;
                      }

                  /*therapy pricing end*/

                   
                   
                   $price =   $todatys_appointment->price;
                   $status =   $todatys_appointment->status;

                   $tharpy_registration_no =   $todatys_appointment->registration_no;
                   $tharpyname =   $todatys_appointment->name;
                   $booking_block_duration =   $todatys_appointment->booking_block_duration;
                   $therapy_description =   $todatys_appointment->description;
                   $therapy_description2 =   $todatys_appointment->description_second;
                   

                   $locationname = $todatys_appointment->location_name;
                   $location_address = $todatys_appointment->location_address;
                   $locationdesc = $todatys_appointment->location_description;

                 
                  $client_name = $todatys_appointment->fname." ".$todatys_appointment->lname;
                  $clientemail = $todatys_appointment->client_email;
                  $clientphone = $todatys_appointment->client_phone;
                  $client_house_number = $todatys_appointment->house_number;
                  $client_postcode =   $todatys_appointment->postcode;
                  $client_address = $todatys_appointment->client_address;
                  $client_phone =   $todatys_appointment->client_phone;
                  $moneybird_contact_id = $todatys_appointment->moneybird_contact_id;
       
                $thrapist_name = $todatys_appointment->first_name." ".$todatys_appointment->last_name; 
                $thrapist_email = $todatys_appointment->email; 
                $therapisttelephone = $todatys_appointment->phone; 
                
                 $therapistdes='';$therapistdes2='';

                if(isset($todatys_appointment->description))
                {$therapistdes  = $todatys_appointment->description;}
             if(isset($todatys_appointment->description_second))
                {$therapistdes2  = $todatys_appointment->description_second;}

                   $email_customer_template = DB::table('email_templates')
          ->select('*')
         ->where('email_templates.email_type', '=', 'reminder_email_client')->get();
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

  if(!empty(trim($tharpy_registration_no)))
      {$matter = str_replace("{registrations_therapist}",$tharpy_registration_no,$matter);}
    else 
      {$matter = str_replace("{registrations_therapist}",'',$matter);}

 

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
     {$matter = str_replace("{session_costs_for_an_hour}",'',$matter);}
    $matter = str_replace("{booking_date}","".Date::parse($todatys_appointment->start_time)->format('l j F Y'),$matter);

     $dateT = explode(' ', $todatys_appointment->start_time);
      $time = explode(':', $dateT[1]); 
      $boolingTime = $time[0].":".$time[1];
      
     //$matter = str_replace("{booking_time}","".$dateT[1],$boolingTime);
     $matter = str_replace("{booking_time}",$boolingTime,$matter);
     $matter = Appointment::EmailContentTLT($matter);
   // $matter = str_replace("{booking_date}","".Date::parse($dateT[0])->format('l j F Y'),$matter);
    //$matter = str_replace("{booking_time}","".$dateT[1],$matter);

   // $matter = str_replace("{booking_date}",date('l d F Y',strtotime($todatys_appointment->start_time)),$matter);
    
     
         Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
              $message->to($clientemail)
                ->subject($email_subject)
                ->setBody($matter, 'text/html')
                ->setContentType('text/html')
                ; // for HTML rich messages

            }); 
    
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        $Logdata = new Logdata;
        $Logdata->log_from="Appoinment";
        $Logdata->refrance_id = $todatys_appointment->appointment_id;
        $Logdata->log_datetime = date("Y-m-d h:i:s");
        //$userL = \Auth::user();   
        $Logdata->tr_by = 1;//$userL->id; default sender admin 
        $Logdata->message = "Remainder mail sent";
        $Logdata->save();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
      }
    }
    
    public function invoicereminder()
    { 
      $this->updateExpireInvoices();
      
      $ArrdateRm=[];
      $clientName = TblCompanies::find(1);
      if(!empty($clientName->reminders_days))
      {
        $ArrrmDays = explode(",", $clientName->reminders_days);
        foreach ($ArrrmDays as $key => $days) 
        {
          if(!empty($days) && $days>0) 
            { 
              $ArrdateRm[] = date('Y-m-d', strtotime("-".$days." days")); 
            }
        }
      }
      foreach ($ArrdateRm as $key => $value) {
        $appointmentsReminder = Appointment::select( 
          "appointments.id as appointments_id","appointments.company_id as company_id",
         DB::raw("CONCAT(invoices.inv_fyear,'-',invoices.inv_number) AS Invoice_number"),
          DB::raw("CONCAT(clients.first_name,' ',clients.last_name) AS customer_name"),
          DB::raw("CONCAT(employees.first_name,' ',employees.last_name) AS therapist_name"), 'invoices.inv_date', 'invoices.due_date', 'clients.address', 
          'clients.house_number', 'clients.postcode', 'clients.city_name',
          'clients.email', 'clients.email_invoice','invoices.netamount','invoices.payment_received'
         )->join('invoices','invoices.appointment_id','=','appointments.id')
            ->join('clients', 'clients.id','=','appointments.client_id')
            ->Join('employees', 'employees.id', '=', 'appointments.employee_id')
            ->whereDate("invoices.due_date",$value)
            ->whereIn("appointments.booking_status",["booking_unpaid","booking_confirmed","partial_paid"])->get();
        if(!empty($appointmentsReminder))    
        {
          $tlt_type = "reminder_invoice_email_client_".($key+1);
          /*$email_template=EmailTemplate::where('email_type','=',$Svar)->first();
          $email_subject_data = $email_template->email_subject;*/
          foreach($appointmentsReminder as $appointment)
          { 
            //Appointment::GetInvoiceEmailContent($appointment->appointments_id,$Svar);
            $ApmId = $appointment->appointments_id;
            $clientemail = "";
            if(!empty($appointment->email))
              { $clientemail = $appointment->email;}
            if(!empty($appointment->email_invoice)) 
            {
              if($clientemail!=""){ $clientemail = $clientemail.",";}
              $clientemail = $clientemail.$appointment->email_invoice;
            }

            if(!empty($clientemail))
            {
               
             
              $ARRPdfFile = $this->createinvoicePdf($ApmId);
              $Strpath = $ARRPdfFile['filepath'] ;
              $PdfUrl = $ARRPdfFile['fileurl'];
              $email_filename_attachment = $ARRPdfFile['fileName'];


               
              /*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX*/
              $email_attachment_path = $Strpath;
              $ArrfileExtArr = explode(".", $email_attachment_path);
              $ext = end($ArrfileExtArr);
              $application = 'application/'.$ext;
              /*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX*/
              $RetArr = Appointment::GetInvoiceEmailContent($ApmId,$tlt_type,0);
          
              $email_subject = $RetArr['email_subject'];
              $BaseMetter = Appointment::EmailContentTLT($RetArr['email_mtr']);
              
            //  $RetArr['ExtraEmail']="phoogewerf@quicknet.nl";
              
              if(!empty($RetArr['ExtraEmail'])) 
              {
                if($clientemail!=""){ $clientemail = $clientemail.",";}
                $clientemail = $clientemail.$RetArr['ExtraEmail'];
              }


              
              $StremaiBcc=[];
              $ArrEmail = explode(",", $clientemail);
              $BaseEmail = $ArrEmail[0];
              if(count($ArrEmail)>1)
              {
               foreach ($ArrEmail as $keyEm => $valueEm) 
                { if($keyEm>0 && $valueEm!="") { $StremaiBcc[]=$valueEm;}}
              }
              $clientemail = $BaseEmail;
              
              
              /*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX*/

            // $clientemail="manorgoyal@gmail.com";
          Mail::send([], [], function ($message) use ($clientemail,$email_subject,$BaseMetter,$email_attachment_path,$email_filename_attachment,$application,$StremaiBcc) {

                if(!empty($StremaiBcc))
                {
                  $message->to($clientemail)
                    ->cc($StremaiBcc)
                   ->subject($email_subject)
                   ->attach($email_attachment_path, [
                    'as' => $email_filename_attachment,
                    'mime' => $application,])
                   ->setBody($BaseMetter, 'text/html')
                   ->setContentType('text/html')
                   ; 
                }
                else
                {
                  $message->to($clientemail)
                   ->subject($email_subject)
                   ->attach($email_attachment_path, [
                    'as' => $email_filename_attachment,
                    'mime' => $application,])
                   ->setBody($BaseMetter, 'text/html')
                   ->setContentType('text/html')
                   ; 
                }

          });
        

          

              $appointmentData = Appointment::findOrFail($ApmId);
              $appointmentData->status_rm_exp='R';
              $appointmentData->save();

              $MsgStatus="Send mail <a href='".$PdfUrl."'>".$PdfUrl."</a>"; 
              //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
              $Logdata = new Logdata;
              $Logdata->log_from="Appoinment";
              $Logdata->refrance_id = $ApmId;
              $Logdata->log_datetime = date("Y-m-d h:i:s");
              //$userL = \Auth::user();   
              $Logdata->tr_by = 1;
              $Logdata->message = $MsgStatus;
              $Logdata->save();
              //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data 
            }
          }  
        }
      }
      exit;
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
     
     
      

    $clientName = TblCompanies::find(1);
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

    function createinvoicePdf($id)
    {
      $appointment = Appointment::findOrFail($id);
      $View = Appointment::ApInvoicePrintView($id);
      $email_filename_attachment = $appointment->moneybird_invoice_id."_".date("Y-m-dhis").'.pdf';
      $pdf = PDF::loadHTML($View);
      $Strpath = public_path('/upload/'.$email_filename_attachment);
      $pdf->save($Strpath);
      $PdfUrl = url('/public/upload/'.$email_filename_attachment);
      //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
      $ArrPDF['filepath'] = $Strpath;
      $ArrPDF['fileName'] = $email_filename_attachment;
      $ArrPDF['fileurl'] = $PdfUrl;
      return $ArrPDF;
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
    public function clienttoken($token)
     {
        $cleintApproved = Client::where('verify_link','=',$token)->first();
       // dd($appointments);
        if(isset($cleintApproved))
        {
           $cleintApproved->email_verified = 1;
            $cleintApproved->update();
        
        } 
       return redirect()->to('/login'); 
     }
    public function token($token)
    {
              
        $appointments = Appointment::where('verify_token','=',$token)->first();
       // dd($appointments);
        if(isset($appointments))
        {
            $appointments->booking_status = '';
            $appointments->status = 'booking_confirmed';
            $appointments->update();

            //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
        $Logdata = new Logdata;
        $Logdata->log_from="Appoinment";
        $Logdata->refrance_id = $appointments->appointment_id;
        $Logdata->log_datetime = date("Y-m-d h:i:s");
        //$userL = \Auth::user();   
        $Logdata->tr_by = 1;//$userL->id; default sender admin 
        $Logdata->message = "booking confirmed";
        $Logdata->save();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX Log Data
           
            $clientName = \App\Client::find($appointments->client_id);
            $client_name = $clientName->first_name." ".$clientName->last_name;
            $clientemail = $clientName->email;
            $clientphone = $clientName->phone;
            $client_house_number = $clientName->house_number;
            $client_address = $clientName->adderss;
            
            $moneybird_contact_id = $clientName->moneybird_contact_id;
            if(empty($moneybird_contact_id))
              {
                  /*$contactSearchObject = Moneybird::contact();
                  $contactSearchObject = $contactSearchObject->search($clientName->email);
                 if(empty($contactSearchObject))
                 {
                    $contactObject = Moneybird::contact();
                    $contactObject->company_name = $clientName->company_name;
                    $contactObject->firstname = $clientName->first_name;
                    $contactObject->lastname = $clientName->last_name;
                    $contactObject->send_estimates_to_email = $clientName->email;
                    $contactObject->send_invoices_to_email = $clientName->email;
                    if(isset($clientName->address))
                     {$contactObject->address1 = $clientName->address;}    
                    if(isset($clientName->phone))
                     {$contactObject->phone = $clientName->phone; }
                    if(isset($clientName->phone))
                     {$contactObject->city = $clientName->city_name;}
                   if(isset($clientName->phone))
                     {$contactObject->zipcode = $clientName->postcode;}
                   

                    $contactObject->save();  

                    $clientName->moneybird_contact_id= $contactObject->id;
                    $clientName->status= 'approved';
                    $clientName->save();
                 } */
                $clientName->moneybird_contact_id= '1';
                $clientName->status= 'approved';
                $clientName->save();
              }
         
       //Confirmation email sended 

       $thrapist = \App\Employee::find($appointments->employee_id);
       $service  = \App\Service::find($appointments->service_id);
       $locations = \App\Location::find($appointments->location_id);
       $tharpy  = \App\Service::find($appointments->service_id);       
       $tharpyname = $tharpy->name;
       $locationname = $locations->location_name;
     
       $block_timing  = $tharpy->booking_block_duration;
       $tharpy_registration_no='';
       if(isset($employee->registration_no))
        {$tharpy_registration_no  = $employee->registration_no;}

       $therapistdes='';$therapistdes2='';
       if(isset($tharpy->description))
        {$therapistdes  = $tharpy->description;}
     if(isset($tharpy->description_second))
        {$therapistdes2  = $tharpy->description_second;}

       //$no_of_block  = $request->no_of_block;
        $thrapist_name = $thrapist->first_name." ".$thrapist->last_name; 
        $thrapist_email = $thrapist->email; 
        $therapisttelephone = $thrapist->phone; 
          
    
    $verify_appointment_token = md5(time().$thrapist_email);
 
    $block_timing  = $tharpy->booking_block_duration;
    $location_address = $locations->location_address;
    $locationdesc = $locations->location_description;
    $tharpy_price = DB::table('services')
         ->leftjoin('service_extra_cost','services.id','=','service_extra_cost.service_id')        
         ->where('services.id', '=', $appointments->service_id)
         ->get();
   /// dd($tharpy_price);
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
            
             
             if($appointments->start_time.":00" > $extra_cost_price_startTime && $extrCost)
                { 
                  $extrCost=false;
                   $totalCost = $totalCost  + ($tharpy_price->booking_block_pricing *  $no_of_block);
                }

             $extra_cost_price_endTime = $tharpy_price->booking_pricing_time_to;
          }
      $email_customer_template = DB::table('email_templates')
          ->select('*')
         ->where('email_templates.email_type','=','confirmation_customer_email_o')->get();
    
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
      { $matter = str_replace("{location_address}",'',$matter); }

      if(!empty(trim($therapisttelephone)))
        { $matter = str_replace("{therapisttelephone}",$therapisttelephone,$matter); }
      else
        { $matter = str_replace("{therapisttelephone}",'',$matter); }  
   if(!empty(trim($sessionCost)))
     {$matter = str_replace("{session_costs_for_an_hour}",$sessionCost,$matter);}
   else 
     $matter = str_replace("{session_costs_for_an_hour}",'',$matter);
     
     Date::setLocale('nl');

     $dateT = explode(' ', $appointments->start_time);
    
    $matter = str_replace("{booking_date}","".Date::parse($dateT[0])->format('l j F Y'),$matter);
    $matter = str_replace("{booking_time}","".$dateT[1],$matter);

  
     
         Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
              $message->to($clientemail)
                ->subject($email_subject)
                ->setBody($matter, 'text/html'); // for HTML rich messages
            }); 
      


            return redirect()->to('/admin/home');
        }
        else {
            return redirect()->to('/admin/home');
          }
    }
  function remindapointment()
  {
    Date::setLocale('nl');
    $Date=date("Y-m-d");
    $Arrdates[]="'".date('Y-m-d', strtotime($Date. ' + 7 days'))."'"; 
    $Arrdates[]="'".date('Y-m-d', strtotime($Date. ' + 14 days'))."'"; 
    $Arrdates[]="'".date('Y-m-d', strtotime($Date. ' + 21 days'))."'"; 
    $appointments = Appointment::where('booking_status','=','booking_unpaid')
    ->where('status','=','booking_confirmed')
    ->whereIN(DB::raw('DATE(start_time)'),$Arrdates)
    ->orderBy('id')->get();
    //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
         $email_customer_template = DB::table('email_templates')->select('*')
         ->where('email_templates.email_type', '=', 'confirmation_customer_email')->first();
        $email_subject = $email_customer_template->email_subject;
        foreach ($appointments as $key => $value) 
        {
          $clientName = Client::find($value->client_id);
          $clientemail = $clientName->email;
          Appointment::ApInvoice($value->id);
          $matter = Appointment::ApInvoicePrintView($value->id);
          Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
              $message->to($clientemail)
                ->subject($email_subject)
                ->setBody($matter, 'text/html'); // for HTML rich messages
            });   
        }
    //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX      
    exit;
        
        

  }
}
