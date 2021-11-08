<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Mollie\Laravel\Facades\Mollie;

use App\Appointment;
use App\Logdata;
use App\EmailTemplate;
use App\Client;
use App\Employee;
use App\Service;
use App\Location;
use App\Invoice;
use Date;

class MollieWebhookController extends Controller
{
  
  public function responce($id) 
  {
      Date::setLocale('nl');

      $appointment = Appointment::findOrFail($id);

      $employee_service=\App\EmployeeService::where('service_id','=',$appointment->service_id)->where('employee_id','=',$appointment->employee_id)->whereNull('deleted_at')->get();

      $TaxData = \App\TaxRate::where('active',"=",'1')->where('tax_rate_type',"=",'sales_invoice')->get();

      $CmpDtl = \App\TblCompanies::find($appointment->company_id);
      $InvoiceDetail = Appointment::ApInvoiceDetail($id);
      if(empty($InvoiceDetail)) { $InvoiceDetail = Appointment::ApInvoice($id); }

      if(!empty($InvoiceDetail->display_inv_no))
        { $inv_numberStr =$InvoiceDetail->display_inv_no;}
      elseif(!empty($InvoiceDetail->inv_number))
        {
          $inv_numberStr = $inv_number = $InvoiceDetail->inv_number; 
          if(strlen($inv_number)==1){$inv_numberStr = "000".$inv_number;}
          elseif(strlen($inv_number)==2){$inv_numberStr = "00".$inv_number;}
          elseif(strlen($inv_number)==3){$inv_numberStr = "0".$inv_number;}
          $inv_numberStr = $InvoiceDetail->inv_fyear.'-'.$inv_numberStr;
        }
        
        $user = \Auth::user();
        
        $relations = [
                    'employee_service' => $employee_service,
                    "TaxData" => $TaxData,
                    "inv_numberStr" => $inv_numberStr,
                    "InvoiceDetail" => $InvoiceDetail,
                    'CurrUser' => $user,
                    'CmpDtl' => $CmpDtl

                   ];
      
     return view('payment_responce', compact('appointment') + $relations);
  }

  public function show($id) 
  {
        Date::setLocale('nl');

        $appointment = Appointment::findOrFail($id);

        $employee_service=\App\EmployeeService::where('service_id','=',$appointment->service_id)->where('employee_id','=',$appointment->employee_id)->whereNull('deleted_at')->get();

        $TaxData = \App\TaxRate::where('active',"=",'1')->where('tax_rate_type',"=",'sales_invoice')->get();

        $CmpDtl = \App\TblCompanies::find($appointment->company_id);
        $InvoiceDetail = Appointment::ApInvoiceDetail($id);
        if(empty($InvoiceDetail)) { $InvoiceDetail = Appointment::ApInvoice($id); }

        if(!empty($InvoiceDetail->display_inv_no))
          { $inv_numberStr =$InvoiceDetail->display_inv_no;}
        elseif(!empty($InvoiceDetail->inv_number))
          {
            $inv_numberStr = $inv_number = $InvoiceDetail->inv_number; 
            if(strlen($inv_number)==1){$inv_numberStr = "000".$inv_number;}
            elseif(strlen($inv_number)==2){$inv_numberStr = "00".$inv_number;}
            elseif(strlen($inv_number)==3){$inv_numberStr = "0".$inv_number;}
            $inv_numberStr = $InvoiceDetail->inv_fyear.'-'.$inv_numberStr;
          }
          if(!empty($InvoiceDetail->payment_received))
            { $InvoiceDetail->payment_received = json_decode($InvoiceDetail->payment_received); }
          else
            {
              $payment_received['I']['amount']="";
              $payment_received['I']['date']="";
              $payment_received['II']['amount']="";
              $payment_received['II']['date']="";
              $InvoiceDetail->payment_received=json_decode(json_encode($payment_received));
            }
          $InvLog = Logdata::leftJoin('users', 'users.id', '=', 'logdatas.tr_by')->where('log_from','=','Appoinment')->where('refrance_id','=',$id)->orderBy('logdatas.id','desc')->get();
          
          $user = \Auth::user();
          
          $relations = [
                      'employee_service' => $employee_service,
                      'InvLog' => $InvLog,
                      'email_templates' => \App\EmailTemplate::whereNull('email_type')->get()->pluck('email_subject', 'id')->prepend('Please select email template', ''),
                       
                      "TaxData" => $TaxData,
                      "inv_numberStr" => $inv_numberStr,
                      "InvoiceDetail" => $InvoiceDetail,
                      'CurrUser' => $user

                     ];
        
       return view('appointments_show', compact('appointment') + $relations);
  }

