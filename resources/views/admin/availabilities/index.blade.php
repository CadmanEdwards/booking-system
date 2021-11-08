@extends('layouts.app')

@section('content')
  <h3 class="page-title"><i class="fa fa-home ifont"></i> Availability</h3>
    <div class="col-sm-12 ">        
       <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('location', 'Location*', ['class' => 'control-label']) !!}
                    {!! Form::Select('location_id',$locations, old('location_id'), ['class' => 'form-control locationId', 'placeholder select2'] ) !!}
                    
                <input type="hidden" name='location_id' id='location_id_latest' value='{{ $location_id }}'>    
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('service', 'Service*', ['class' => 'control-label']) !!}
                    {!! Form::Select('service_id',$services, old('services'), ['class' => 'form-control serviceId', 'placeholder select2'] ) !!}
                    
                <input type="hidden" name='serviceId' id='service_id_latest' value='0'>    
                </div>
                
            </div>

     <div id='calendar'></div>
    </div>
    <div class="row col-md-12">
<div class="panel panel-default">
        <div class="panel-heading">
            Short Info
        </div>

        <div class="panel-body table-responsive">
     
         <div class="col-sm-12 col-md-12">
            <table class="table table-bordered table-striped ">
                <thead>
                    <tr>
                      <th>Therapist Name</th>
                      <th>Short Info</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($getAlltherapist) > 0)
                        @foreach ($getAlltherapist as $emp)
                            @if($emp->small_info!='')
                            <tr data-entry-id="{{ $emp->id }}">
                               <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                                
                                <td>
                                   {{ $emp->small_info }}
                                </td>
                            </tr>
                            @endif
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
   </div>
 </div>
    <div class="row">
   <!--      <div class="col-md-3">
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
                <div class="panel-heading bold">Total Therapist</div>
                <div class="panel-body tile-body">
                    <div class="col-md-6"><i class="fa fa-user"></i></div>
                    <div class="col-md-6"><h2>{{ $therapist }}</h2></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default tile">
                <div class="panel-heading bold">Total Appointments</div>
                <div class="panel-body tile-body">       
                    <div class="col-md-6"><i class="fa fa-shopping-cart"></i></div>
                    <div class="col-md-6"><h2>20</h2></div> 
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default tile">
                <div class="panel-heading bold">Today's Appointments</div>
                <div class="panel-body tile-body">       
                    <div class="col-md-6"><i class="fa fa-shopping-basket"></i></div>
                    <div class="col-md-6"><h2>5</h2></div> 
                </div>
            </div>
        </div> -->
    </div>
<link href='{{ url('quickadmin/fullcal') }}/core/main.css' rel='stylesheet' />
<link href='{{ url('quickadmin/fullcal') }}/daygrid/main.css' rel='stylesheet' />
<link href='{{ url('quickadmin/fullcal') }}/timegrid/main.css' rel='stylesheet' />
<link href='{{ url('quickadmin/fullcal') }}/timeline/main.css' rel='stylesheet' />
<link href='{{ url('quickadmin/fullcal') }}/resource-timeline/main.css' rel='stylesheet' />
<script src='{{ url('quickadmin/fullcal') }}/core/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/interaction/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/daygrid/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/timegrid/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/timeline/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/resource-common/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/resource-timeline/main.js'></script>
 <script src="{{ url('quickadmin/js') }}/popper.min.js"></script>
 <script src="{{ url('quickadmin/js') }}/tooltip.min.js"></script>
{{-- <script src='{{ url('quickadmin/fullcal') }}/resource-daygrid/main.js'></script>
<script src='{{ url('quickadmin/fullcal') }}/resource-timegrid/main.js'></script> --}}
<script>

//alert(newdate)
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
       plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'resourceTimeline' ],
       
      editable: true, // enable draggable events
      aspectRatio: 1.8,
      //scrollTime: '00:00', // undo default 6am scrollTime
      header: {
        left: 'today prev,next',
        center: 'title',
       right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
      },
      defaultView: 'resourceTimelineDay',
       views: {
        resourceTimelineDay: {
          buttonText: 'Day',
          slotDuration: '00:15'
        },
      resourceTimelineWeek: {
        slotDuration: '00:15'
      },
      resourceTimelineMonth: {
        slotDuration: '00:15'
      }
    },
      resourceLabelText: 'Therapist',
 resources: { // you can also specify a plain string like 'json/resources.json'
        url: '{{ url("admin/get-employees-resource") }}',
        failure: function() {
          document.getElementById('script-warning').style.display = 'block';
        }
      },
      resourceRender: function(info) {
          console.log(info.resource.extendedProps.description)
          
              var questionMark = document.createElement('strong');
              questionMark.innerText = ' (?) ';
              //console.log(info.resource.extendedProps.description);
              if(info.resource.extendedProps.description !==null)
               {
                  info.el.querySelector('.fc-cell-text')
                .appendChild(questionMark);
        
               var tooltip = new Tooltip(questionMark, {
                title: info.resource.extendedProps.description,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
              });
               }
            
        },
events: { // you can also specify a plain string like 'json/events-for-resources.json'
         url: '{{ url("admin/get-time-employees-resource") }}',
        
       extraParams: function() { // a function that returns an object
        return {
          location_id: document.getElementById('location_id_latest').value,
          service_id: document.getElementById('service_id_latest').value

        };
      },
  
        failure: function() {
          document.getElementById('script-warning').style.display = 'block';
        },
        eventColor: '#f00'
      }
       
    });
    calendar.render(); 
     $('.serviceId').on('change',function(){
     $('#location_id_latest').val($(".locationId").val());
     $('#service_id_latest').val($(this).val());
     //var eventSource = calendar.getEventSourceById('calendar');
      calendar.refetchEvents();  
      //calendar.refetch();
    })

     $('.locationId').on('change',function(){
     $('#location_id_latest').val($(this).val());
     $('#service_id_latest').val($(".serviceId").val());
     //var eventSource = calendar.getEventSourceById('calendar');
      calendar.refetchEvents();  
      //calendar.refetch();
    })
    
  });

</script>
<style>

  body {
    margin: 0;
    padding: 0;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 1000px;
    margin: 50px auto;
  }
  
.popper,
  .tooltip {
    position: absolute;
    z-index: 9999;
    background: #FFC107;
    color: black;
    width: 250px !important;
    border-radius: 1px !important;
    padding: 2px;
    text-align: left;
    opacity: 1 !important;
  }
  .tooltip-inner{
    max-width:350px !important;
    text-align: left !important;
  }
  .style5 .tooltip {
    background: #1E252B;
    color: #FFFFFF;
    max-width: 100px;
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
