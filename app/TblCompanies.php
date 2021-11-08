<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TblCompanies extends Model
{
     protected $fillable = [  'cmp_name','cmp_streetname', 'cmp_postalcode', 'cmp_place', 'cmp_email', 'cmp_web', 'cmp_contact_no', 'cmp_chmbrcno', 'cmp_vat_no', 'cmp_bank_ac_no', 
     'cmp_bank_ac_name', 'cmp_logo1', 'cmp_logo2', 'cmp_paid_inv_desc', 'cmp_cashpaid_inv_desc','cmp_partial_paid_inv_desc'
    , 'cmp_unpaid_inv_desc','reminders_days','expired_days','invoice_template'];
}

 