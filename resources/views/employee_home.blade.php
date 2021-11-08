@extends('layouts.app')
@section('content')

    <div class="row">
  @can('appointment_create')
          <p>
              <a href="{{ route('admin.appointments.create') }}"
                 class="btn btn-success">@lang('quickadmin.qa_add_new') Booking</a>

          </p>
            <p>

                    <a href="{{ route('admin.employees_working_hour.create',getEmployeeId(Auth::user()->id)) }}"
                       class="btn btn-primary">Set Your Working hours</a>


            </p>
            <p>

                <a href="{{ route('admin.leave.leavelist',getEmployeeId(Auth::user()->id)) }}" class="btn btn-primary">Set Your Leaves</a>


            </p>
      @endcan  <div class="col-sm-12">        
       <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('location', 'Location*', ['class' => 'control-label']) !!}
                    {!! Form::Select('location_id',$locations, old('location_id'), ['class' => 'form-control locationId', 'placeholder select2','id'=>'loca'] ) !!}
                    <input type="hidden" name='location_id' id='location_id_latest' value='{{ $location_id }}'/>    
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('date', 'Date*', ['class' => 'control-label']) !!}
                    {!! Form::text('date', old('date'), ['class' => 'form-control date', 'placeholder' => '']) !!}
                  
                </div>
            </div>
   <div id='calendar'></div>
    </div>
     <div class="col-md-3">
            <div class="panel panel-default tile">
                <div class="panel-heading bold">Total Customer</div>
                <div class="panel-body tile-body">
                    <div class="col-md-6"><i class="fa fa-users"></i></div>
                    <div class="col-md-6"><h2>{{ $clinet }}</h2></div>
                </div>
            </div>
        </div>
       
        <div class="col-md-3">
            <div class="panel panel-default tile">
                <div class="panel-heading bold">Total Appointments</div>
                <div class="panel-body tile-body">       
                    <div class="col-md-6"><i class="fa fa-shopping-cart"></i></div>
                    <div class="col-md-6"><h2>{{ $appointments }}</h2></div> 
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default tile">
                <div class="panel-heading bold">Today's Appointments</div>
                <div class="panel-body tile-body">       
                    <div class="col-md-6"><i class="fa fa-shopping-basket"></i></div>
                    <div class="col-md-6"><h2>{{ $today_appointment }}</h2></div> 
                </div>
            </div>
        </div>
<link href='{{ url('quickadmin/fullcal') }}/core/main.css' rel='stylesheet' />
<link href='{{ url('quickadmin/fullcal') }}/daygrid/main.css' rel='stylesheet' />
<link href='{{ url('quickadmin/fullcal') }}/timegrid/main.css' rel='stylesheet' />
<link href='{{ url('quickadmin/fullcal') }}/timeline/main.css' rel='stylesheet' />
<link href='{{ url('quickadmin/fullcal') }}/resource-timeline/main.css' rel='stylesheet' />
@section('javascript')
    @parent
<script src='{{ url('quickadmin/fullcal') }}/core/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/interaction/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/daygrid/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/timegrid/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/timeline/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/resource-common/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/resource-daygrid/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/resource-timegrid/main.js'></script>
 <script src="{{ url('quickadmin/js') }}/dateTimePicker.js"></script>

 <script src="{{ url('quickadmin/js') }}/popper.min.js"></script>
 <script src="{{ url('quickadmin/js') }}/tooltip.min.js"></script>
{{-- <script src='{{ url('quickadmin/fullcal') }}/resource-daygrid/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/resource-timegrid/main.js'></script> --}}
<script>
 
  var time;
  var isWorkinghour = false;
   $('.date').datepicker({
    autoclose: true,
    dateFormat: "{{ config('app.date_format_js') }}"
  }).datepicker("setDate", "0");


  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
   var location_id =  document.getElementById('location_id_latest').value;
   
    var calendar = new FullCalendar.Calendar(calendarEl, {
      plugins: [ 'resourceTimeGrid', 'dayGrid','timeGrid','interaction' ],
       schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
      height: 'parent',
      timeZone: 'UTC',
     /* defaultView: 'resourceTimeGridDay',
     defaultView: 'resourceTimeGridThirtyDay',*/
     defaultView: 'resourceTimeGridDay',
     header: {
      left: 'prev,next',
      center: 'title',
      right: 'resourceTimeGridDay,resourceTimeGridFourDay,resourceTimeGridSevanDay'
    },
        
        titleFormat: {   month: 'long',
          year: 'numeric',
          day: 'numeric',
          weekday: 'long'},
    views: {
      resourceTimeGridSevanDay: {
        type: 'resourceTimeGrid',
        duration: { days: 7 },
        buttonText: 'Week'
      },
      resourceTimeGridFourDay: {
        type: 'resourceTimeGrid',
        duration: { days: 4 },
        buttonText: '4 Days'
      }
    },
      resources: {!! $rooms !!},

      events: { url:'{{ url("admin/get-employees-appointments") }}',
       startParam  : 'Dates',
      extraParams: function() { // a function that returns an object
        return {
          location_id: document.getElementById('location_id_latest').value
         };
      },
     },
        dateClick: function(info) {

            time =  info.dateStr
            setTimeout(function () {
                if (!isWorkinghour) {
                    window.location.href = '/admin/employees_customtiming/create/{{ Auth::user()->id }}?date=' + time + '&start=' + time + '&location_id=' + $("#loca").val()
                }
            },1000)

        },

        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if(info.event.extendedProps.description == 'Working hours'){
                isWorkinghour = true
            }
            setTimeout(function (){
                if (info.event.url) {
                    if(info.event.title != 'Available'){

                        if(info.event.loc){
                            window.location.href = info.event.url+"&selectedTime="+time+"&location_id="+info.event.loc
                        }else{
                            window.location.href = info.event.url+"&selectedTime="+time+"&location_id="+$("#loca").val()
                        }

                    }else{
                        //    console.log(info.event.url)
                        if(info.event.loc){
                            window.location.href = info.event.url+"&selectedTime="+time+"&location_id="+info.event.loc
                        }else{
                            window.location.href = info.event.url+"&selectedTime="+time+"&location_id="+$("#loca").val()
                        }

                    }

                    return false;
                }
            },1000)
        },

        eventRender: function(info) {
            //console.log(info);
            //   $(info.el).parent().children().last().remove();
            // console.log(info.view);
            // console.log(info.event);
            // console.log(info.event.extendedProps.description);
            var tooltip = new Tooltip(info.el, {
                title: info.event.extendedProps.description,
                placement: 'top',
                trigger: 'hover',
                container: 'body',
                html: true,
            });
        }
   
    });

    calendar.render();
    var calendarEve = calendar.getEventSourceById('calendar');


    $('.locationId').on('change',function(){
     $('#location_id_latest').val($(this).val());
     //var eventSource = calendar.getEventSourceById('calendar');
      calendar.refetchEvents();  
      //calendar.refetch();
    })

    $('.date').on('change',function(){
     //$('#location_id_latest').val($(this).val());
     //var eventSource = calendar.getEventSourceById('calendar');
      //calendar.refetchEvents();  
      calendar.gotoDate( $(this).val())
      calendar.changeView('resourceTimeGridDay');

      //calendar.fullCalendar('gotoDate', $(this).val());
      //calendar.refetch();
    })

  
  });

