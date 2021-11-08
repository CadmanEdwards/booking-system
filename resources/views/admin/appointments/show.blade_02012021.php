@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.appointments.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.appointments.fields.client')</th>
                            <td>{{ $appointment->client->first_name or '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.last-name')</th>
                            <td>{{ isset($appointment->client) ? $appointment->client->last_name : '' }}</td>
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
                            <td>{{ $appointment->employee->first_name or '' }}</td>
                        </tr>
                         <tr>
                            <th>Room No</th>
                            <td>{!! isset($appointment->room) ? $appointment->room->room_name : '' !!} </td>
                        </tr>
                        
                        <tr>
                            <th>@lang('quickadmin.employees.fields.last-name')</th>
                            <td>{{ isset($appointment->employee) ? $appointment->employee->last_name : '' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.appointments.fields.comments')</th>
                            <td>{!! $appointment->comments !!}</td>
                        </tr>
                     
                        
                        
                    </table>
                </div>
                <div class="col-md-6">
                  {!! Form::open(['method' => 'POST', 'route' => ['admin.appointments.changeinvoicestatusp'],'name'=>'changeinvoicestatus','id'=>'changeinvoicestatus']) !!} 

                  <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
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
            

            @if($CurrUser->role_id == "1")

             <a title="Delete" onclick="javascript: return confirm('Are you sure to Delete this apointment?')" 
              href="{{ route('admin.appointment_destroy',[$appointment->id]) }}" 
              class="btn btn-danger col-md-1"><i class="fa fa-trash"></i> Delete</a>
            @endif
            
             <a href="{{ route('admin.appointments.index') }}" class="btn btn-default col-md-2">@lang('quickadmin.qa_back_to_list')</a>
           
            {!! Form::open(['method' => 'POST', 'route' => ['admin.appointments.send_custom_email'],'class'=>'col-md-3','name'=>'send_custom_email','id'=>'send_custom_email']) !!} 
              <input type="hidden" name="appointment_id" value="{{ $appointment->id  }}">
              {!! Form::select('email_templates', $email_templates, old('email_templates'), ['class' => 'form-control select2 col-md-12', 'required' => '','onchange'=>'function submitform()','id'=>'email_template']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email_templates'))
                        <p class="help-block">
                            {{ $errors->first('email_templates') }}
                        </p>
                    @endif
           
            {!! Form::close() !!}

             
            @if($CurrUser->role_id == "1")
              <div class="col-md-12 no-padding">
                <h4>Doctor Email</h4>
                {!! Form::open(['method' => 'POST', 'route' => ['admin.appointments.send_custom_email_doctor'],'name'=>'send_custom_email_doctor','id'=>'send_custom_email_doctor']) !!} 
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
            @endif
            <div class="col-sm-12 no-padding">
              <h4>History</h4>
              <div class="col-sm-12 no-padding">
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
@stop

@section('javascript')
    @parent
    <script>
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
          { $("#invoice_send_to").val(""); }
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
             $("#send_custom_email").submit();
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



    </script>
@stop    
<style type="text/css">
  .loglist{display: block;list-style: none;}
  .loglist li { padding:0px; margin: 0px; display: block;border-bottom: 1px solid #CCC }
</style>