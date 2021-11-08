@extends('layouts.app')
@section('content')

<div class="row">
    @can('appointment_create')
    <p>
        <a href="{{ route('admin.appointments.create') }}"
           class="btn btn-success pull-right">@lang('quickadmin.qa_add_new') Booking</a>

    </p>
    <p>
        <a id="therapist_work_hour" style="display: none" href="#" class="btn btn-primary">Set Working hours for this user</a>

<!--        <a id="admin_working_hour" href="{{ route('admin.employees.edit',[\Illuminate\Support\Facades\Auth::user()->id]) }}" class="btn btn-primary">Set Your Working hours</a>-->


    </p>
    @endcan

    <div class="col-sm-12">
        <div class="row">


            @if(\Illuminate\Support\Facades\Auth::user()->id == 1)
            <div class="col-xs-6 form-group">

                {!! Form::label('Therapist', 'Therapist*', ['class' => 'control-label']) !!}

                <select class="form-control" name="therapist_id"  id="thera">
                    <option value="0">Select Therapist</option>
                    @foreach($therapist_arr as $k => $v)
                    <option value="{{ $v->id }}"
                            @isset($_GET['therapist_id']) @if($_GET['therapist_id'] == $v->id ) selected  @endif @endisset
                    >{{ $v->first_name.' '.$v->last_name }}</option>

                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-xs-6 form-group">
                {!! Form::label('location', 'Location*', ['class' => 'control-label']) !!}
                {!! Form::Select('location_id',$locations, old('location_id'), ['class' => 'form-control locationId', 'placeholder select2','id' => 'loca','name' => 'location_id'] ) !!}
                <input type="hidden" name='location_id' id='location_id_latest' value='{{ $location_id }}'/>
            </div>
            <div class="col-xs-6 form-group">
                {!! Form::label('date', 'Date*', ['class' => 'control-label']) !!}
                {!! Form::text('date', old('date'), ['class' => 'form-control date', 'placeholder' => '','id' => 'calender_date','name' => 'calendar_date']) !!}

            </div>

        </div>
        <div id='calendar'></div>
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


    $('.date').datepicker({
        autoclose: true,
        dateFormat: "{{ config('app.date_format_js') }}"
    }).datepicker("setDate", "0");



</script>
@stop
<style>

    body {
        margin: 0;
        padding: 0;
        font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
        font-size: 14px;
    }
    .fc-scroller.fc-time-grid-container {
        height: auto !important;
    }
    #calendar {
        /*max-width: 1300px;*/
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
        width: 350px !important;
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
