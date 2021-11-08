@extends('layouts.app')


@section('content')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

<style>
    .swiper-container {
        width: 100%;
        height: 100%;
    }
    .swiper-wrapper{
        padding-bottom: 30px;
    }

    .swiper-container-horizontal>.swiper-pagination-bullets, .swiper-pagination-custom, .swiper-pagination-fraction{
        bottom: 0px !important;
    }
    .swiper-container {
        width: 100%;
        height: 100%;
    }

    .swiper-slide {
        text-align: center;
        font-size: 18px;
        background: #fff;

        /* Center slide text vertically */
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
    }

    .swiper-pagination-bullet {
        width: 20px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        font-size: 12px;
        color: #000;
        opacity: 1;
        background: rgba(0, 0, 0, 0.2);
    }

    .swiper-pagination-bullet-active {
        color: #fff;
        background: #007aff;
    }
</style>
    <h3 class="page-title">@lang('quickadmin.appointments.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            {!! Form::label('client_id', 'Client*', ['class' => 'control-label']) !!}
                            {!! Form::select('client_id', $clients, $appointment->client->id, ['class' => 'form-control select2', 'required' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('client_id'))
                                <p class="help-block">
                                    {{ $errors->first('client_id') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">

                        <tr>
                            <th>@lang('quickadmin.appointments.fields.client')</th>
                            <td id="client_first_name_td"><span data-editable >{{ $appointment->client->first_name or '' }}</span>
							<!-- {{ 'http://localhost/pro/projects/domains/hoogewerf.praktijk-afspraken.nl/public_html/admin/clients/'.$appointment->client->id.'/edit?redirect_url='.url()->full()  }} -->
                                <a href="{{ 'https://hoogewerf.praktijk-afspraken.nl/admin/clients/'.$appointment->client->id.'/edit?redirect_url='.url()->full()  }}">
                                    Edit Client <i class="fa fa-pencil" aria-hidden="true"></i></a></td>
									

                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.last-name')</th>
                            <td id="client_last_name_td"><span data-editable >{{ isset($appointment->client) ? $appointment->client->last_name : '' }}</span></td>

                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.phone')</th>
                            <td>{{ isset($appointment->client) ? $appointment->client->phone : '' }}</td>

                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.email')</th>
                            <td>{{ isset($appointment->client) ? $appointment->client->email : '' }}</td>

                        </tr>
                        <tr>
                            <th>@lang('quickadmin.appointments.fields.employee')</th>
                            <td id="emp_first_name_td"><span data-editable  >{{ $appointment->employee->first_name or '' }} </span>
                                <a href="{{ 'https://hoogewerf.praktijk-afspraken.nl/admin/employees/'.$appointment->employee->id.'/edit?redirect_url='.url()->full()  }}">
                                    Edit Employee <i class="fa fa-pencil" aria-hidden="true"></i></a> </td>

                        </tr>
                         <tr>
                            <th>Room No</th>
                            <td>{!! isset($appointment->room) ? $appointment->room->room_name : '' !!} </td>

                        </tr>
                        
                        <tr>
                            <th>@lang('quickadmin.employees.fields.last-name')</th>
                            <td id="emp_last_name_td"><span data-editable >{{ isset($appointment->employee) ? $appointment->employee->last_name : '' }}</span>
                            </td>

                        </tr>
                        <tr>
                            <th>@lang('quickadmin.appointments.fields.comments')</th>
                            <td>{!! $appointment->comments !!}</td>

                        </tr>
                     
                        
                        
                    </table>
                </div>
                <div class="col-md-6">
                  {!! Form::open(['method' => 'POST', 'route' => ['admin.appointments.changeinvoicestatusp'],
'name'=>'changeinvoicestatus','id'=>'changeinvoicestatus']) !!}

                  <input type="hidden" name="is_save" id="is_save">
                  <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
{{--                  <input type="hidden" class="client_first_name" name="client_first_name" >--}}
{{--                  <input type="hidden" class="client_last_name" name="client_last_name" >--}}
{{--                  <input type="hidden" class="emp_first_name" name="emp_first_name" >--}}
{{--                  <input type="hidden" class="emp_last_name" name="emp_last_name" >--}}
                  <input type="hidden" class="client_id_field" name="client_id" value="0">
                    <input type="hidden" class="client_name_field" name="client_name" >
                  @if(!empty($InvoiceDetail->id))
                  <input type="hidden" name="invoice_id" value="{{ $InvoiceDetail->id }}">
                  @endif
                  <table class="table table-bordered table-striped">
                    <tr>
                      <th >factuur nummer</th>
                      <td> 
                        @if($CurrUser->role_id == "1")
                        <input type="text" name="display_inv_no" value="{{ $inv_numberStr }}" class="form-control required">
                        @else
                        <input type="hidden" name="display_inv_no" value="{{ $inv_numberStr }}">
                        {{ $inv_numberStr }}
                        @endif
                        

                      </td>
                    </tr>
                    <tr>
                      <th>Factuur Datum</th>
                      <td>
                       @if($appointment->booking_status=='' OR $CurrUser->role_id == "1")
                       <input type="text" name='inv_date' value="{{ $InvoiceDetail->inv_date }}" class="inv_date date form-control"/>
                       @else
                       <input type="hidden" name='inv_date' value="{{ $InvoiceDetail->inv_date }}" />
                        <label class="form-control">{{ $InvoiceDetail->inv_date }}</label>
                       @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Vervaldatum (Due Date)</th>
                      <td>
                       @if($appointment->booking_status=='' OR $CurrUser->role_id == "1")
                       <input type="text" name='due_date' value="{{ $InvoiceDetail->due_date }}" class="due_date date form-control"/>
                       @else
                       <input type="hidden" name='due_date' value="{{ $InvoiceDetail->due_date }}" />
                        <label class="form-control">{{ $InvoiceDetail->due_date }}</label>
                       @endif
                      </td>



                      
                    </tr>
                    @php
                      $Isgreater='';

                      if(strtotime($appointment->start_time) < strtotime(NOW()))
                           $Isgreater='Yes';
                      else
                         $Isgreater='No';
                   @endphp
                     
                    @php
                      if($Isgreater=='No' || $appointment->booking_status=='booking_paid_pin')
                         $editable=0;
                      else
                         $editable=1;
                    @endphp 
                     @if($Isgreater=='No' || $appointment->booking_status=='booking_paid_pin') 
                        
                        <tr>
                           <input type="hidden" name="edittable1" class="edittable" value="0" >
                            <th>@lang('quickadmin.appointments.fields.start-time')</th>
                            <td>{{ $appointment->start_time }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.appointments.fields.finish-time')</th>
                            <td>{{ $appointment->finish_time }}</td>
                        </tr>
                      @else
                           
                        <tr>
                          <input type="hidden" name="edittable1" class="edittable" value="1" />
                            <th>@lang('quickadmin.appointments.fields.start-time')</th>
                            <td>
                              <div class="col-md-6 no-padding">
                                <input type="text" name="sdate1" class="date form-control" value="{{ date('Y-m-d',strtotime($appointment->start_time)) }}"/>
                              </div>
                              <div class="col-md-6 no-padding">
                                <input type="time" name="start_time1" class=" form-control start_time"  value="{{ date('H:i',strtotime($appointment->start_time)) }}"/>
                              </div>
                     </td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.appointments.fields.finish-time')</th>
                            <td><input type="time" name="finish_time1" class="finish_time form-control" value="{{ date('H:i',strtotime($appointment->finish_time)) }}"/></td>
                        </tr>
                        
                      @endif
                      <tr>
                        <th>Bedrag</th>
                         @if($appointment->booking_status=='' OR $CurrUser->role_id == "1")
                          <td><input type="text" name='price' id="price" value="{{ $InvoiceDetail->price }}" class="form-control chaneprice priceChange"  /></td>
                         @else
                         <td>{!! $InvoiceDetail->price !!}
                          <input type="hidden" name='price' id="price" value="{{ $InvoiceDetail->price }}"/>
                         </td>
                         @endif
                    </tr>
                    <tr>
                            <th>Extra Added </th>
                             
                              <td><input type="text" name='extra_price_comment' value="{{ $appointment->extra_price_comment }}" class="form-control extra_price_comment" /></td>
                             
                        </tr>
                    <tr>
                        <th>Omschrijving</th>
                        <td>
                          <textarea name="prd_description" class="moneybird_username form-control" style="width: 100%"  rows="4">{{ $InvoiceDetail->prd_description }}</textarea> 
                        </td>
                    </tr>
                    <tr>
                      <th>BTW-tarief (VAT Class)</th>
                      <td>
                        @if($appointment->booking_status=='' OR $CurrUser->role_id == "1")
                        <select name="taxid" id="taxid" class="form-control chaneprice">
                          <option value="" class="hide">--Select Tax--</option>
                          @foreach($TaxData as $key=> $val)
                            @if($val->id == $InvoiceDetail->taxid)
                              <option selected="selected" value="{{ $val->id }}" id="tax_{{ $val->id }}" date-price="{{ $val->percentage}}">
                                {{ $val->name}} 
                              </option>
                            @else
                              <option value="{{ $val->id }}" id="txx_{{ $val->id }}" date-price="{{ $val->percentage}}" data-title="{{ $val->name}}">{{ $val->name}} </option>
                            @endif  
                          @endforeach
                        </select>
                        @else
                          <input type="hidden" name="taxid" id="taxid" value="{{ $InvoiceDetail->taxid }}">
                          <label class="form-control">{{ $InvoiceDetail->taxrate_title }}</label>
                        @endif
                        
                        <input type="hidden" name="taxrate_title" id="taxrate_title" value="{{ $InvoiceDetail->taxrate_title }}">
                        <input type="hidden" name="taxrate" id="taxrate" value="{{ $InvoiceDetail->taxrate }}">
                      </td>
                    </tr>
                    <tr>
                      <th>Btw Inclusief / Uitsluiten</th>
                      <td>
                        @if($appointment->booking_status=='' OR $CurrUser->role_id == "1")
                          <select name="tax_inc_exc" id="tax_inc_exc" class="form-control chaneprice">
                            <option value="1" @if(1 == $InvoiceDetail->tax_inc_exc) selected="selected" @endif>Btw Uitsluiten</option>
                            <option value="0" @if(0 == $InvoiceDetail->tax_inc_exc) selected="selected" @endif >Btw Inclusief</option>
                          </select>
                        @else
                          <input type="hidden" name="tax_inc_exc"  id="tax_inc_exc" value="{{ $InvoiceDetail->tax_inc_exc }}">
                          @if(1 == $InvoiceDetail->tax_inc_exc)
                            <label>Btw Uitsluiten</label>
                          @else
                            <label>Btw Inclusief</label>
                          @endif
                        @endif
                        

                      </td>
                    </tr>
                    
                    <tr>
                      <th>Basis Bedrag (Base Value)</th>
                      <td>
                        <input type="text" class="form-control" id="baseamount" name="baseamount" value="{{ $InvoiceDetail->baseamount }}" readonly="readonly">
                      </td>
                    </tr>
                    <tr>
                      <th>Btw Bedrag (VAT Value)</th>
                      <td>
                        <input type="text" class="form-control" id="taxamount" name="taxamount" value="{{ $InvoiceDetail->taxamount }}" readonly="readonly">
                      </td>
                    </tr>
                    <tr>
                      <th>Netto bedrag (Net Value)</th>
                      <td>
                        <input type="text" class="form-control" id='netamount' name="netamount" value="{{ $InvoiceDetail->netamount }}" readonly="readonly">
                      </td>
                    </tr>
                    
                    <tr>
                      <th>Boekingsstatus</th>
                      <td>
                      @if("booking_paid_pin" == $appointment->booking_status)
                          @if($appointment->paid_waya=="")
                            Pin Paid
                          @else
                            {{$appointment->paid_waya}} Paid
                          @endif
                      @else
                          @if("booking_unpaid" == $appointment->booking_status)
                            Unpaid
                          @else
                            {{ $appointment->booking_status }}
                          @endif

                      @endif
                        <input type="hidden" name="paid_waya" id="paid_waya" value="{{ $appointment->paid_waya }}" />
                        <input type="hidden" name="booking_status" id="booking_status" value="{{ $appointment->booking_status }}">
                      </td>
                    </tr>
                    <!-- <tr class="tr_paid_waya @if('booking_paid_pin' != $appointment->booking_status) hide @endif"">
                      <th>Betaling door</th>
                      <td>
                        @if($appointment->booking_status=='' OR $CurrUser->role_id == "1")
                          @if("Mollie" == $appointment->paid_waya)
                            Mollie <input type="hidden" name="paid_waya" id="paid_waya" value="Mollie" />
                          @else
                            <select name="paid_waya" id="paid_waya" class="form-control chaneprice">
                              <option value="">--Selecteer--</option>
                              <option value="Cash" @if("Cash" == $appointment->paid_waya) selected="selected" @endif>Cash</option>
                              <option value="Bank" @if("Bank" == $appointment->paid_waya) selected="selected" @endif>Bank</option>
                            </select>
                          @endif
                        @else
                          {{ $appointment->paid_waya }}
                          
                        @endif
                      </td>
                    </tr> -->
                    @php
                      $PaidAmt = $DueAmount = 0;
                      if(!empty($InvoiceDetail->payment_received))
                      {
                        $ArrPAmt = $InvoiceDetail->payment_received;
                        if(!empty($ArrPAmt->I->amount))
                          { $PaidAmt = $PaidAmt + $ArrPAmt->I->amount; }
                        if(!empty($ArrPAmt->II->amount))
                          { $PaidAmt = $PaidAmt + $ArrPAmt->II->amount; }
                      }
                      $DueAmount =number_format($InvoiceDetail->netamount - $PaidAmt,2);
                    @endphp

                    @if($CurrUser->role_id == '1')
                      <tr class="partial_payment"><th colspan="2" class="text-center">If Payment Received (Partial)</th></tr>
                      <tr class="partial_payment">
                        <th>Instalment - I</th>
                        <td>
                          <div class="col-md-6 col-xs-12 no-padding">
                            <input type="text" class="form-control" name="payment_received[I][amount]" value="{{ $InvoiceDetail->payment_received->I->amount }}"  placeholder="Amount" >
                          </div>
                          <div class="col-md-6 col-xs-12 no-padding">
                           <input type="text" class=" date form-control" placeholder="Date" name="payment_received[I][date]" 
                           value="{{ $InvoiceDetail->payment_received->I->date }}" >
                          </div>
                        </td>
                      </tr>
                      <tr class="partial_payment">
                        <th>Instalment - II</th>
                        <td>
                          <div class="col-md-6 col-xs-12 no-padding">
                            <input type="text" class="form-control" placeholder="Amount"
                            name="payment_received[II][amount]"                           value="{{ $InvoiceDetail->payment_received->II->amount }}"  >
                          </div>
                          <div class="col-md-6 col-xs-12 no-padding">
                            <input type="text" class=" date form-control" 
                            name="payment_received[II][date]" placeholder="Date"
                            value="{{ $InvoiceDetail->payment_received->II->date }}"  >
                          </div>
                        </td>
                      </tr>
                    @else
                      <tr class="partial_payment @if(($appointment->booking_status=='booking_paid_pin') || ($DueAmount == $InvoiceDetail->netamount) || ($DueAmount == "0.00")) hide @endif">
                        <th>openstaand bedrag</th>
                        <td>
                          <input type="hidden" class="hide" 
                          name="payment_received[I][amount]"
                          value="{{ $InvoiceDetail->payment_received->I->amount }}" />
                          
                          <input type="hidden" class="hide" 
                          name="payment_received[I][date]"
                          value="{{ $InvoiceDetail->payment_received->I->date }}" />

                          <input type="hidden" class="hide" 
                          name="payment_received[II][amount]"
                          value="{{ $InvoiceDetail->payment_received->II->amount }}" />
                          
                          <input type="hidden" class="hide" 
                          name="payment_received[II][date]"
                          value="{{ $InvoiceDetail->payment_received->II->date }}" />
                          <div class="form-control col-xs-12">
                            {{ $DueAmount }}
                          </div>
                        </td>
                      </tr>
                    @endif
                    <tr>
                      <th>Email To</th>
                      <td>
                        
                        @php
                        $InvEmail="";
                        if(!empty($appointment->client->email))
                          { $InvEmail = $appointment->client->email; }

                        if(!empty($appointment->client->email_invoice))
                          { 
                            if($InvEmail!=""){$InvEmail=$InvEmail.",";}
                            $InvEmail = $InvEmail.$appointment->client->email_invoice; 
                          }
                        @endphp
                          <textarea name="invoice_send_to" id="invoice_send_to" class="form-control">{{$InvEmail}}</textarea>
                      </td>
                    </tr>

                    <tr>
                      <td colspan=2 class="text-center"> 
                        @can('appointment_edit')

                        @if($appointment->booking_status=='')
                          <button type="button" class="btn btn-default" onclick="changestatus('paid_cash')">Cash Paid </button>

                          <button type="button" class="btn btn-default"
                          onclick="changestatus('booking_paid_pin')">
                            Paid Pin
                          </button>

                          <button type="button" class="btn btn-default"
                            onclick="changestatus('booking_unpaid')">Unpaid
                          </button>
                        @elseif( ($CurrUser->role_id == "1") && ($appointment->booking_status=='booking_unpaid'))
                          <button type="button" class="btn btn-default" onclick="changestatus('paid_cash')">Cash Paid </button>

                          <button type="button" class="btn btn-default"
                          onclick="changestatus('booking_paid_pin')">
                            Paid Pin
                          </button>
                        @elseif( ($CurrUser->role_id == "1") && ($appointment->booking_status=='booking_paid_pin'))
                          @if(($appointment->paid_waya==''))
                            <button type="button" class="btn btn-default" onclick="changestatus('paid_cash')">Cash Paid </button>

                            <button type="button" class="btn btn-default"
                              onclick="changestatus('booking_unpaid')">Unpaid
                            </button>
                          @elseif(($appointment->paid_waya=='Cash'))
                            <button type="button" class="btn btn-default"
                              onclick="changestatus('booking_paid_pin')">
                              Paid Pin
                            </button>
                            <button type="button" class="btn btn-default"
                              onclick="changestatus('booking_unpaid')">Unpaid
                            </button>
                          @else
                            <button type="button" class="btn btn-default"
                              onclick="changestatus('booking_unpaid')">Unpaid
                            </button>
                          @endif
                        @endif
                            @if(\Illuminate\Support\Facades\Auth::guard('web')->user()->role_id == 1 &&  $appointment->booking_status=='booking_unpaid')
                                <select  name="invoice_email_type"  class="form-control" style="width: auto;display: inline"  id="invoice_email_type" onchange="sendInvoiceEmail()">
                                    <option value="">Select Invoice Email Type</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            @endif
                        @if($CurrUser->role_id == "1")
                          <button type="button" class="btn btn-default"
                           onclick="changestatus('save')">Save
                          </button>
                        
                        @endif

                        
                        
                        

                        <!-- @if( $CurrUser->role_id == "1")
                        
                          <button type="submit" name="appointment" class="btn btn-default "><i class="fa fa-pencil"></i> Save </button>
                        
                        @elseif(!($appointment->booking_status=='booking_unpaid' OR $appointment->booking_status=='booking_paid_pin') )
                          
                          <button type="submit" name="appointment" class="btn btn-default "><i class="fa fa-pencil"></i> Save </button>
                        @endif -->

                        @endcan

                        



                        @if(!empty($InvoiceDetail->id) && ((($appointment->booking_status=='booking_unpaid') OR ($appointment->booking_status==''))))
                        <a href="{{ route('mollie.payment',[$appointment->id]) }}">
                          <button type="button"  class="btn btn-success ">
                            <i class="fa fa-euro"></i> {{ $DueAmount }} Online Payment
                          </button>
                        </a>
                        @endif


                      </td>

                    </tr>
                  </table>
                  {!! Form::close() !!}
                  @if(!empty($InvoiceDetail->id))

                  {!! Form::open(['method' => 'POST', 'route' => ['admin.appointments.sendinvoice'],'name'=>'emailinvoicefrm','id'=>'emailinvoicefrm']) !!} 
                  <table class="table table-bordered table-striped">
                    <tr>
                      <th>Invoice Email</th>
                      <td>
                          <input type="hidden" name="appointment_id" value="{{$appointment->id}}">
{{--                          <input type="hidden" class="client_first_name" name="client_first_name" >--}}
{{--                          <input type="hidden" class="client_last_name" name="client_last_name" >--}}
{{--                          <input type="hidden" class="emp_first_name" name="emp_first_name" >--}}
{{--                          <input type="hidden" class="emp_last_name" name="emp_last_name" >--}}
                          <input type="hidden" class="client_id_field" name="client_id" value="0">
                          <input type="hidden" class="client_name_field" name="client_name" >
                          <textarea name="invoiceemail" id="invoiceemail" class="form-control">{{$InvEmail}}</textarea>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" class="text-center">
                        <button type="submit" id="sendbtn" class="btn btn-default"><i class="fa fa-envelope"></i> Send Email</button>

                        <button type="button" class="btn btn-success prientview">
                          <i class="fa fa-print"></i> Print</button>
                      </td>
                    </tr>
                  </table>
                  {!! Form::close() !!}
                   @endif 
                </div>
            </div>

            @if($CurrUser->role_id == "1" OR $CurrUser->role_id == "3")
              <div class="panel panel-default">
                <div class="panel-heading">Doctor Email</div>
                <div class="panel-body">
                  <div class="col-md-12 no-padding">
                    {!! Form::open(['method' => 'POST', 'route' => ['admin.appointments.send_custom_email_doctor_confirmed'],'name'=>'send_custom_email_doctor','id'=>'send_custom_email_doctor' ,'onsubmit' => 'sendDoctorEmail()']) !!}
                      <input type="hidden" name="appointment_id" value="{{ $appointment->id  }}">
          <span class="hide email_ref_employee">{{ $ArrEmail['employee'] }}</span>
          <span class="hide email_ref_client">{{ $ArrEmail['client'] }}</span>
          <span class="hide email_ref_doctor">{{ $ArrEmail['doctor'] }}</span>
                      <div class="col-xs-12 col-sm-4">
                        {!! Form::select('email_templates_doctor', $email_templates_doctor, old('email_templates_doctor'), ['class' => 'form-control select2 col-md-12', 'required' => 'required','onchange'=>'emaildoctor()','id'=>'email_templates_doctor']) !!}
                          <p class="help-block"></p>
                          @if($errors->has('email_templates_doctor'))
                              <p class="help-block">
                                  {{ $errors->first('email_templates_doctor') }}
                              </p>
                          @endif
                      </div>
                      <div class="col-xs-12  col-sm-4">
                        <textarea name="email_doctor" id="email_doctor" class="form-control" required="required"></textarea>
                      </div>
                      <div class="col-xs-12  col-sm-4">
                        <button type="submit"   class="btn btn-default"><i class="fa fa-envelope"></i> Send Email</button>
                      </div>



                    {!! Form::close() !!}
                  </div>
                </div>
              </div>
              
            @endif
            
            <div class="panel panel-default">
                <div class="panel-heading">Client Email</div>
                <div class="panel-body">
                  <div class="col-md-12 col-xs-12 no-padding">
            
           <div class="col-md-4 col-xs-12 ">
            {!! Form::open(['method' => 'POST', 'route' => ['admin.appointments.send_custom_email'],'name'=>'send_custom_email','id'=>'send_custom_email']) !!}
              <input type="hidden" id="appoint_id" name="appointment_id" value="{{ $appointment->id  }}">
{{--              <input type="hidden" id="tok" name="_token" value="{{ csrf_token()  }}">--}}
               <input type="hidden" id="sendClientToken" name="_token" value="{{ csrf_token() }}">
              {!! Form::select('email_templates', $email_templates, old('email_templates'), ['class' => 'form-control select2  col-xs-12 no-padding', 'required' => ''
            ,'id'=>'email_template']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email_templates'))
                        <p class="help-block">
                            {{ $errors->first('email_templates') }}
                        </p>
                    @endif
           
            {!! Form::close() !!}
          </div>
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-default col-md-2 col-xs-4">@lang('quickadmin.qa_back_to_list')</a>

            @if($CurrUser->role_id == "1")
             <a title="Delete" onclick="javascript: return confirm('Are you sure to Delete this apointment?')" 
              href="{{ route('admin.appointment_destroy',[$appointment->id]) }}" 
              class="btn btn-danger col-md-1 col-xs-4 pull-right"><i class="fa fa-trash"></i> Delete</a>
            @endif
            
             

            </div>
                </div>
            </div>  
            
             
            
            <div class="panel panel-default">
              <div class="panel-heading">History</div>
              <div class="panel-body">
                <div class="col-sm-12">
                  <ul class="loglist">
                      <li>
                        <div class="row">
                          <div class="col-sm-2 no-padding"> <b>Update By</b></div>
                          <div class="col-sm-2"> <b>Log on</b> </div>
                          <div class="col-sm-8 no-padding"> <b>Message</b> </div>
                        </div>
                      </li>
                      @foreach($InvLog as $key2=> $val2 )
                      <li>
                        <div class="row">
                          <div class="col-sm-2 no-padding"> {{ $val2->name }} </div>
                          <div class="col-sm-2"> {{ $val2->log_datetime }} </div>
                          <div class="col-sm-8 no-padding"> {!! $val2->message !!} </div>
                        </div>
                      </li>
                      @endforeach
                      <li></li>
                  </ul>
                </div>
              </div>
            </div>
             
        </div>
        <div class="modal fade" id="emailDoctorModal" data-backdrop="static" data-keyboard="false" style="overflow:hidden;" role="dialog" aria-labelledby="emailDoctorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                        <h4 id="modalTitle" class="modal-title"></h4>
                    </div>

                    <div  class="modal-body">
                        <div class="swiper-container">
                            <div class="swiper-wrapper" id="emailDoctorModalBody">


                            </div>
                            <!-- Add Pagination -->
                            <div class="swiper-pagination"></div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="sendDoctorMail" class="btn btn-primary">Send Mail(s)</button>
                    </div>
                    <div class="modal-footer">

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="emailClientModal" data-backdrop="static" data-keyboard="false" style="overflow:hidden;" role="dialog" aria-labelledby="emailClientModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                        <h4 id="modalTitle" class="modal-title"></h4>
                    </div>

                    <div  class="modal-body">
                        <div class="swiper-container">
                            <div class="swiper-wrapper" id="emailClientModalBody">


                            </div>
                            <!-- Add Pagination -->
                            <div class="swiper-pagination"></div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="sendClientMail" class="btn btn-primary">Send Mail(s)</button>
                    </div>
                    <div class="modal-footer">

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="emailInvoiceModal" data-backdrop="static" data-keyboard="false" style="overflow:hidden;" role="dialog" aria-labelledby="emailInvoiceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                        <h4 id="modalTitle" class="modal-title"></h4>
                    </div>

                    <div  class="modal-body">
                        <textarea id="invoiceText" class="form-control" name="invoiceText" rows="10" cols="60"></textarea>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="sendInvoiceMail" class="btn btn-primary">Send Mail(s)</button>
                    </div>
                    <div class="modal-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    @parent
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <script src="https://cdn.tiny.cloud/1/mahjowjx5cbjsjflu83u2suiolbmvkxa7e5ntqflqrx52tgt/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>

       $("#client_id").on("change", function() {

            if (confirm("Are you sure you want to change the client name?")) {
                $('.client_id_field').val($(this).val())
                $('.client_name_field').val($(this).select2('data')[0].text)
            } else {
               window.location.reload()
            }


          //  console.log($(this).val())
          //  return false;
          //   const fullName = $(this).select2('data')[0].text.split(' ');
          //   const lastName = fullName.pop();
          //   const firstName = fullName.join(' ');


            // $('#client_first_name_td > span').text(firstName);
            // $('#client_last_name_td > span').text(lastName);
            // $('.client_first_name').val(firstName);
            // $('.client_last_name').val(lastName);
       });
        function editData(e) {
            const el = e.target;
            const input = document.createElement("input");
            input.setAttribute("value", el.textContent);
            el.replaceWith(input);

            const save = function() {
                const previous = document.createElement(el.tagName.toLowerCase());
                previous.onclick = editData;
                previous.textContent = input.value;
                input.replaceWith(previous);

                $('.client_first_name').val($('#client_first_name_td > span').text());
                $('.client_last_name').val($('#client_last_name_td > span').text());
                $('.emp_first_name').val($('#emp_first_name_td > span').text());
                $('.emp_last_name').val($('#emp_last_name_td > span').text());
            };

            /**
             We're defining the callback with `once`, because we know that
             the element will be gone just after that, and we don't want
             any callbacks leftovers take memory.
             Next time `p` turns into `input` this single callback
             will be applied again.
             */
            input.addEventListener('blur', save, {
               once: true,
            });
            input.focus();



        }

        for (const child of document.querySelectorAll('[data-editable]')) {
            child.onclick = editData;
        }
        tinymce.init({
            selector: '#invoiceText',
            plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak code',
            toolbar_mode: 'floating',
            height: 500
        });

     //   $(document).ready(function(){
            // $('.client_first_name').val($('#client_first_name_td > span').text());
            // $('.client_last_name').val($('#client_last_name_td > span').text());
            // $('.emp_first_name').val($('#emp_first_name_td > span').text());
            // $('.emp_last_name').val($('#emp_last_name_td > span').text());
      //  });
        function sendDoctorEmail(){

            event.preventDefault();
            var formData = new FormData($('#send_custom_email_doctor')[0]);
            formData.append("matter",$('#emailDoctorModalBody > textarea').val())

                    $.ajax({
                        method: 'POST',
                        url: '/admin/send_custom_email_doctor',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                    }).success(function ({html}){

                        $('#emailDoctorModal').modal('show')
                        $('#emailDoctorModalBody').html("");


                            $('#emailDoctorModalBody').html(html);


                        var swiper = new Swiper('.swiper-container', {
                            pagination: {
                                el: '.swiper-pagination',
                                clickable: true,
                                renderBullet: function (index, className) {
                                    return '<span class="' + className + '">' + (index + 1) + '</span>';
                                },
                            },
                            observer: true,
                            observeParents: true
                        });



                    }).done(function () {
                        CKEDITOR.replace( 'edi' );
                    });


                return false;



        }
        function sendInvoiceEmail(){

            event.preventDefault();
            var formData = new FormData();
            formData.append("invoice_email_type",$('#invoice_email_type').val())
            formData.append("appointment_id",'{{ $appointment->id }}')
            formData.append("_token",$('#sendClientToken').val())
            $.ajax({
                method: 'POST',
                url: '/admin/send_custom_email_invoice',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
            }).success(function ({html}){

                $('#emailInvoiceModal').modal('show')
                $('#invoiceText').val("");

                $('#invoiceText').val(html);


            }).done(function () {

            });


            return false;



        }
        function sendClientEmail(){

            event.preventDefault();
            var formData = new FormData($('#send_custom_email')[0]);
            formData.append("matter",$('#emailClientModalBody > textarea').val())
            formData.append("_token",$('#sendClientToken').val())

            $.ajax({
                method: 'POST',
                url: '/admin/send_custom_email',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
            }).success(function ({html}){

                $('#emailClientModal').modal('show')
                $('#emailClientModalBody').html("");


                $('#emailClientModalBody').html(html);


                var swiper = new Swiper('.swiper-container', {
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                        renderBullet: function (index, className) {
                            return '<span class="' + className + '">' + (index + 1) + '</span>';
                        },
                    },
                    observer: true,
                    observeParents: true
                });



            }).done(function () {
                CKEDITOR.replace( 'edi' );
            });


            return false;



        }
        $('#sendInvoiceMail').on("click",function (){
            $(this).attr('disabled','true').text('Sending....')
            event.preventDefault();
            var formData = new FormData();

            formData.append("matter",$('#invoiceText').val())
            formData.append("appointment_id",'{{ $appointment->id }}')
            formData.append("invoice_email_type",$('#invoice_email_type').val())
            formData.append("_token",$('#sendClientToken').val())
            $.ajax({
                method: 'POST',
                processData: false,
                contentType: false,
                cache: false,
                url: '/admin/send_custom_email_invoice_confirmed',
                data: formData
            }).success(function (data) {
                alert(data.message);
                if(data.status){
                    $('#emailInvoiceModal').modal('hide')
                    window.location.reload()
                }
            }).done(function () {
                $('#sendInvoiceMail').removeAttr('disabled').text('Send Mail')
            });
        });
        $('#sendClientMail').on("click",function (){
            $(this).attr('disabled','true').text('Sending....')
            event.preventDefault();
            var formData = new FormData($('#send_custom_email_client')[0]);
            formData.append("matter",CKEDITOR.instances['edi'].getData())
            formData.append("_token",$('#sendClientToken').val())
            formData.append("appointment_id",$('#appoint_id').val())
            formData.append("email_templates",$('#email_template').val())


            $.ajax({
                method: 'POST',
                processData: false,
                contentType: false,
                cache: false,
                url: '/admin/send_custom_email_confirmed',
                data: formData
            }).success(function (data) {

                alert(data.message);
                if(data.status){
                    $('#emailClientModal').modal('hide')
                    window.location.reload()
                }
            }).done(function () {
                $('#sendClientMail').removeAttr('disabled').text('Send Mail')
            });
        });
        $('#sendDoctorMail').on("click",function (){
            $(this).attr('disabled','true').text('Sending....')
            event.preventDefault();
            var formData = new FormData($('#send_custom_email_doctor')[0]);
            formData.append("matter",CKEDITOR.instances['edi'].getData())
            $.ajax({
                method: 'POST',
                processData: false,
                contentType: false,
                cache: false,
                url: '/admin/send_custom_email_doctor_confirmed',
                data: formData
            }).success(function (data) {
                alert(data.message);
                if(data.status){
                    $('#emailDoctorModal').modal('hide')
                    window.location.reload()
                }
            }).done(function () {
                $('#sendDoctorMail').removeAttr('disabled').text('Send Mail')
            });
        });

      function emaildoctor()
      {
        eid=$("#email_templates_doctor").val();

          

        if(eid=="27" || eid=="28")
          { $("#email_doctor").html($(".email_ref_doctor").html()); }
        else
          { $("#email_doctor").html($(".email_ref_employee").html()); }
      }
      function changestatus(status)
      {
        
        if("booking_paid_pin"==status)
        {
          $("#booking_status").val("booking_paid_pin");
          $("#paid_waya").val("");
        }
        else if("paid_cash"==status)
        {
          $("#booking_status").val("booking_paid_pin");
          $("#paid_waya").val("Cash");
        }
        else if("booking_unpaid"==status)
        {
          $("#booking_status").val("booking_unpaid");
          $("#paid_waya").val("");
        }
        else if("save"==status) 
          { 
			  $("#invoice_send_to").val(""); 
			  $("#is_save").val("save"); 
		}
        $("#changeinvoicestatus").submit();
        
      }

      $("#booking_status").on("change", function() {

        var cstatus = $(this).val();
        $(".partial_payment").removeClass('hide');
        if(cstatus!='partial_paid') { $(".partial_payment").addClass('hide'); }

      });

      $(".chaneprice").on("change", function() {
            
            price = $("#price").val();
            price = price*1;
             if(!(price>0))
                  {
                      alert("Please enter proper value in Integer Only and grater then 0");
                     return false;   
                 }
            
            
            tax_inc_exc = $("#tax_inc_exc").val();
            
            taxid = $("#taxid").val();
            
            taxrate = $("#txx_"+taxid).attr('date-price'); 
            $("#taxrate").val(taxrate);

            taxrate_title = $("#txx_"+taxid).attr('data-title'); 
            $("#taxrate_title").val(taxrate_title);
            
            taxrate = taxrate*1;
            if(taxrate > 0 && price > 0)
              { 
                taxamount = (price*(taxrate/100)); 
                taxamount.toFixed(4);
              }
            else
              { taxamount = 0; }

            
            
            taxamount = taxamount*1;
            $("#taxamount").val(taxamount);
            if(tax_inc_exc==0)// include
            {
              $("#netamount").val(price);
              baseamount =price - taxamount; 
              $("#baseamount").val(baseamount); 
            }
            else
            {
              $("#baseamount").val(price);
              netamount = price + taxamount;
              $("#netamount").val(netamount);
            }
        });

        $("#email_template").on("change", function() {
          //  $("#send_custom_email").submit();
            sendClientEmail()
        });

              

        $(".prientview").on("click", function() {

              window.open("{{ route('admin.appointments.pview',$appointment->id) }}", "socialPopupWindow",
                "location=no,width=600,height=600,scrollbars=yes,top=100,left=700,resizable = no");

        });

        

         $('.date').datepicker({
            autoclose: true,
            dateFormat: "{{ config('app.date_format_js') }}"
        });
        
    </script>
    <script src="{{ url('quickadmin/js') }}/timepicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>

    <script>
      

         
        $('.timepicker').datetimepicker({
            autoclose: true,
            timeFormat: "HH:mm:ss",
            timeOnly: true
        });

        function submitForm(){

            var formData = new FormData();
          formData.append('email_templates',$('#email_template').val());
          formData.append('appointment_id',$('#appoint_id').val());
          formData.append('_token',$('#tok').val());

            $.ajax({
                method: 'POST',
                processData: false,
                contentType: false,
                cache: false,
                url: '/admin/send_custom_email',
                data: formData
            }).success(function (data) {
                console.log(data)
                // if(data.status){
                //
                // }
            }).done(function () {
              //  $('#sendDoctorMail').removeAttr('disabled').text('Send Mail')
            });
        }

    </script>
@stop    
<style type="text/css">
  .loglist{display: block;list-style: none;}
  .loglist li { padding:0px; margin: 0px; display: block;border-bottom: 1px solid #CCC }
</style>