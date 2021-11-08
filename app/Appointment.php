<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

use Date;

/**
 * Class Appointment
 *
 * @package App
 * @property string $client
 * @property string $employee
 * @property string $start_time
 * @property string $finish_time
 * @property text $comments
 */
class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = ['start_time', 'finish_time', 'comments', 'client_id', 'employee_id','switched_off_reminder_email','switched_off_confirmed_email','price','extra_price_comment','add_by'];
    /**
     * Set to null if empty
     * @param $input
     */
    public function setClientIdAttribute($input)
    {
        $this->attributes['client_id'] = $input ? $input : null;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setEmployeeIdAttribute($input)
    {
        $this->attributes['employee_id'] = $input ? $input : null;
    }


    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id')->withTrashed();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withTrashed();
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id')->withTrashed();
    }

    public static function ApInvoiceDetail($ApmtId)
    {
        $InvoiceDetail =\DB::table('invoices')->where('appointment_id', $ApmtId)->first();
        if(!empty($InvoiceDetail->appointment_id))
        { return $InvoiceDetail; }
        else
        {
            return false;
        }
    }

    public static function ApInvoice($ApmtId,$updateInfoIfExist=false)
    {
        $InvoiceDetail =\DB::table('invoices')->where('appointment_id', $ApmtId)->first();

        if(!empty($InvoiceDetail->appointment_id)) { if(!$updateInfoIfExist){ return $InvoiceDetail;} }

        Date::setLocale('nl');
        $appointment =\DB::table('appointments')->where('id', $ApmtId)->first();


        $employee_service = \DB::table('employee_services')->where('service_id','=',$appointment->service_id)->where('employee_id','=',$appointment->employee_id)->whereNull('deleted_at')->first();

        $CmpDtl = \DB::table('tbl_companies')->where('id',$appointment->company_id)->first();

        $descriptadd = "";

        if(!empty($employee_service->moneybird_username))
        { $descriptadd = $employee_service->moneybird_username; }

        $dateM = $appointment->start_time;
        $linedescription = $descriptadd."<br/> Afspraakdatum: ".Date::parse($appointment->start_time)->format('d F Y');
        $ProductData = $TaxData = "";
        $service_tax_rate = \DB::table('services')->where('id','=',$appointment->service_id)->whereNotNull('tax_rate_id_moneybrid')->first();
        if(!empty($service_tax_rate->tax_rate_id_moneybrid))
        {
            $tax_rate_id = $service_tax_rate->tax_rate_id_moneybrid;

            $TaxData = \DB::table('tax_rates')->where('moneybird_tax_id',$tax_rate_id)->first();
        }
        $due_date = date("Y-m-d");
        if( $appointment->booking_status != "booking_paid_pin" )
        {
            $expired_days = $CmpDtl->expired_days;
            $due_date = date('Y-m-d', strtotime($due_date. ' + '.$expired_days.' days'));
        }

        $ArrinvData=[
            'inv_date' => date("Y-m-d"),
            'due_date' => $due_date,
            "prd_description"=>$linedescription,
            'price'=>$appointment->price,
            'baseamount'=>$appointment->price
        ];

        if(empty($InvoiceDetail))
        {
            $invNOArr=\DB::table('invoices')->where('inv_fyear','=',date("Y"))->where('inv_number','>',0)->orderBy('inv_number', 'desc')->first();

            if(!empty($invNOArr)) { $inv_number = $invNOArr->inv_number+1;} else { $inv_number = 200;}

            $ArrinvData['inv_fyear'] = date("Y");
            $ArrinvData['inv_number'] = $inv_number;
            $ArrinvData['appointment_id'] = $ApmtId;

            $inv_numberStr = $inv_number;
            if(strlen($inv_number)==1){$inv_numberStr = "000".$inv_number;}
            elseif(strlen($inv_number)==2){$inv_numberStr ="00".$inv_number;}
            elseif(strlen($inv_number)==3){$inv_numberStr ="0".$inv_number;}
            $inv_numberStr = date("Y").'-'.$inv_numberStr;
            $ArrinvData['display_inv_no'] = $inv_numberStr;
        }
        else
        {
            $inv_number = $InvoiceDetail->inv_number;
            $ArrinvData['inv_fyear'] = $InvoiceDetail->inv_fyear;
            $ArrinvData['inv_number'] = $inv_number;
            $ArrinvData['appointment_id'] = $ApmtId;
            $ArrinvData['display_inv_no'] = $InvoiceDetail->display_inv_no;
        }
        $taxamount = 0;
        $price = $appointment->price;
        if(!empty($TaxData))
        {
            if( $appointment->price > 0 && $TaxData->percentage > 0 )
            {
                $Rate = $TaxData->percentage;
                $taxamount = ($price*($Rate/100)) ;
            }
            $ArrinvData['taxid'] = $TaxData->id;
            if($TaxData->percentage!="") { $ArrinvData['taxrate'] = $TaxData->percentage; }
            $ArrinvData['taxrate_title'] = $TaxData->name;
            $ArrinvData['taxamount'] = $taxamount;
        }

        $ArrinvData['netamount'] = $price + $taxamount;

        if($appointment->booking_status == "booking_paid_pin")
        { $ArrinvData['footer_description'] = $CmpDtl->cmp_paid_inv_desc; }
        elseif($appointment->booking_status == "cash_paid")
        { $ArrinvData['footer_description'] = $CmpDtl->cmp_cashpaid_inv_desc; }
        elseif($appointment->booking_status == "booking_unpaid")
        { $ArrinvData['footer_description'] = $CmpDtl->cmp_unpaid_inv_desc; }
        /*elseif($appointment->booking_status == "partial_paid")
            { $ArrinvData['footer_description'] = $CmpDtl->cmp_partial_paid_inv_desc; } */

        if(empty($ArrinvData['footer_description'])){unset($ArrinvData['footer_description']);}

        $inv_numberStr = $inv_number;
        if(strlen($inv_number)==1){$inv_numberStr="000".$inv_number;}
        elseif(strlen($inv_number)==2){$inv_numberStr="00".$inv_number;}
        elseif(strlen($inv_number)==3){$inv_numberStr="0".$inv_number;}
        $InvNo = $ArrinvData['inv_fyear']." - ".$inv_numberStr;
        if(empty($InvoiceDetail))
        {
            $appointment->moneybird_invoice_id = $InvNo;

            \DB::table('appointments')->where('id',$ApmtId)->update(['moneybird_invoice_id' => $InvNo]);

            $ArrinvData['updated_at'] =date("Y-m-d h:i:s");
            $ArrinvData['created_at'] =date("Y-m-d h:i:s");

            \DB::table('invoices')->insert($ArrinvData);
        }
        else
        {   $ArrinvData['updated_at'] =date("Y-m-d h:i:s");
            \DB::table('invoices')->where('appointment_id',$ApmtId)->update($ArrinvData);
        }


        $InvoiceDetail =\DB::table('invoices')->where('appointment_id', $ApmtId)->first();
        return $InvoiceDetail;
    }

    public static function GetInvoiceEmailContent($ApmtId,$Tlttype="",$imgUrl=0)
    {

        Date::setLocale('nl');
        $InvoiceDetail =\DB::table('invoices')->where('appointment_id', $ApmtId)->first();
        $appointment =\DB::table('appointments')->where('id', $ApmtId)->first();
        $CmpDtl = \DB::table('tbl_companies')->where('id',$appointment->company_id)->first();
        $ThrpDtl= \DB::table('employees')->where("id",$appointment->employee_id)->first();
        $ClientDt = \DB::table('clients')->where("id",$appointment->client_id)->first();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
        if($Tlttype!="")
        {
            $EmailTemplate =\DB::table('email_templates')->where('email_type',$Tlttype)->first();
        }
        elseif($appointment->booking_status=="booking_paid_pin")
        {
            $EmailTemplate =\DB::table('email_templates')->where('email_type','paid_invoice')->first();
        }
        elseif($appointment->booking_status=="cash_paid")
        {
            $EmailTemplate = \DB::table('email_templates')->where('email_type','cash_paid_invoice')->first();
        }
        elseif($appointment->booking_status=="booking_unpaid")
        {
            $EmailTemplate = \DB::table('email_templates')->where('email_type','unpaid_invoice')->first();
        }
        /*elseif($appointment->booking_status=="partial_paid")
          {
            $EmailTemplate = \DB::table('email_templates')->where('email_type','partial_payment_invoice')->first();
          }*/
        $strInvTlt = $EmailTemplate->email_content;
        $Subject = $EmailTemplate->email_subject;
        if(!empty($EmailTemplate->email_id))
        { $ExtraEmailAddress = $EmailTemplate->email_id;  }

        $locationdesc = $location_address = $location_name = "";
        if(!empty($appointment->location_id))
        {
            $location = \DB::table('locations')->where("id",$appointment->location_id)->first();
            if(!empty($location->location_name))
            { $location_name = $location->location_name;}
            $location_address = $location->location_address;
            $locationdesc = $location->location_description;
        }
        $location_name = $location_name." ".$ClientDt->city_name;

        $strInvTlt = str_replace('[TODAY DATE]',  date("d-M-Y"), $strInvTlt);

        if(!empty($CmpDtl))
        {
            if($imgUrl==0)
            {
                $strInvTlt = str_replace('[COMPANY LOGO IMAGE]',  public_path('/upload/'.$CmpDtl->cmp_logo1)   , $strInvTlt);
            }
            else
            {
                $strInvTlt = str_replace('[COMPANY LOGO IMAGE]',  url('/public/upload/'.$CmpDtl->cmp_logo1)   , $strInvTlt);
            }

            $strInvTlt = str_replace('[COMPANY NAME]', $CmpDtl->cmp_name, $strInvTlt);
            $Subject = str_replace('[COMPANY NAME]', $CmpDtl->cmp_name, $Subject);

            $strInvTlt = str_replace('[COMPANY WEB]',$CmpDtl->cmp_web, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY PHONE]',$CmpDtl->cmp_contact_no,$strInvTlt);

            $strInvTlt = str_replace('[COMPANY STREET]', $CmpDtl->cmp_streetname, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY POSTCODE]', $CmpDtl->cmp_postalcode, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY PLACE]', $CmpDtl->cmp_place, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY EMAIL]', $CmpDtl->cmp_email, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY VAT NUMBER]', $CmpDtl->cmp_vat_no, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY BANK AC NUMBER]',$CmpDtl->cmp_bank_ac_no,$strInvTlt);
            $strInvTlt = str_replace('[COMPANY BANK NAME]', $CmpDtl->cmp_bank_ac_name, $strInvTlt);
        }

        if(!empty($ThrpDtl))
        {
            if(!empty($ThrpDtl->logo1))
            {
                if($imgUrl==0)
                {
                    $Strimg='<img src="'.public_path('/upload/'.$ThrpDtl->logo1).'" style="height: 100%;width: 100%">';
                }
                else
                {
                    $Strimg='<img src="'.url('/public/upload/'.$ThrpDtl->logo1).'" style="height: 100%;width: 100%">';
                }
                $strInvTlt = str_replace('[THARAPIST IMAGE1]', $Strimg, $strInvTlt);
            }
            else
            { $strInvTlt = str_replace('[THARAPIST IMAGE1]', "", $strInvTlt); }
            if(!empty($ThrpDtl->logo2))
            {
                if($imgUrl==0)
                {
                    $Strimg='<img src="'.public_path('/upload/'.$ThrpDtl->logo2).'" style="height: 100%;width: 100%">';
                }
                else
                {
                    $Strimg='<img src="'.url('/public/upload/'.$ThrpDtl->logo2).'" style="height: 100%;width: 100%">';
                }

                $strInvTlt = str_replace('[THARAPIST IMAGE2]', $Strimg, $strInvTlt);
            }
            else
            { $strInvTlt = str_replace('[THARAPIST IMAGE2]','', $strInvTlt); }

            $strInvTlt = str_replace('[THARAPIST AGB_CODE]', $ThrpDtl->agb_code, $strInvTlt);
            $strInvTlt = str_replace('[THARAPIST BEHANDELAAR]', $ThrpDtl->small_info, $strInvTlt);
            $strInvTlt = str_replace('[THARAPIST RBCZ_NUMBER]', $ThrpDtl->rcbz_no, $strInvTlt);

            $strInvTlt = str_replace('[THARAPIST NAME]', $ThrpDtl->first_name." ".$ThrpDtl->last_name, $strInvTlt);
            $Subject = str_replace('[THARAPIST NAME]', $ThrpDtl->first_name." ".$ThrpDtl->last_name, $Subject);

            $strInvTlt = str_replace('[THARAPIST ADDRESS]', $ThrpDtl->address, $strInvTlt);
            $thlocation_name="";
            if(!empty($ThrpDtl->location_id))
            {
                $location = \DB::table('locations')->where("id",$ThrpDtl->location_id)->first();
                if(!empty($location->location_name))
                { $thlocation_name = $location->location_name; }
            }
            $strInvTlt = str_replace('[THARAPIST CITY]', $thlocation_name, $strInvTlt);

            $strInvTlt = str_replace("{therapistitle}",$ThrpDtl->moneybird_username,$strInvTlt);

            $strInvTlt = str_replace("{therapistname}",$ThrpDtl->first_name." ".$ThrpDtl->last_name,$strInvTlt);
            $Subject = str_replace("{therapistname}",$ThrpDtl->first_name." ".$ThrpDtl->last_name,$Subject);
            $strInvTlt = str_replace("{therapisttelephone}",$ThrpDtl->phone,$strInvTlt);
            $strInvTlt = str_replace("{therapistregistrations}",$ThrpDtl->registration_no,$strInvTlt);
            $strInvTlt = str_replace("{therapistemail}",$ThrpDtl->email,$strInvTlt);
        }
        $PaymentUrl = url('/mollie-cpayment/'.$ApmtId);
        $strInvTlt = str_replace('[PAYMENT_URL]', $PaymentUrl, $strInvTlt);

        if(!empty($ClientDt))
        {
            $doctorName = $doctorEmail = $doctorCity = $doctorPascode = $doctorPhone = $doctorNumber = $doctorAddress = "";
            if(!empty($ClientDt->doctor_id))
            {
                $DoctorData = \App\Doctor::findOrFail($ClientDt->doctor_id);
                if(!empty($DoctorData->first_name))
                {
                    $doctorName = $DoctorData->first_name." ".$DoctorData->last_name;
                    $doctorEmail = $DoctorData->email;
                    $doctorPhone = $DoctorData->phone;
                    $doctorAddress = $DoctorData->address;
                    $doctorNumber = $DoctorData->house_number;
                    $doctorPascode = $DoctorData->postcode;
                    $doctorCity = $DoctorData->city_name;
                }
            }
            $strInvTlt = str_replace('[CLIENT REPORT PROBLEEM]', $ClientDt->probleem, $strInvTlt);
            $strInvTlt = str_replace('[CLIENT REPORT BEREIKEN]', $ClientDt->bereiken, $strInvTlt);
            $strInvTlt = str_replace('[CLIENT REPORT WAARNEMINGEN]', $ClientDt->waarnemingen, $strInvTlt);
            $strInvTlt = str_replace('[CLIENT REPORT PROCEDIAGNOSE]', $ClientDt->procediagnose, $strInvTlt);
            $strInvTlt = str_replace('[CLIENT REPORT FYSIEKE]', $ClientDt->fysieke, $strInvTlt);
            $strInvTlt = str_replace('[CLIENT REPORT SAMENVATTING]', $ClientDt->samenvatting, $strInvTlt);





            $strInvTlt = str_replace('[refferer]', $doctorName, $strInvTlt);
            $strInvTlt = str_replace('[DOCTOR EMAIL ADDRESS]', $doctorEmail, $strInvTlt);
            $strInvTlt = str_replace('[DOCTOR PHONE]', $doctorPhone, $strInvTlt);
            $strInvTlt = str_replace('[DOCTOR Address]', $doctorAddress, $strInvTlt);
            $strInvTlt = str_replace('[DOCTOR NUMBER]', $doctorNumber, $strInvTlt);

            $strInvTlt = str_replace('[DOCTOR POSTCODE]', $doctorPascode, $strInvTlt);
            $strInvTlt = str_replace('[DOCTOR CITY]', $doctorCity, $strInvTlt);




            $strInvTlt = str_replace('[CUSTOMER NAME]', $ClientDt->first_name." ".$ClientDt->last_name, $strInvTlt);
            $Subject = str_replace('[CUSTOMER NAME]', $ClientDt->first_name." ".$ClientDt->last_name, $Subject);
            $strInvTlt = str_replace('{clientname}', $ClientDt->first_name." ".$ClientDt->last_name, $strInvTlt);
            $Subject = str_replace('{clientname}', $ClientDt->first_name." ".$ClientDt->last_name, $Subject);


            $strInvTlt = str_replace("{customeremail}",$ClientDt->email,$strInvTlt);
            $strInvTlt = str_replace("{customertelephonenumber}",$ClientDt->phone,$strInvTlt);
            $strInvTlt = str_replace("{location_address}",$location_address,$strInvTlt);
            $strInvTlt = str_replace("{location}",$thlocation_name,$strInvTlt);
            $strInvTlt = str_replace("{route_directions}",$locationdesc,$strInvTlt);
            if(!empty($InvoiceDetail->prd_description))
            { $strInvTlt = str_replace("{thrapyname}",$InvoiceDetail->prd_description,$strInvTlt); }
            else
            { $strInvTlt = str_replace("{thrapyname}",'',$strInvTlt); }

            $strInvTlt = str_replace('[CUSTOMER ADDRESS]', $ClientDt->address.' '.$ClientDt->house_number, $strInvTlt);
            $strInvTlt = str_replace('[CUSTOMER POSTCODE]', $ClientDt->postcode, $strInvTlt);
            $strInvTlt = str_replace('[CUSTOMER CITY]', $ClientDt->city_name, $strInvTlt);
            if(!empty($ClientDt->dob))
            {
                $strInvTlt = str_replace('[CUSTOMER DATEOFBIRTH]', date("d-m-Y",strtotime($ClientDt->dob)), $strInvTlt);
            }
            else
            { $strInvTlt = str_replace('[CUSTOMER DATEOFBIRTH]',"", $strInvTlt);  }
        }

        if($EmailTemplate->id==29)
        {
            $FirsBookingtDate = "";
            $AllBookingtDate = "";
            $appointmentBook =\DB::table('appointments')
                ->select('start_time')
                ->where('employee_id', $appointment->employee_id)
                ->where('client_id', $appointment->client_id)
                ->orderBy('start_time','ASC')
                ->get();
            if(!empty($appointmentBook))
            {
                foreach ($appointmentBook as $keyTh => $valueBk) {
                    if($FirsBookingtDate=="")
                    {
                        $FirsBookingtDate = date("d-m-Y",strtotime($valueBk->start_time));}
                    if($AllBookingtDate!=""){ $AllBookingtDate = $AllBookingtDate.",";}
                    $AllBookingtDate = $AllBookingtDate.date("d-m-Y",strtotime($valueBk->start_time));
                }
            }
            $strInvTlt = str_replace('{first therapy session client}',$FirsBookingtDate, $strInvTlt);
            $strInvTlt = str_replace('{all booking dates}',$AllBookingtDate, $strInvTlt);
        }


        $strInvTlt = str_replace('[EXTRA ADDED]',$appointment->extra_price_comment, $strInvTlt);
        if(!empty($InvoiceDetail->inv_number))
        {
            $inv_number = $InvoiceDetail->inv_number;
            $inv_numberStr = (string)$inv_number;
        }
        else
        { $inv_numberStr ="";}
        $DueAmount = 0;$PaidAmt=0;
        if(!empty($InvoiceDetail))
        {
            if(($appointment->booking_status == "partial_paid")  || ($appointment->booking_status == "booking_unpaid") )
            {
                $StrDesc = $CmpDtl->cmp_unpaid_inv_desc;

                if(!empty($InvoiceDetail->payment_received))
                {
                    $Arrdata = json_decode($InvoiceDetail->payment_received);
                    if(!empty($Arrdata->I->amount))
                    { $PaidAmt = $PaidAmt + $Arrdata->I->amount; }
                    if(!empty($Arrdata->II->amount))
                    { $PaidAmt = $PaidAmt + $Arrdata->II->amount; }
                }
                $DueAmount = $InvoiceDetail->netamount - $PaidAmt;
            }
            $InvNo = $InvoiceDetail->display_inv_no;
            $strInvTlt = str_replace('[INVOICE NUMBER]',$InvNo, $strInvTlt);
            $Subject = str_replace('[INVOICE NUMBER]',$InvNo, $Subject);

            $strInvTlt = str_replace('[DATE OF START]',date('d-m-Y',strtotime($InvoiceDetail->inv_date)), $strInvTlt);
            $Subject = str_replace('[DATE OF START]',date('d-m-Y',strtotime($InvoiceDetail->inv_date)), $Subject);

            $strInvTlt = str_replace('[DATE OF BOOKING]',date('d-m-Y',strtotime($InvoiceDetail->due_date)), $strInvTlt);
            $Subject = str_replace('[DATE OF BOOKING]',date('d-m-Y',strtotime($InvoiceDetail->due_date)), $Subject);

            $strInvTlt = str_replace('[PRODUCT_NAME]',$InvoiceDetail->prd_description, $strInvTlt);
            $strInvTlt = str_replace('[PRICE]',"&euro;".number_format($InvoiceDetail->price,2,",","."), $strInvTlt);
            $strInvTlt = str_replace('[AMOUNT]',"&euro;".number_format($InvoiceDetail->price,2,",","."), $strInvTlt);
            $strInvTlt = str_replace('[TOTALAMOUNT]',"&euro;".number_format($InvoiceDetail->netamount,2,",","."), $strInvTlt);
        }
        else
        {
            $DueAmount = $appointment->price;

            $strInvTlt = str_replace('[DATE OF BOOKING]',date('d-m-Y',strtotime($appointment->start_time)), $strInvTlt);
            $Subject = str_replace('[DATE OF BOOKING]',date('d-m-Y',strtotime($appointment->start_time)), $Subject);

            /*$strInvTlt = str_replace('[PRODUCT_NAME]',$appointment->prd_description, $strInvTlt);*/
            $strInvTlt = str_replace('[PRICE]',"&euro;".number_format($appointment->price,2,",","."), $strInvTlt);
            $strInvTlt = str_replace('[AMOUNT]',"&euro;".number_format($appointment->price,2,",","."), $strInvTlt);
            $strInvTlt = str_replace('[TOTALAMOUNT]',"&euro;".number_format($appointment->price,2,",","."), $strInvTlt);
        }





        if(!empty($InvoiceDetail->taxrate))
        {
            $strInvTlt = str_replace('[Tax]',$InvoiceDetail->taxrate."%", $strInvTlt);
            if($InvoiceDetail->tax_inc_exc==1)
            {$stitle="Subtotaal excl. btw";}
            else
            {$stitle="Subtotaal incl. btw";}
            $SubTotalStr='<tr>
                                <th colspan="2" style="text-align: right;">
                                '.$stitle.'</th><td style="text-align: right;">&euro;'.number_format($InvoiceDetail->baseamount,2,",",".").'</td>
                                <th>&nbsp;</th>
                              </tr>
                              <tr>
                                <th colspan="2" style="text-align: right;">'.$InvoiceDetail->taxrate_title.'</th><td style="text-align: right;">&euro;'.number_format($InvoiceDetail->taxamount,2,",",".").'</td>
                                <th>&nbsp;</th>
                              </tr>';
            $strInvTlt = str_replace('[SUB_TOTAL]', $SubTotalStr, $strInvTlt);
        }
        else
        {
            $strInvTlt = str_replace('[Tax]',"", $strInvTlt);
            $strInvTlt = str_replace('[SUB_TOTAL]','', $strInvTlt);
        }
        $DueAmtStr="";
        if($DueAmount>0)
        {
            $DueAmtStr = '<tr>
                        <th colspan="2" style="text-align: right;">
                        &nbsp;</th><td style="text-align: right;">&euro;'.number_format($DueAmount,2,",",".").'</td>
                        <th>&nbsp;</th>
                      </tr>
                      <tr>';
        }
        $strInvTlt = str_replace('[DUE AMOUNT]',$DueAmtStr, $strInvTlt);
        $strInvTlt = str_replace('[DUE AMOUNT VALUE]','&euro;'.number_format($DueAmount,2,",","."), $strInvTlt);


        $StrDesc = $CmpDtl->cmp_unpaid_inv_desc;
        if($appointment->booking_status == "booking_paid_pin")
        { $StrDesc = $CmpDtl->cmp_paid_inv_desc; }
        elseif($appointment->booking_status == "cash_paid")
        { $StrDesc = $CmpDtl->cmp_cashpaid_inv_desc; }
        elseif($appointment->booking_status == "booking_unpaid")
        {
            $StrDesc = $CmpDtl->cmp_unpaid_inv_desc;
            $StrDesc = str_replace('{document.total_price}',"&euro;".number_format($DueAmount,2,",","."), $StrDesc);
            if(!empty($InvoiceDetail))
            { $StrDesc = str_replace('{document.due_date}',date('d-m-Y',strtotime($InvoiceDetail->inv_date)), $StrDesc); }
            else
            {
                $StrDesc = str_replace('{document.due_date}',date('d-m-Y',strtotime($appointment->start_time)), $StrDesc);
            }

            $StrDesc = str_replace('{document.invoice_id}',$InvNo, $StrDesc);
        }
        /*elseif($appointment->booking_status == "partial_paid")
            { 
                $StrDesc = $CmpDtl->cmp_unpaid_inv_desc;
                $StrDesc = str_replace('{document.total_price}',"&euro;".number_format($DueAmount,2,",","."), $StrDesc); 
                $StrDesc = str_replace('{document.due_date}',date('d-m-Y',strtotime($InvoiceDetail->inv_date)), $StrDesc);
                 
                $StrDesc = str_replace('{document.invoice_id}',$InvNo, $StrDesc);
            }*/

        $strInvTlt = str_replace('[FOOTER COMPANY NOTE]',$StrDesc, $strInvTlt);
        if(!empty($InvoiceDetail))
        {
            $strInvTlt = str_replace('{session_costs_for_an_hour}',$InvoiceDetail->price, $strInvTlt);
            $strInvTlt = str_replace('{tharpycost}',$InvoiceDetail->price, $strInvTlt);
        }
        else
        {
            $strInvTlt = str_replace('{session_costs_for_an_hour}',$appointment->price, $strInvTlt);
            $strInvTlt = str_replace('{tharpycost}',$appointment->price, $strInvTlt);
        }
        $strInvTlt = str_replace('{booking_date}',date('d-m-Y',strtotime($appointment->start_time)), $strInvTlt);
        $strInvTlt = str_replace('{booking_time}',date('H:i',strtotime($appointment->start_time)), $strInvTlt);

        $service_id = $appointment->service_id;
        $servicedetail =\DB::table('services')->where('id', $service_id)->first();
        $strInvTlt = str_replace('{therapistdes}',$servicedetail->description, $strInvTlt);
        $strInvTlt = str_replace('{therapistdes2}',$servicedetail->description_second, $strInvTlt);


        $showView =  "<a href=".url('admin/appointments/'.$appointment->id).">Show View</a>";
        $strInvTlt = str_replace("{go_booking_view}",$showView,$strInvTlt);
        $strInvTlt = str_replace("{recipient.show_open_invoices_online_url}",$showView,$strInvTlt);


        $RetArr['email_subject'] = $Subject;
        $RetArr['email_mtr'] = $strInvTlt;
        $RetArr['client_email'] = $ClientDt->email;
        if(!empty($ExtraEmailAddress))
        { $RetArr['ExtraEmail'] = $ExtraEmailAddress; }
        return $RetArr;
    }

    public static function ApInvoicePrintView($ApmtId,$imgUrl=0)
    {

       // echo $client_last_name;


     //   die;
        Date::setLocale('nl');
        $InvoiceDetail =\DB::table('invoices')->where('appointment_id', $ApmtId)->first();
        $appointment =\DB::table('appointments')->where('id', $ApmtId)->first();
        $CmpDtl = \DB::table('tbl_companies')->where('id',$appointment->company_id)->first();
        $ThrpDtl= \DB::table('employees')->where("id",$appointment->employee_id)->first();
        $ClientDt = \DB::table('clients')->where("id",$appointment->client_id)->first();
        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

        if($appointment->booking_status=="booking_paid_pin")
        {
            $EmailTemplate =\DB::table('email_templates')->where('email_type','paid_invoice')->first();
        }
        elseif($appointment->booking_status=="cash_paid")
        {
            $EmailTemplate = \DB::table('email_templates')->where('email_type','cash_paid_invoice')->first();
        }
        else//if($appointment->booking_status=="booking_unpaid")
        {
            $EmailTemplate = \DB::table('email_templates')->where('email_type','unpaid_invoice')->first();
        }
        /*elseif($appointment->booking_status=="partial_paid")
            {
              $EmailTemplate = \DB::table('email_templates')->where('email_type','partial_payment_invoice')->first();
            }*/
        $strInvTlt = $EmailTemplate->email_content_h;

        $location_name = "";
        if(!empty($appointment->location_id))
        {
            $location = \DB::table('locations')->where("id",$appointment->location_id)->first();
            if(!empty($location->location_name))
            { $location_name = $location->location_name;}
        }
        $location_name = $location_name." ".$ClientDt->city_name;

        $doctorName = $doctorEmail = $doctorCity = $doctorPascode = $doctorPhone = $doctorNumber = $doctorAddress = "";
        if(!empty($ClientDt->doctor_id))
        {
            $DoctorData = \App\Doctor::findOrFail($ClientDt->doctor_id);
            if(!empty($DoctorData->first_name))
            {
                $doctorName = $DoctorData->first_name." ".$DoctorData->last_name;
                $doctorEmail = $DoctorData->email;
                $doctorPhone = $DoctorData->phone;
                $doctorAddress = $DoctorData->address;
                $doctorNumber = $DoctorData->house_number;
                $doctorPascode = $DoctorData->postcode;
                $doctorCity = $DoctorData->city_name;
            }
        }

        $strInvTlt = str_replace('[refferer]', $doctorName, $strInvTlt);
        $strInvTlt = str_replace('[DOCTOR EMAIL ADDRESS]', $doctorEmail, $strInvTlt);
        $strInvTlt = str_replace('[DOCTOR PHONE]', $doctorPhone, $strInvTlt);
        $strInvTlt = str_replace('[DOCTOR Address]', $doctorAddress, $strInvTlt);
        $strInvTlt = str_replace('[DOCTOR NUMBER]', $doctorNumber, $strInvTlt);

        $strInvTlt = str_replace('[DOCTOR POSTCODE]', $doctorPascode, $strInvTlt);
        $strInvTlt = str_replace('[DOCTOR CITY]', $doctorCity, $strInvTlt);


        $strInvTlt = str_replace('[CLIENT REPORT PROBLEEM]', $ClientDt->probleem, $strInvTlt);
        $strInvTlt = str_replace('[CLIENT REPORT BEREIKEN]', $ClientDt->bereiken, $strInvTlt);
        $strInvTlt = str_replace('[CLIENT REPORT WAARNEMINGEN]', $ClientDt->waarnemingen, $strInvTlt);
        $strInvTlt = str_replace('[CLIENT REPORT PROCEDIAGNOSE]', $ClientDt->procediagnose, $strInvTlt);
        $strInvTlt = str_replace('[CLIENT REPORT FYSIEKE]', $ClientDt->fysieke, $strInvTlt);
        $strInvTlt = str_replace('[CLIENT REPORT SAMENVATTING]', $ClientDt->samenvatting, $strInvTlt);


        if(!empty($CmpDtl))
        {
            if($imgUrl==1)
            {

                $strInvTlt = str_replace('[COMPANY LOGO IMAGE]',  url('/public/upload/'.$CmpDtl->cmp_logo1)   , $strInvTlt);
            }
            else
            {

                $strInvTlt = str_replace('[COMPANY LOGO IMAGE]',  URL::to('/').'/public/upload/'.$CmpDtl->cmp_logo1  , $strInvTlt);
            }
            
//            echo "<pre>"; print_r($strInvTlt);
//              print_r($CmpDtl->cmp_name); die;
            $strInvTlt = str_replace('[COMPANY NAME]', $CmpDtl->cmp_name, $strInvTlt);

            $strInvTlt = str_replace('[COMPANY WEB]', $CmpDtl->cmp_web, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY PHONE]', $CmpDtl->cmp_contact_no, $strInvTlt);

            $strInvTlt = str_replace('[COMPANY STREET]', $CmpDtl->cmp_streetname, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY POSTCODE]', $CmpDtl->cmp_postalcode, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY PLACE]', $CmpDtl->cmp_place, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY EMAIL]', $CmpDtl->cmp_email, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY VAT NUMBER]', $CmpDtl->cmp_vat_no, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY BANK AC NUMBER]',$CmpDtl->cmp_bank_ac_no,$strInvTlt);
            $strInvTlt = str_replace('[COMPANY BANK NAME]', $CmpDtl->cmp_bank_ac_name, $strInvTlt);
        }

        if(!empty($ThrpDtl))
        {
            if(!empty($ThrpDtl->logo1))
            {
                if($imgUrl==1)
                {
                    $Strimg='<img src="'.url('/public/upload/'.$ThrpDtl->logo1).'" style="height: 100%;width: 100%">';
                }
                else
                {
                    $Strimg='<img src="'.URL::to('/').'/public/upload/'.$ThrpDtl->logo1.'" style="height: 100%;width: 100%">';
                }
                $strInvTlt = str_replace('[THARAPIST IMAGE1]', $Strimg, $strInvTlt);
            }
            else
            { $strInvTlt = str_replace('[THARAPIST IMAGE1]', "", $strInvTlt); }
            if(!empty($ThrpDtl->logo2))
            {
                if($imgUrl==1)
                {
                    $Strimg='<img src="'.url('/public/upload/'.$ThrpDtl->logo2).'" style="height: 100%;width: 100%">';
                }
                else
                {
                    $Strimg='<img src="'.URL::to('/').'/public/upload/'.$ThrpDtl->logo2.'" style="height: 100%;width: 100%">';
                }


                $strInvTlt = str_replace('[THARAPIST IMAGE2]', $Strimg, $strInvTlt);
            }
            else
            { $strInvTlt = str_replace('[THARAPIST IMAGE2]','', $strInvTlt); }

            $strInvTlt = str_replace('[THARAPIST AGB_CODE]', $ThrpDtl->agb_code, $strInvTlt);
            $strInvTlt = str_replace('[THARAPIST BEHANDELAAR]', $ThrpDtl->small_info, $strInvTlt);
            $strInvTlt = str_replace('[THARAPIST RBCZ_NUMBER]', $ThrpDtl->rcbz_no, $strInvTlt);
        }
        $strInvTlt = str_replace('[THARAPIST NAME]', $ThrpDtl->first_name." ".$ThrpDtl->last_name, $strInvTlt);
      //  $strInvTlt = str_replace('[THARAPIST NAME]', $emp_first_name." ".$emp_last_name, $strInvTlt);
        $strInvTlt = str_replace("{therapistitle}",$ThrpDtl->moneybird_username,$strInvTlt);

        $strInvTlt = str_replace('[THARAPIST ADDRESS]', $ThrpDtl->address, $strInvTlt);
        $thlocation_name="";
        if(!empty($ThrpDtl->location_id))
        {
            $location = \DB::table('locations')->where("id",$ThrpDtl->location_id)->first();
            if(!empty($location->location_name))
            { $thlocation_name = $location->location_name; }
        }
        $strInvTlt = str_replace('[THARAPIST CITY]', $thlocation_name, $strInvTlt);
        $PaymentUrl = url('/mollie-cpayment/'.$ApmtId);
        $strInvTlt = str_replace('[PAYMENT_URL]', $PaymentUrl, $strInvTlt);
      //  echo "<pre>"; print_r($ClientDt); die;
        if(!empty($ClientDt))
        {
            $strInvTlt = str_replace('[CUSTOMER NAME]', $ClientDt->first_name." ".$ClientDt->last_name, $strInvTlt);
         //   $strInvTlt = str_replace('[CUSTOMER NAME]', $client_first_name." ".$client_last_name, $strInvTlt);
            $strInvTlt = str_replace('[CUSTOMER COMPANY NAME]', $ClientDt->company_name, $strInvTlt);

            $strInvTlt = str_replace('[CUSTOMER ADDRESS]', $ClientDt->address.' '.$ClientDt->house_number, $strInvTlt);
            $strInvTlt = str_replace('[CUSTOMER POSTCODE]', $ClientDt->postcode, $strInvTlt);
            $strInvTlt = str_replace('[CUSTOMER CITY]', $ClientDt->city_name, $strInvTlt);
            if(!empty($ClientDt->dob))
            {
                $strInvTlt = str_replace('[CUSTOMER DATEOFBIRTH]', date("d-m-Y",strtotime($ClientDt->dob)), $strInvTlt);
            }
            else
            { $strInvTlt = str_replace('[CUSTOMER DATEOFBIRTH]',"", $strInvTlt);  }
        }

        $inv_number = $InvoiceDetail->inv_number;
        $inv_numberStr = (string)$inv_number;
        $DueAmount = 0;
        if(($appointment->booking_status == "partial_paid") || ($appointment->booking_status == "booking_unpaid") )
        {
            $StrDesc = $CmpDtl->cmp_partial_paid_inv_desc;
            $PaidAmt=0;
            if(!empty($InvoiceDetail->payment_received))
            {
                $Arrdata = json_decode($InvoiceDetail->payment_received);
                if(!empty($Arrdata->I->amount))
                { $PaidAmt = $PaidAmt + $Arrdata->I->amount; }
                if(!empty($Arrdata->II->amount))
                { $PaidAmt = $PaidAmt + $Arrdata->II->amount; }
            }
            $DueAmount = $InvoiceDetail->netamount - $PaidAmt;
        }
        $InvNo = $InvoiceDetail->display_inv_no;
        $strInvTlt = str_replace('[INVOICE NUMBER]',$InvNo, $strInvTlt);
        $strInvTlt = str_replace('[DATE OF START]',date('d-m-Y',strtotime($InvoiceDetail->due_date)), $strInvTlt);

        $strInvTlt = str_replace('[EXTRA ADDED]',$appointment->extra_price_comment, $strInvTlt);
        $strInvTlt = str_replace('[DATE OF BOOKING]',date('d-m-Y',strtotime($InvoiceDetail->inv_date)), $strInvTlt);

        $strInvTlt = str_replace('[PRODUCT_NAME]',$InvoiceDetail->prd_description, $strInvTlt);
        $strInvTlt = str_replace('[PRICE]',"&euro;".number_format($InvoiceDetail->price,2,",","."), $strInvTlt);
        $strInvTlt = str_replace('[AMOUNT]',"&euro;".number_format($InvoiceDetail->price,2,",","."), $strInvTlt);
        if(!empty($InvoiceDetail->taxrate))
        {
            $strInvTlt = str_replace('[Tax]',$InvoiceDetail->taxrate."%", $strInvTlt);
            if($InvoiceDetail->tax_inc_exc==1)
            {$stitle="Subtotaal excl. btw";}
            else
            {$stitle="Subtotaal incl. btw";}
            $SubTotalStr='<tr>
                                <th colspan="2" style="text-align: right;">
                                '.$stitle.'</th><td style="text-align: right;">&euro;'.number_format($InvoiceDetail->baseamount,2,",",".").'</td>
                                <th>&nbsp;</th>
                              </tr>
                              <tr>
                                <th colspan="2" style="text-align: right;">'.$InvoiceDetail->taxrate_title.'</th><td style="text-align: right;">&euro;'.number_format($InvoiceDetail->taxamount,2,",",".").'</td>
                                <th>&nbsp;</th>
                              </tr>';
            $strInvTlt = str_replace('[SUB_TOTAL]', $SubTotalStr, $strInvTlt);
        }
        else
        {
            $strInvTlt = str_replace('[Tax]',"", $strInvTlt);
            $strInvTlt = str_replace('[SUB_TOTAL]','', $strInvTlt);
        }
        $DueAmtStr="";
        if($DueAmount>0)
        {
            $DueAmtStr = '<tr>
                        <th colspan="2" style="text-align: right;">
                        Openstaand bedrag:</th><td style="text-align: right;">&euro;'.number_format($DueAmount,2,",",".").'</td>
                        <th>&nbsp;</th>
                      </tr>
                      <tr>';
        }
        $strInvTlt = str_replace('[DUE AMOUNT]',$DueAmtStr, $strInvTlt);
        $strInvTlt = str_replace('[DUE AMOUNT VALUE]','&euro;'.number_format($DueAmount,2,",","."), $strInvTlt);
        $strInvTlt = str_replace('[TOTALAMOUNT]',"&euro;".number_format($InvoiceDetail->netamount,2,",","."), $strInvTlt);
        $StrDesc = $CmpDtl->cmp_unpaid_inv_desc;
        if($appointment->booking_status == "booking_paid_pin")
        { $StrDesc = $CmpDtl->cmp_paid_inv_desc; }
        elseif($appointment->booking_status == "cash_paid")
        { $StrDesc = $CmpDtl->cmp_cashpaid_inv_desc; }
        elseif($appointment->booking_status == "booking_unpaid")
        {
            $StrDesc = $CmpDtl->cmp_unpaid_inv_desc;
            $StrDesc = str_replace('{document.total_price}',"&euro;".number_format($DueAmount,2,",","."), $StrDesc);
            $StrDesc = str_replace('{document.due_date}',date('d-m-Y',strtotime($InvoiceDetail->due_date)), $StrDesc);
            // echo "<pre>"; print_r($InvoiceDetail); die;
            $StrDesc = str_replace('{document.invoice_id}',$InvNo, $StrDesc);
        }
        /*elseif($appointment->booking_status == "partial_paid")
            {
                $StrDesc = $CmpDtl->cmp_partial_paid_inv_desc;
                $StrDesc = str_replace('{document.total_price}',"&euro;".number_format($DueAmount,2,",","."), $StrDesc);
                $StrDesc = str_replace('{document.due_date}',date('d-m-Y',strtotime($InvoiceDetail->inv_date)), $StrDesc);
                $StrDesc = str_replace('{document.invoice_id}',$InvNo, $StrDesc);
            }*/

        $strInvTlt = str_replace('[FOOTER COMPANY NOTE]',$StrDesc, $strInvTlt);

      // echo "<pre>"; print_r($strInvTlt); die;
        return $strInvTlt;
    }

    public static function ApSampleInvoiceView($ApmtId)
    {
        Date::setLocale('nl');
        $ThrpDtl = \DB::table('employees')->where("id",$ApmtId)->first();

        $employee_service = \DB::table('employee_services')->where('employee_id','=',$ApmtId)->whereNull('deleted_at')->first();

        $CmpDtl = \DB::table('tbl_companies')->where('id',$ThrpDtl->company_id)->first();

        $descriptadd = "Consult integratieve psychotherapie. Behandelcode 24511";

        if(!empty($employee_service->moneybird_username))
        { $descriptadd = $employee_service->moneybird_username; }

        $dateM = date("Y-m-d h:i:s");
        $linedescription = $descriptadd."<br/> Afspraakdatum: ".Date::parse($dateM)->format('d F Y');
        $ProductData = $TaxData = "";




        //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
        $strInvTlt =""  ;
        $location_name = "";

        $ClientDt = \DB::table('clients')->first();
        if(!empty($ClientDt->location_id))
        {
            $location = \DB::table('locations')->where("id",$ClientDt->location_id)->first();
            $location_name = $location->location_name;
        }


        $EmailTemplate = \DB::table('email_templates')->where('id',23)->first();
        $strInvTlt = $EmailTemplate->email_content_h;
        if(!empty($CmpDtl))
        {
            $strInvTlt = str_replace('[COMPANY LOGO IMAGE]',  url('/public/upload/'.$CmpDtl->cmp_logo1)   , $strInvTlt);
            $strInvTlt = str_replace('[COMPANY NAME]', $CmpDtl->cmp_name, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY STREET]', $CmpDtl->cmp_streetname, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY POSTCODE]', $CmpDtl->cmp_postalcode, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY PLACE]', $CmpDtl->cmp_place, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY EMAIL]', $CmpDtl->cmp_email, $strInvTlt);

            $strInvTlt = str_replace('[COMPANY VAT NUMBER]', $CmpDtl->cmp_vat_no, $strInvTlt);
            $strInvTlt = str_replace('[COMPANY BANK AC NUMBER]',$CmpDtl->cmp_bank_ac_no,$strInvTlt);
            $strInvTlt = str_replace('[COMPANY BANK NAME]', $CmpDtl->cmp_bank_ac_name, $strInvTlt);
        }

        if(!empty($ThrpDtl))
        {

            if(!empty($ThrpDtl->logo1))
            {
                $Strimg='<img src="'.url('/public/upload/'.$ThrpDtl->logo1).'" style="height: 100%;width: 100%">';
                $strInvTlt = str_replace('[THARAPIST IMAGE1]', $Strimg, $strInvTlt);
            }
            else
            { $strInvTlt = str_replace('[THARAPIST IMAGE1]', "", $strInvTlt); }
            if(!empty($ThrpDtl->logo2))
            {
                $Strimg='<img src="'.url('/public/upload/'.$ThrpDtl->logo2).'" style="height: 100%;width: 100%">';
                $strInvTlt = str_replace('[THARAPIST IMAGE2]', $Strimg, $strInvTlt);
            }
            else
            { $strInvTlt = str_replace('[THARAPIST IMAGE2]','', $strInvTlt); }

            $strInvTlt = str_replace('[THARAPIST AGB_CODE]', $ThrpDtl->agb_code, $strInvTlt);
            $strInvTlt = str_replace('[THARAPIST BEHANDELAAR]', $ThrpDtl->small_info, $strInvTlt);
            $strInvTlt = str_replace('[THARAPIST RBCZ_NUMBER]', $ThrpDtl->rcbz_no, $strInvTlt);
        }
        $strInvTlt = str_replace('[THARAPIST NAME]', $ThrpDtl->first_name." ".$ThrpDtl->last_name, $strInvTlt);
        $strInvTlt = str_replace("{therapistitle}",$ThrpDtl->moneybird_username,$strInvTlt);
        if(!empty($ClientDt))
        {
            /*$strInvTlt = str_replace('[CUSTOMER NAME]', $ClientDt->first_name." ".$ClientDt->last_name, $strInvTlt);*/
            $strInvTlt = str_replace('[THARAPIST ADDRESS]', $ClientDt->address, $strInvTlt);
            $strInvTlt = str_replace('[THARAPIST POSTCODE]', $ClientDt->postcode, $strInvTlt);
            $strInvTlt = str_replace('[THARAPIST CITY]', $location_name, $strInvTlt);
        }
        else
        {
            $strInvTlt = str_replace('[THARAPIST ADDRESS]', "", $strInvTlt);
            $strInvTlt = str_replace('[THARAPIST POSTCODE]', "", $strInvTlt);
            $strInvTlt = str_replace('[THARAPIST CITY]', "", $strInvTlt);
        }

        $InvNo = date("Y")." - 0001";
        $strInvTlt = str_replace('[INVOICE NUMBER]',$InvNo, $strInvTlt);
        $strInvTlt = str_replace('[DATE OF START]',date('d-m-Y'), $strInvTlt);
        $strInvTlt = str_replace('[DATE OF BOOKING]',date('d-m-Y'), $strInvTlt);

        $price=200;
        $strInvTlt = str_replace('[PRODUCT_NAME]',$linedescription, $strInvTlt);
        $strInvTlt = str_replace('[PRICE]',$price, $strInvTlt);
        $strInvTlt = str_replace('[AMOUNT]',$price, $strInvTlt);

        $strInvTlt = str_replace('[Tax]',"", $strInvTlt);
        $strInvTlt = str_replace('[SUB_TOTAL]',"", $strInvTlt);

        $strInvTlt = str_replace('[TOTALAMOUNT]',$price, $strInvTlt);

        $StrDesc = $CmpDtl->cmp_paid_inv_desc;


        $strInvTlt = str_replace('[FOOTER COMPANY NOTE]',$StrDesc, $strInvTlt);
        return $strInvTlt;
    }
    public static function EmailContentTLT($EmailMetter)
    {
        $StrEMAIL='<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>hoogewerf.praktijk-afspraken.nl</title>
    <style>
      /* -------------------------------------
          GLOBAL RESETS
      ------------------------------------- */
      
      /*All the styling goes here*/
      
      img {
        border: none;
        -ms-interpolation-mode: bicubic;
        max-width: 100%; 
      }

      body {
        background-color: #f6f6f6;
        font-family: sans-serif;
        -webkit-font-smoothing: antialiased;
        font-size: 14px;
        line-height: 1.4;
        margin: 0;
        padding: 0;
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%; 
      }

      table {
        border-collapse: separate;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        width: 100%; }
        table td {
          font-family: sans-serif;
          font-size: 14px;
          vertical-align: top; 
      }

      /* -------------------------------------
          BODY & CONTAINER
      ------------------------------------- */

      .body {
        background-color: #f6f6f6;
        width: 100%; 
      }

      /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
      .container {
        display: block;
        margin: 0 auto !important;
        /* makes it centered */
        max-width: 580px;
        padding: 10px;
        width: 580px; 
      }

      /* This should also be a block element, so that it will fill 100% of the .container */
      .content {
        box-sizing: border-box;
        display: block;
        margin: 0 auto;
        max-width: 580px;
        padding: 10px; 
      }

      /* -------------------------------------
          HEADER, FOOTER, MAIN
      ------------------------------------- */
      .main {
        background: #ffffff;
        border-radius: 3px;
        width: 100%; 
      }

      .wrapper {
        box-sizing: border-box;
        padding: 20px; 
      }

      .content-block {
        padding-bottom: 10px;
        padding-top: 10px;
      }

      .footer {
        clear: both;
        margin-top: 10px;
        text-align: center;
        width: 100%; 
      }
        .footer td,
        .footer p,
        .footer span,
        .footer a {
          color: #999999;
          font-size: 12px;
          text-align: center; 
      }

      /* -------------------------------------
          TYPOGRAPHY
      ------------------------------------- */
      h1,
      h2,
      h3,
      h4 {
        color: #000000;
        font-family: sans-serif;
        font-weight: 400;
        line-height: 1.4;
        margin: 0;
        margin-bottom: 30px; 
      }

      h1 {
        font-size: 35px;
        font-weight: 300;
        text-align: center;
        text-transform: capitalize; 
      }

      p,
      ul,
      ol {
        font-family: sans-serif;
        font-size: 14px;
        font-weight: normal;
        margin: 0;
        margin-bottom: 15px; 
      }
        p li,
        ul li,
        ol li {
          list-style-position: inside;
          margin-left: 5px; 
      }

      a {
        color: #3498db;
        text-decoration: underline; 
      }

      /* -------------------------------------
          BUTTONS
      ------------------------------------- */
      .btn {
        box-sizing: border-box;
        width: 100%; }
        .btn > tbody > tr > td {
          padding-bottom: 15px; }
        .btn table {
          width: auto; 
      }
        .btn table td {
          background-color: #ffffff;
          border-radius: 5px;
          text-align: center; 
      }
        .btn a {
          background-color: #ffffff;
          border: solid 1px #3498db;
          border-radius: 5px;
          box-sizing: border-box;
          color: #3498db;
          cursor: pointer;
          display: inline-block;
          font-size: 14px;
          font-weight: bold;
          margin: 0;
          padding: 12px 25px;
          text-decoration: none;
          text-transform: capitalize; 
      }

      .btn-primary table td {
        background-color: #3498db; 
      }

      .btn-primary a {
        background-color: #3498db;
        border-color: #3498db;
        color: #ffffff; 
      }

      /* -------------------------------------
          OTHER STYLES THAT MIGHT BE USEFUL
      ------------------------------------- */
      .last {
        margin-bottom: 0; 
      }

      .first {
        margin-top: 0; 
      }

      .align-center {
        text-align: center; 
      }

      .align-right {
        text-align: right; 
      }

      .align-left {
        text-align: left; 
      }

      .clear {
        clear: both; 
      }

      .mt0 {
        margin-top: 0; 
      }

      .mb0 {
        margin-bottom: 0; 
      }

      .preheader {
        color: transparent;
        display: none;
        height: 0;
        max-height: 0;
        max-width: 0;
        opacity: 0;
        overflow: hidden;
        mso-hide: all;
        visibility: hidden;
        width: 0; 
      }

      .powered-by a {
        text-decoration: none; 
      }

      hr {
        border: 0;
        border-bottom: 1px solid #f6f6f6;
        margin: 20px 0; 
      }

      /* -------------------------------------
          RESPONSIVE AND MOBILE FRIENDLY STYLES
      ------------------------------------- */
      @media only screen and (max-width: 620px) {
        table[class=body] h1 {
          font-size: 28px !important;
          margin-bottom: 10px !important; 
        }
        table[class=body] p,
        table[class=body] ul,
        table[class=body] ol,
        table[class=body] td,
        table[class=body] span,
        table[class=body] a {
          font-size: 16px !important; 
        }
        table[class=body] .wrapper,
        table[class=body] .article {
          padding: 10px !important; 
        }
        table[class=body] .content {
          padding: 0 !important; 
        }
        table[class=body] .container {
          padding: 0 !important;
          width: 100% !important; 
        }
        table[class=body] .main {
          border-left-width: 0 !important;
          border-radius: 0 !important;
          border-right-width: 0 !important; 
        }
        table[class=body] .btn table {
          width: 100% !important; 
        }
        table[class=body] .btn a {
          width: 100% !important; 
        }
        table[class=body] .img-responsive {
          height: auto !important;
          max-width: 100% !important;
          width: auto !important; 
        }
      }

      /* -------------------------------------
          PRESERVE THESE STYLES IN THE HEAD
      ------------------------------------- */
      @media all {
        .ExternalClass {
          width: 100%; 
        }
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
          line-height: 100%; 
        }
        .apple-link a {
          color: inherit !important;
          font-family: inherit !important;
          font-size: inherit !important;
          font-weight: inherit !important;
          line-height: inherit !important;
          text-decoration: none !important; 
        }
        #MessageViewBody a {
          color: inherit;
          text-decoration: none;
          font-size: inherit;
          font-family: inherit;
          font-weight: inherit;
          line-height: inherit;
        }
        .btn-primary table td:hover {
          background-color: #34495e !important; 
        }
        .btn-primary a:hover {
          background-color: #34495e !important;
          border-color: #34495e !important; 
        } 
      }

    </style>
  </head>
  <body class="">
     
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          <div class="content">
            <table role="presentation" class="main">
              <tr>
                <td class="wrapper">
                   '.$EmailMetter.'
                </td>
              </tr>
            </table>
            <div class="footer">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                
                <tr>
                  <td class="content-block powered-by">
                      <a href="https://hoogewerf.praktijk-afspraken.nl">hoogewerf.praktijk-afspraken.nl</a>.
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </body>
</html>';
        return $StrEMAIL;
    }

}