  public function cpayment($id) 
    {
        $appointment = Appointment::findOrFail($id);
        $InvoiceDetail = Appointment::ApInvoiceDetail($id);

        if(!empty($InvoiceDetail->display_inv_no))
          {$inv_numberStr =  $InvoiceDetail->display_inv_no;}
        else
          {
            $inv_numberStr = $inv_number = $InvoiceDetail->inv_number; 
            if(strlen($inv_number)==1){$inv_numberStr = "000".$inv_number;}
            elseif(strlen($inv_number)==2){$inv_numberStr = "00".$inv_number;}
            elseif(strlen($inv_number)==3){$inv_numberStr = "0".$inv_number;}
            $inv_numberStr = $InvoiceDetail->inv_fyear.'-'.$inv_numberStr;
          }

        $prd_description = $InvoiceDetail->prd_description;
         
        $PaidAmt=0;
        if(!empty($InvoiceDetail->payment_received))
        {
         $Arrdata = json_decode($InvoiceDetail->payment_received);
          if(!empty($Arrdata->I->amount))
            { $PaidAmt = $PaidAmt + $Arrdata->I->amount; }
          if(!empty($Arrdata->II->amount))
            { $PaidAmt = $PaidAmt + $Arrdata->II->amount; }
        }
        $DueAmount =number_format($InvoiceDetail->netamount -$PaidAmt ,2);
        
        $MOLLIE_KEY =config("custom.mollie_key");
        
        $webhook = route('webhooks.mollie');
        /*$ReturnUrl = route('mollie.show',[$id]);*/
        $ReturnUrl = route('mollie.responce',[$id]);

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

    public function handle(Request $request) {
      
      if (! $request->has('id')) { return false; }

      $MOLLIE_KEY =config("custom.mollie_key");
      Mollie::api()->setApiKey($MOLLIE_KEY); // your mollie 
        $payment = Mollie::api()->payments()->get($request->id);
         
        if($payment->isPaid()) 
        {

          $ApmtId = $payment->metadata->order_id;
          $Amount = $payment->amount->value;
          $status = $payment->status;

           

          //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
          Date::setLocale('nl');
          $status = "paid"; 
         

          $appointment = Appointment::findOrFail($ApmtId);
          $Oldbooking_status = $appointment->booking_status;
          $MsgStatus="Change Payment Status from ".$Oldbooking_status." to paid";
          $InvoiceDetail = Invoice::where('appointment_id', $ApmtId)->first();
          if(empty($InvoiceDetail->inv_date))
          {
            $InvoiceDetail = Appointment::ApInvoice($ApmtId);
            $InvoiceDetail =Invoice::where('appointment_id', $ApmtId)->first();
          }


          $payment_received['I']['amount'] = "";
          $payment_received['I']['date'] = "";
          $payment_received['II']['amount'] = "";
          $payment_received['II']['date'] = "";
            if(!empty($InvoiceDetail->payment_received))
            {
              $payment_received = json_decode($InvoiceDetail->payment_received,true);
            }
            
            if(empty($payment_received['I']['amount']))
            {
              $payment_received['I']['amount'] = $Amount;
              $payment_received['I']['date'] = date("d-m-Y");
            }
            elseif(empty($payment_received['II']['amount']))
            {
              $payment_received['II']['amount'] = $Amount;
              $payment_received['II']['date'] = date("d-m-Y");
            }
            $InvoiceDetail->payment_received = json_encode($payment_received); 
            $InvoiceDetail->updated_at = date("Y-m-d h:i:s");
            $InvoiceDetail->update();

            //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
            
            $appointment->booking_status = 'booking_paid_pin';
            $appointment->paid_waya = 'Mollie';
            
            $appointment->update();
            
            $Logdata = new Logdata;
            $Logdata->log_from="Appoinment";
            $Logdata->refrance_id = $ApmtId;
            $Logdata->log_datetime = date("Y-m-d h:i:s");
            $Logdata->tr_by = 1;
            $Logdata->message = $MsgStatus;
            $Logdata->save();

          //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
        }

      return true;
    }

    
}
