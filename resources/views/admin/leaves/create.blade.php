@extends('layouts.app')

@section('content')
@if ($message = Session::get('error'))
<div class="col-sm-12">
    <div class="note note-danger" role="alert">
        {{ $message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
@endif

<h3 class="page-title"><i class="fa fa-user-circle ifont"></i> @lang('quickadmin.leaves.title')</h3>


{!! Form::open(['method' => 'POST', 'route' => ['admin.employees_leaves.store']]) !!}

<div class="panel panel-default">
    <div class="panel-heading">
        @lang('quickadmin.qa_create')
    </div>

    <div class="panel-body">
        {{--  <div class="panel-heading">
            <div >
                <p style="font-weight: bold;color:red">Spacial Note:</p>
                If you have 1 day leave with different breaks, Suppose you have leave on 20 Jan, Your working hour is 09:00 - 18:00 and you have couple of breaks
                <br>
                1. 11.00-12.00<br>
                2. 14.00-15.00<br>
                so meaning in 1 day you have couple of breaks then you need to create a custom timing for this day, Leave
                will work on either you have full day leave (09:00-18:00) or half day leave (09:00-14:00) or muliple day
                11 jan 14:00 pm to 14 jan (12:00) .
            </div>
        </div> --}}
        <div class="row">
            <div class="col-xs-6 form-group">

                <input type="hidden" name="employee_id" value="{{ $employee_id }}">

                {!! Form::label('name', 'Leave Title*', ['class' => 'control-label']) !!}
                @if(isset($_GET['date']))
                <input type="text"  required name="leave_title" value="Free day" class="form-control">

                @else
                {!! Form::text('leave_title', old('leave_title'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                @endif

                <p class="help-block"></p>
                @if($errors->has('name'))
                <p class="help-block">
                    {{ $errors->first('name') }}
                </p>
                @endif
            </div>
            <div class="col-xs-6 form-group">
                {!! Form::label('Comments', 'Leave Comments', ['class' => 'control-label']) !!}
                {!! Form::text('leave_comment', old('leave_comment'), ['class' => 'form-control', 'placeholder' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('leave_comment'))
                <p class="help-block">
                    {{ $errors->first('leave_comment') }}
                </p>
                @endif
            </div>
            <div class="col-xs-6 form-group">
                <div class="col-xs-6 form-group">
                    {!! Form::label('date', 'Leave From Date*', ['class' => 'control-label']) !!}

                    @if(isset($_GET['date']))
                    <input type="text"  required name="leave_date" value="{{ $_GET['date'] }}" class="form-control date">

                    @else
                    {!! Form::text('leave_date', old('leave_date'), ['class' => 'form-control date ', 'placeholder' => '', 'required' => '','autocomplete'=>'off']) !!}
                    @endif

                    <p class="help-block"></p>
                    @if($errors->has('leave_date'))
                    <p class="help-block">
                        {{ $errors->first('leave_date') }}
                    </p>
                    @endif
                </div>
                {{-- <div class="col-xs-6 form-group">
                    {!! Form::label('Start Time', 'Start Time', ['class' => 'control-label']) !!}
                    {!! Form::time('start_time', old('start_time'), ['class' => 'form-control  ', 'placeholder' => '','autocomplete'=>'off']) !!}


                </div>  --}}
            </div>
            <div class="col-xs-6 form-group">
                <div class="col-xs-6 form-group">
                    {!! Form::label('date', 'Leave To Date*', ['class' => 'control-label']) !!}
                    @if(isset($_GET['date']))
                    <input type="text"  required name="leave_to_date" value="{{ $_GET['date'] }}" class="form-control date">

                    @else
                    {!! Form::text('leave_to_date', old('leave_to_date'), ['class' => 'form-control date timepicker ', 'placeholder' => '', 'required' => '','autocomplete'=>'off']) !!}
                    @endif
                    <p class="help-block"></p>
                    @if($errors->has('leave_to_date'))
                    <p class="help-block">
                        {{ $errors->first('leave_to_date') }}
                    </p>
                    @endif
                </div>
                {{-- <div class="col-xs-6 form-group">
                    {!! Form::label('End Time', 'End Time', ['class' => 'control-label']) !!}
                    {!! Form::time('end_time', old('end_time'), ['class' => 'form-control  ', 'placeholder' => '','autocomplete'=>'off']) !!}



                </div>  --}}
            </div>
        </div>


    </div>
</div>

{!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
{!! Form::close() !!}
@stop

@section('javascript')
@parent
<script>
    $('.date').datepicker({
        autoclose: true,
        dateFormat: "{{ config('app.date_format_js') }}"
    });
</script>
<script src="{{ url('quickadmin/js') }}/timepicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>    <script>
    $('.timepicker').datetimepicker({
        autoclose: true,
        timeFormat: "HH:mm:ss",
        timeOnly: true
    });
</script>

@stop