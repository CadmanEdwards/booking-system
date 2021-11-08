@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.appointments.title')</h3>
    @can('appointment_create')
        <p>
            <a href="{{ route('admin.appointments.create') }}"
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
            <table class="table-bordered table-striped">
     <tr>
       <td>
         <input type='text' id='searchByName' placeholder='Enter name'>
       </td>
       <td id="Month" style="display:none">
         <select id='searchByMonth' name="select_month">
           <option value=''>-- Select Month--</option>
           <option value='1'>January</option>
           <option value='2'>February</option>
           <option value='3'>March</option>
           <option value='4'>April</option>
           <option value='5'>May</option>
            <option value='6'>June</option>
            <option value='7'>July</option>
            <option value='8'>August</option>
            <option value='9'>September</option>
            <option value='10'>October</option>
            <option value='11'>November</option>
            <option value='12'>December</option>
          
         </select>
       </td>

       <td id="SearchYear" style="display:none">
         <select id='SearchByYear' name="select_year">
           <option value=''>-- Select Year--</option>
           
           @foreach ($yearlist as $yeardata)
            <option value='{{ $yeardata }}' @if($yeardata ==@date("Y")) selected="selected"  @endif>{{ $yeardata }}</option> 
          @endforeach
         </select>
       </td>

       <td> 
         <select id='searchByGender' name="select_type">
           <option value=''>-- Select Type--</option>
           <option value='by_email'>By Email</option>
           <option value='by_customer_name'>By Customer Name</option>
           <option value='by_therapy_name'>By Therapist Name</option>
           <option value='by_therapist_email'>By Therapist EmailId</option>
           <option value='by_therapist_name_month'>By Therapist Name & Month Year</option>
         </select>
       </td>
         <td id="status" >
             <select id='searchByStatus' name="select_status">
                 <option value=''>-- Select Status--</option>
                 <option value='paid_pin'>Paid Pin</option>
                 <option value='bank_paid'>Bank Paid</option>
                 <option value='cash_paid'>Cash Paid</option>
                 <option value='booking_unpaid'>Booking unpaid</option>
                 <option value='booking_confirmed'>Booking Confirmed</option>
                 <option value='booking_pending'>Booking pending</option>
                 <option value='mollie_paid'>Mollie Paid</option>
             </select>
         </td>
     </tr>
   </table>
   <br/>
            <table class="table table-bordered table-striped {{ count($appointments) > 0 ? 'datatable1' : '' }} @can('appointment_delete') dt-select @endcan">
                <thead>
                <tr>
                    <th style="text-align:center;">
                      @can('appointment_delete')
                      <input type="checkbox" id="select-all" name="select_all"/>
                      @endcan
                    </th>
                    
                    <th>Status</th>
                    <th>@lang('quickadmin.appointments.fields.start-time')</th>
                    <th>@lang('quickadmin.appointments.fields.finish-time')</th>
                    <th>Price</th>
                    <th id="thera_pr" style="display: none">Therapist Price</th>
                    <th>Customer Name</th>
                    <th>@lang('quickadmin.clients.fields.phone')</th>
                    <th>Location</th>
                    {{-- <th>@lang('quickadmin.clients.fields.email')</th> --}}
                    <th>Therapy Name</th>
                    <th>Therapist Name</th>
                    <th>Room No</th>
                    <th>Created By</th>
                    <th>Client Email Verified</th> 
                    
                    {{-- <th>@lang('quickadmin.appointments.fields.comments')</th> --}}
                    <th>@lang('quickadmin.appointments.fields.moneybird_status')</th>
                    <th>Booking status</th>
                    
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
             
                </tbody>
                <tfoot id="foot_tb" style="display: none">
                <tr>
                    <td colspan="5"><span style="float: right">Total Therapist Price</span></td>
                    <td ><span id="total_therapist_price">€ 0</span></td>

                </tr>
                </tfoot>
            </table>
        </div>
    </div>

@stop

@section('javascript')
    <script>
          var handleCheckboxes = function (html, rowIndex, colIndex, cellNode) {
                var $cellNode = $(cellNode);
                var $check = $cellNode.find(':checked');
                return ($check.length) ? ($check.val() == 1 ? 'Yes' : 'No') : $cellNode.text();
            };
           window.route_all_data = '{{ url("admin/get-appointment-datatable") }}';
           window.hasAppointment=1;
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
    //$('body').(".appointment_status").on('change',function(){
    $('body').on("change", ".appointment_status", function() {
    var appointment_status = $(this).val();
    var appointment_id = $(this).attr('rel');
    
    if(appointment_id != 0  && appointment_status!=0 )
    {
      if(appointment_status=='booking_unpaid') 
        {
          window.location.href="appointments/changeinvoicestatus/"+appointment_id+"/booking_unpaid";
        }
        else if(appointment_status=='booking_paid' || appointment_status=='booking_paid_pin') 
        {
          window.location.href="appointments/changeinvoicestatus/"+appointment_id+"/booking_paid_pin";
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

                        @php
                        $first_name = "";
                        $last_name = "";
                            if(isset($appointment->client->first_name)){
                                $first_name = $appointment->client->first_name;
                            }
                            if(isset($appointment->client->last_name)){
                                $last_name = $appointment->client->last_name;
                            }
                            $title = $first_name." ".$last_name @endphp

                        title : '{{ $title  }}',
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