</script>
@stop
<style>

  body {
    margin: 0;
    padding: 0;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 1300px;
    margin: 20px auto;
  }

  /*
  i wish this required CSS was better documented :(
  https://github.com/FezVrasta/popper.js/issues/674
  derived from this CSS on this page: https://popper.js.org/tooltip-examples.html
  */

  .popper,
  .tooltip {
    position: absolute;
    z-index: 9999;
    background: #FFC107;
    color: black;
    width: 150px;
    border-radius: 3px;
    box-shadow: 0 0 2px rgba(0,0,0,0.5);
    padding: 10px;
    text-align: center;
    opacity: 1 !important;
  }
  .style5 .tooltip {
    background: #1E252B;
    color: #FFFFFF;
    max-width: 200px;
    width: auto;
    font-size: .8rem;
    padding: .5em 1em;
  }
  .popper .popper__arrow,
  .tooltip .tooltip-arrow {
    width: 0;
    height: 0;
    border-style: solid;
    position: absolute;
    margin: 5px;
  }

  .tooltip .tooltip-arrow,
  .popper .popper__arrow {
    border-color: #FFC107;
  }
  .style5 .tooltip .tooltip-arrow {
    border-color: #1E252B;
  }
  .popper[x-placement^="top"],
  .tooltip[x-placement^="top"] {
    margin-bottom: 5px;
  }
  .popper[x-placement^="top"] .popper__arrow,
  .tooltip[x-placement^="top"] .tooltip-arrow {
    border-width: 5px 5px 0 5px;
    border-left-color: transparent;
    border-right-color: transparent;
    border-bottom-color: transparent;
    bottom: -5px;
    left: calc(50% - 5px);
    margin-top: 0;
    margin-bottom: 0;
  }
  .popper[x-placement^="bottom"],
  .tooltip[x-placement^="bottom"] {
    margin-top: 5px;
  }
  .tooltip[x-placement^="bottom"] .tooltip-arrow,
  .popper[x-placement^="bottom"] .popper__arrow {
    border-width: 0 5px 5px 5px;
    border-left-color: transparent;
    border-right-color: transparent;
    border-top-color: transparent;
    top: -5px;
    left: calc(50% - 5px);
    margin-top: 0;
    margin-bottom: 0;
  }
  .tooltip[x-placement^="right"],
  .popper[x-placement^="right"] {
    margin-left: 5px;
  }
  .popper[x-placement^="right"] .popper__arrow,
  .tooltip[x-placement^="right"] .tooltip-arrow {
    border-width: 5px 5px 5px 0;
    border-left-color: transparent;
    border-top-color: transparent;
    border-bottom-color: transparent;
    left: -5px;
    top: calc(50% - 5px);
    margin-left: 0;
    margin-right: 0;
  }
  .popper[x-placement^="left"],
  .tooltip[x-placement^="left"] {
    margin-right: 5px;
  }
  .popper[x-placement^="left"] .popper__arrow,
  .tooltip[x-placement^="left"] .tooltip-arrow {
    border-width: 5px 0 5px 5px;
    border-top-color: transparent;
    border-right-color: transparent;
    border-bottom-color: transparent;
    right: -5px;
    top: calc(50% - 5px);
    margin-left: 0;
    margin-right: 0;
  }

</style>

@endsection
