@extends('layouts.auth')

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
                  
                  
                  <table class="table table-bordered table-striped">
                    <tr><th >factuur nummer</th><td class="form-control"> {{ $inv_numberStr }}</td></tr>
                    <tr>
                      <th>Factuur Datum</th>
                      <td>
                        {{ $InvoiceDetail->inv_date }}
                      </td>
                    </tr>
                    <tr>
                      <th>Vervaldatum (Due Date)</th>
                      <td><input type="text" name='due_date' value="{{ $InvoiceDetail->due_date }}" class="due_date date form-control" /></td>
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
                           
                            <th>@lang('quickadmin.appointments.fields.start-time')</th>
                            <td>{{ $appointment->start_time }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.appointments.fields.finish-time')</th>
                            <td>{{ $appointment->finish_time }}</td>
                        </tr>
                      @else
                           
                        <tr>
                          
                            <th>@lang('quickadmin.appointments.fields.start-time')</th>
                            <td>
                              <div class="col-md-6 no-padding">
                                {{ date('Y-m-d',strtotime($appointment->start_time)) }}
                              </div>
                              <div class="col-md-6 no-padding">
                                {{ date('H:i',strtotime($appointment->start_time)) }}
                              </div>
                     </td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.appointments.fields.finish-time')</th>
                            <td>{{ date('H:i',strtotime($appointment->finish_time)) }}</td>
                        </tr>
                        
                      @endif 
                      <tr>
                        <th>Bedrag</th>
                         <td>{!! $InvoiceDetail->price !!}</td>
                    </tr>
                    <tr>
                        <th>Omschrijving</th>
                        <td>
                          {{ $InvoiceDetail->prd_description }}
                        </td>
                    </tr>
                    <tr>
                      <th>BTW-tarief (VAT Class)</th>
                      <td>
                        {{ $InvoiceDetail->taxrate_title }}
                      </td>
                    </tr>
                    
                    
                    <tr>
                      <th>Basis Bedrag (Base Value)</th>
                      <td>
                        {{ $InvoiceDetail->baseamount }}
                      </td>
                    </tr>
                    <tr>
                      <th>Btw Bedrag (VAT Value)</th>
                      <td>
                        {{ $InvoiceDetail->taxamount }}
                      </td>
                    </tr>
                    <tr>
                      <th>Netto bedrag (Net Value)</th>
                      <td>
                        {{ $InvoiceDetail->netamount }}
                      </td>
                    </tr>
                    <tr>
                      <th>Boekingsstatus</th>
                      <td>
                        {{ $appointment->booking_status }}
                      </td>
                    </tr>
                    <tr>
                      <th>Betaling door</th>
                      <td>
                        {{ $appointment->paid_waya }}
                      </td>
                    </tr>
                    <tr class="partial_payment @if('partial_paid' != $appointment->booking_status) hide @endif"><th colspan="2" class="text-center">Payment Received (Partial)</th></tr>
                    <tr class="partial_payment  @if('partial_paid' != $appointment->booking_status) hide @endif">
                      <th>Instalment - I</th>
                      <td>
                        <div class="col-md-6 col-xs-12 no-padding">
                          {{ $InvoiceDetail->payment_received->I->amount }}
                        </div>
                        <div class="col-md-6 col-xs-12 no-padding">
                         {{ $InvoiceDetail->payment_received->I->date }}
                        </div>
                      </td>
                    </tr>
                    <tr class="partial_payment  @if('partial_paid' != $appointment->booking_status) hide @endif">
                      <th>Instalment - II</th>
                      <td>
                        <div class="col-md-6 col-xs-12 no-padding">
                          {{ $InvoiceDetail->payment_received->II->amount }}
                        </div>
                        <div class="col-md-6 col-xs-12 no-padding">
                          {{ $InvoiceDetail->payment_received->II->date }}
                        </div>
                      </td>
                    </tr>
 
                  </table>
                  
                 
                </div>
            </div>
        </div>
    </div>

<style type="text/css">
  .loglist{display: block;list-style: none;}
  .loglist li { padding:0px; margin: 0px; display: block;border-bottom: 1px solid #CCC }
</style>
@stop
 
