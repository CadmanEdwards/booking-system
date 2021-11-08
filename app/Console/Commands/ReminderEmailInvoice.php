<?php

namespace App\Console\Commands;

use App\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use Mail;
use Date;
use App\Logdata;

class ReminderEmailInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $tbl_company = DB::table('tbl_companies')->select('reminders_days')->first();
        $days = explode(',',$tbl_company->reminders_days);
        foreach ($days as $key => $value){

            $appointments = DB::select(DB::raw("select  `appointments`.id, `invoices`.`due_date`,DATEDIFF( CURRENT_DATE , `invoices`.`due_date`) as dif
                     from `appointments` inner join `invoices` on `invoices`.`appointment_id` = `appointments`.`id` where `appointments`.`booking_status` = 'booking_unpaid' and `appointments`.`deleted_at` IS NULL HAVING dif = $value"));
            $type_no = $key+1;
            $email_type = 'reminder_invoice_email_client_'.$type_no;


            foreach($appointments as $todatys_appointment)
            {
                $content_Arr =  Appointment::GetInvoiceEmailContent($todatys_appointment->id,$email_type,0);

                Mail::send([], [], function ($message) use ($content_Arr) {
                    $message->to($content_Arr['client_email'])
                   // $message->to('phoogewerf@hotmail.com')
                     ->cc(['emailpraktijkafspraken@gmail.com'])
                        ->from("info@praktijk-afspraken.nl")
                        ->subject($content_Arr['email_subject'])
                        ->setBody($content_Arr['email_mtr'], 'text/html'); // for HTML rich messages
                });

                $Logdata = new Logdata;
                $Logdata->log_from="Appoinment";
                $Logdata->refrance_id = $todatys_appointment->id;
                $Logdata->log_datetime = date("Y-m-d h:i:s");
                $Logdata->tr_by = 1;
                $Logdata->message = "Invoice email type ".$email_type." sent to ".$content_Arr['client_email'];
                $Logdata->save();
            }
        }

    }
}
