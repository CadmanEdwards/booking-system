<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Appointment;
use Carbon\Carbon;
use DB;
use Mail;
use Date;
class ReminderEmailClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Reminder Email To client';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      Date::setLocale('nl');
        //
        $todatys_appointments = DB::table('appointments')
          ->select('clients.first_name as fname','clients.last_name as lname','clients.email as client_email','clients.phone as client_phone','clients.address as client_address','clients.address as client_address','clients.house_number','clients.postcode','clients.moneybird_contact_id','employees.*','services.*','locations.*','appointments.*')
         ->join('clients','appointments.client_id','=','clients.id')        
         ->join('employees','appointments.employee_id','=','employees.id')        
         ->join('services','appointments.service_id','=','services.id')        
         ->join('locations','appointments.location_id','=','locations.id')        
         ->where('appointments.deleted_at',NULL)
         ->whereDate('start_time',Carbon::tomorrow())->get();
         Log::info('Log Created: Reminder Email console');


         
         

         foreach($todatys_appointments as $todatys_appointment)
               {

                   /*therpy pricing start*/
                     $tharpy_prices = DB::table('services')
                     ->leftjoin('service_extra_cost','services.id','=','service_extra_cost.service_id')        
                     ->where('services.id', '=', $todatys_appointment->service_id)
                     ->get();
                //dd($tharpy_price);
                    $totalCost=0; 
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

    $booking_t = date('H:i',strtotime($todatys_appointment->start_time));
   $matter = str_replace("{booking_time}", $booking_t,$matter);




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
    else {
      $matter = str_replace("{location}",'',$matter);}
    
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
   // $matter = str_replace("{booking_date}",date('l d F Y',strtotime($todatys_appointment->start_time)),$matter);
     //$clientemail = "sharad@ecybertech.com";


            //   $clientemail = "engrsk60@gmail.com";



         Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
              $message->to($clientemail)
                  ->from("info@praktijk-afspraken.nl")
                ->subject($email_subject)
                ->setBody($matter, 'text/html'); // for HTML rich messages
            });
    
//   exit;
       /*if(!empty($bcc_email_id))
         {
            Mail::send([], [], function ($message) use ($clientemail,$email_subject,$matter) {
                  $message->to($clientemail)
                    ->subject($email_subject)
                    ->setBody($matter, 'text/html'); // for HTML rich messages
                });
             
              

         }*/
                  
    }
                

    }
}
