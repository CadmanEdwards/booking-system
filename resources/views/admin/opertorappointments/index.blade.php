@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.appointments.title')</h3>
    @can('oappointment_create')
        <p>
            <a href="{{ route('admin.opertorappointments.create') }}"
               class="btn btn-success">@lang('quickadmin.qa_add_new')</a>

        </p>
    @endcan

    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
   <div id='calendar'></div>

    <br />

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($appointments) > 0 ? 'datatable' : '' }} @can('appointment_delete') dt-select @endcan">
                <thead>
                <tr>
                    @can('appointment_delete')
                        <th style="text-align:center;"><input type="checkbox" id="select-all"/></th>
                    @endcan
                    <th>Status</th>
                    <th>@lang('quickadmin.appointments.fields.start-time')</th>
                    <th>@lang('quickadmin.appointments.fields.finish-time')</th>
                    <th>Price</th>
                    <th>Customer Name</th>
                    
                    <th>@lang('quickadmin.clients.fields.phone')</th>
                    <th>Location</th>
                    {{-- <th>@lang('quickadmin.clients.fields.email')</th> --}}
                    <th>Therapy Name</th>
                    <th>Therapist Name</th>
                    <th>Room No</th>
                    
                    
                    
                    {{-- <th>@lang('quickadmin.appointments.fields.comments')</th> --}}
                    <th>@lang('quickadmin.appointments.fields.moneybird_status')</th>
                    <th>Booking status</th>
                    
                    <th>&nbsp;</th>
                </tr>
                </thead>
              
                <tbody>
                @if (count($appointments) > 0)
                    @foreach ($appointments as $appointment)
                        @if(isClientVerified($appointment->client_id))
                         <tr data-entry-id="{{ $appointment->id }}">
                        @else
                          <tr data-entry-id="{{ $appointment->id }}" style="background-color:red ">
                        @endif    
                            @can('appointment_delete')
                                <td></td>
                            @endcan
                            <td width="20%">
                             @if ($appointment->booking_status == 'booking_confirmed' || empty($appointment->booking_status)) 
                                 <!-- <select id="appointment_status" name="appointment_status" class="form-control select2 appointment_status" required rel="{{ $appointment->id  }}">
                                        <option value="">Please select</option>
                                        @foreach($booking_status as $key => $booking_statu)
                                        <option value="{{ $key }}" {{ $appointment->status == $key ? "selected":"" }}>{{ $booking_statu }} </option>
                                        @endforeach
                                    </select> -->{{ $appointment->booking_status }}
                                 @else
                                 {!! $appointment->booking_status !!}
                             @endif    

                            </td>
                            <td>{{ date('d M Y H:i',strtotime($appointment->start_time)) }}</td>
                            <td>{{ date('d M Y H:i',strtotime($appointment->finish_time)) }}</td>
                            <td>€ {{ $appointment->price }}</td>
                            
                            <td>{{ $appointment->client->first_name or '' }} {{ isset($appointment->client) ? $appointment->client->last_name : '' }}</td>
                           
                            <td>{{ isset($appointment->client) ? $appointment->client->phone : '' }}</td>
                            {{-- <td>{{ isset($appointment->client) ? $appointment->client->email : '' }}</td> --}}
                            <td> {{ isset($appointment->location->location_name) ? $appointment->location->location_name : '' }} </td>
                            <td> {{ isset($appointment->service->name) ? $appointment->service->name :'' }}</td>
                            <td>{{ $appointment->employee->first_name or '' }} {{ isset($appointment->employee) ? $appointment->employee->last_name : '' }}</td>
                           
                            
                            {{-- <td>{!! $appointment->comments !!}</td> --}}
                            <td>{!! isset($appointment->room) ? $appointment->room->room_name : '' !!} </td>
                            <td>{!! $appointment->booking_status !!}</td>
                            
                            <td>{!! $appointment->status !!}</td>
                            <td>
                                @can('appointment_view')
                                    <a href="{{ route('admin.appointments.show',[$appointment->id]) }}"
                                       class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                @endcan
                                @can('appointment_edit')
                                @if($appointment->booking_status !='booking_paid' && $appointment->booking_status !='cash_paid'  && $appointment->booking_status !='booking_unpaid')
                                    <!-- <a href="{{ route('admin.opertorappointments.edit',[$appointment->id]) }}"
                                       class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a> -->
                                 @endif
                                @endcan
                                @can('appointment_delete')
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.appointments.destroy', $appointment->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9">@lang('quickadmin.qa_no_entries_in_table')</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
{{--     <div class="modal fade" id="calendarModal" style="overflow:hidden;" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
            <h4 id="modalTitle" class="modal-title"></h4>
        </div>
        <div id="modalBody" class="modal-body">
            
          <h3 class="page-title">@lang('quickadmin.appointments.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.appointments.store']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('client_id', 'Client*', ['class' => 'control-label']) !!}
                    <select id="client_id" name="client_id" class="form-control select21" required>
                        <option value="">Please select</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ (old("client_id") == $client->id ? "selected":"") }}>{{ $client->first_name }} {{ $client->last_name }}</option>
                        @endforeach
                    </select>
                    <p class="help-block"></p>
                    @if($errors->has('client_id'))
                        <p class="help-block">
                            {{ $errors->first('client_id') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('service_id', 'Service*', ['class' => 'control-label']) !!}
                    <select id="service_id" name="service_id" class="form-control " required>
                        <option value="">Please select</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" data-price="{{ $service->price }}" {{ (old("service_id") == $service->id ? "selected":"") }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                    <p class="help-block"></p>
                    @if($errors->has('service_id'))
                        <p class="help-block">
                            {{ $errors->first('service_id') }}
                        </p>
                    @endif
                    <input type="hidden" id="price" value="0">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('date', 'Date*', ['class' => 'control-label']) !!}
                    {!! Form::text('date', old('date'), ['class' => 'form-control date dateshow', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('date'))
                        <p class="help-block">
                            {{ $errors->first('date') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <div class="row" id="start_time" >
                <div class="col-xs-12 form-group">
                    {!! Form::label('start_time', 'Start time*', ['class' => 'control-label']) !!}
                    <div class="form-inline">
                    <select name="starting_hour" id="starting_hour" class="form-control" required style="max-width: 85px;">
                        <option value="-1" selected>Please select</option>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                    </select>
                    :
                    <select name="starting_minute" id="starting_minute" class="form-control" required style="max-width: 85px;">
                        <option value="-1" selected>Please select</option>
                        <option value="00">00</option>
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="45">45</option>
                    </select>
                    </div>
                </div>
            </div>
            <div class="row" id="finish_time" >
                <div class="col-xs-12 form-group">
                    {!! Form::label('finish_time', 'Finish time*', ['class' => 'control-label']) !!}
                    <div class="form-inline">
                    <select name="finish_hour" id="finish_hour" class="form-control" required style="max-width: 85px;">
                        <option value="-1" selected>Please select</option>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                    </select>
                    :
                    <select name="finish_minute" id="finish_minute" class="form-control" required style="max-width: 85px;">
                        <option value="-1" selected>Please select</option>
                        <option value="00">00</option>
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="45">45</option>
                    </select>
                    </div>
                </div>
            </div>
            <hr />
            <div id="results" style="display: none;">
            <p class="total_time"><strong>Total time: <span id="time">0</span> hour(s)</strong></p>
            <p class="total_price"><strong>Total price: $<span id="price_total">0</span></strong></p>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('comments', 'Comments', ['class' => 'control-label']) !!}
                    {!! Form::textarea('comments', old('comments'), ['class' => 'form-control ', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('comments'))
                        <p class="help-block">
                            {{ $errors->first('comments') }}
                        </p>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div> --}}
@stop

@section('javascript')
    <script>
        @can('appointment_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.appointments.mass_destroy') }}';
        @endcan

    </script>
     <script src="{{ url('quickadmin/js') }}/timepicker.js"></script>
      <script>
        $('.datetime').datetimepicker({
            autoclose: true,
            dateFormat: "{{ config('app.date_format_js') }}",
            timeFormat: "HH:mm:ss"
        });
    </script>
    <script>
  $(".appointment_status").on('change',function(){
    var appointment_status = $(this).val();
    var appointment_id = $(this).attr('rel');
    if(appointment_id != 0  && appointment_status!=0 )
     {

if(appointment_status=='booking_unpaid') 
        {
          
          window.location.href="appointments/changeinvoicestatus/"+appointment_id+"/unpaid";
          
        }
        else if(appointment_status=='booking_paid') 
        {
          window.location.href="appointments/changeinvoicestatus/"+appointment_id+"/paid";
         
        } 
         else if(appointment_status=='cash_paid') 
        {
          window.location.href="appointments/changeinvoicestatus/"+appointment_id+"/cash_paid";
         
        }  
        else
        {
              $.ajax({
                    url: '{{ url("admin/update-appointment-status") }}',
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {appointment_id:appointment_id, appointment_status:appointment_status},
                    success:function(option){
                      
                        location.reload();

                    }
                });  
        } 
     }
      
  })
      

    $('.date').datepicker({
        autoclose: true,
        dateFormat: "{{ config('app.date_format_js') }}"
    }).datepicker("setDate", "0");
    </script>
    <script>
        $("#service_id").on("change", function() {
            $("#price").val($('option:selected', this).attr('data-price'));
            var date = $("#date").val();
            var service_id = $("#service_id").val();
            UpdateEmployees(service_id, date);
        });
    
        $("#date").change(function() {
            var service_id = $("#service_id").val();
            var date = $("#date").val();
            UpdateEmployees(service_id, date);
        });
        
        $("#starting_hour, #finish_hour, #starting_minute, #finish_minute").on("change", function () {
            CountPrice();       
        });
        
        $('body').on("change", "input[type=radio][name=employee_id]", function() {
            var employee_id = $(this).val();
            var starting_hour = parseInt($(".starting_hour_"+employee_id).text());
            var starting_minute = $(".starting_minute_"+employee_id).text();
            var finish_hour = starting_hour+1;
            if(finish_hour < 10) {
                finish_hour = "0"+finish_hour;
            }
            if(starting_hour < 10) {
                starting_hour = "0"+starting_hour;
            }
            $('#starting_hour option[value='+starting_hour+']').prop('selected','true');
            $('#starting_minute option[value='+starting_minute+']').prop('selected','true');
            $('#finish_hour option[value='+finish_hour+']').prop('selected','true');
            $('#finish_minute option[value='+starting_minute+']').prop('selected','true');
            $("#start_time, #finish_time").show();
            CountPrice();
        });
        
        function CountPrice() {
            var start_hour = parseInt($("#starting_hour").val());
            var start_minutes = parseInt($("#starting_minute").val());
            var finish_hour = parseInt($("#finish_hour").val());
            var finish_minutes = parseInt($("#finish_minute").val());
            var total_hours = (((finish_hour*60+finish_minutes)-(start_hour*60+start_minutes))/60);
            var price = parseFloat($("#price").val());
            $("#price_total").html(price*total_hours);
            $("#time").html(total_hours);
            if(start_hour != -1 && start_minutes != -1 && finish_hour != -1 && finish_minutes != -1) {
                $("#results").show();
            }
        }
        
        function UpdateEmployees(service_id, date)
        {
            if(service_id != "" && date != "") {
                $.ajax({
                    url: '{{ url("admin/get-employees") }}',
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {service_id:service_id, date:date},
                    success:function(option){
                        //alert(option);
                        $(".employees").remove();
                        $("#date").closest(".row").after(option);
                        $("#start_time, #finish_time").hide();
                        $("#results").hide();
                    }
                });
            }
        }
    </script>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.js'></script>
    <script>
        $(document).ready(function() {
           
            // page is now ready, initialize the calendar...
            $('#calendar').fullCalendar({
                // put your options and callbacks here

                  header: {
      left: 'prev,next today',
      center: 'title',
       right: 'month,basicWeek,basicDay'
    },
     dayClick: function(date, jsEvent, view) {

     if (moment().format('YYYY-MM-DD') === date.format('YYYY-MM-DD') || date.isAfter(moment())) {
        // This allows today and future date
            $(".dateshow").val(date.format())
                //alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
                //alert('Current view: ' + view.name);
           $('#calendarModal').modal();
             //$(this).css('background-color', 'red');
            } else {
                // Else part is for past dates
              }

      },events : [
                        @foreach($appointments as $appointment)
                    {
                        title : '{{ $appointment->client->first_name . ' ' . $appointment->client->last_name }}',
                        start : '{{ $appointment->start_time }}',
                        @if ($appointment->finish_time)
                                end: '{{ $appointment->finish_time }}',
                        @endif
                        url : '{{ route('admin.appointments.edit', $appointment->id) }}'
                    },
                    @endforeach
                ]
            })
             // once ajax done we need to refersh events
           // $('#calendar').fullCalendar( 'refetchEvents' );
        });
    </script>
@endsection