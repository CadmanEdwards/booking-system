@extends('layouts.app')

@section('content')
<h3 class="page-title"><i class="fa fa-user-circle ifont"></i> @lang('quickadmin.customtiming.title')</h3>
{!! Form::open(['method' => 'POST', 'route' => ['admin.employees_customtiming.store']]) !!}

<div class="panel panel-default">
    <div class="panel-heading">
        @lang('quickadmin.qa_create')
    </div>

    <div class="panel-body">
        <div class="row">
            @if(Session::has('flash_message'))
            <div class="alert alert-block alert-warning">
                <i class=" fa fa-close cool-green "></i>
                {{ nl2br(Session::get('flash_message')) }}
            </div>
            @endif
            {{-- <div class="col-xs-6 form-group">
                <input type="hidden" name="employee_id" value="{{ $employee_id }}">
                {!! Form::label('name', 'Leave Title*', ['class' => 'control-label']) !!}
                {!! Form::text('leave_title', old('leave_title'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('name'))
                <p class="help-block">
                    {{ $errors->first('name') }}
                </p>
                @endif
            </div>  --}}
            <div class="col-xs-6 form-group">
                <input type="hidden" name="employee_id" value="{{ $employee_id }}">
                {!! Form::label('date', 'Date*', ['class' => 'control-label']) !!}

                @if(isset($_GET['date']))
                <input type="date"  required name="date" value="{{ date('Y-m-d',strtotime($_GET['date'])) }}" class="form-control date">

                @else
                {!! Form::text('date', old('date'), ['class' => 'form-control date', 'placeholder' => '', 'required' => '','autocomplete'=>'off']) !!}
                @endif




                <p class="help-block"></p>
                @if($errors->has('date'))
                <p class="help-block">
                    {{ $errors->first('date') }}
                </p>
                @endif
            </div>
            <div class="col-xs-6 form-group">
                {!! Form::label('location', 'Location', ['class' => 'control-label']) !!}

                @if(isset($_GET['location_id']))

                <select class="form-control" name="location_id">
                    <option value="">Select Location</option>
                    @foreach($locations as $k => $v)
                    <option value="{{ $k }}"
                            @isset($_GET['location_id']) @if($_GET['location_id'] == $k ) selected  @endif @endisset
                    >{{ $v }}</option>

                    @endforeach
                </select>
                @else
                {!! Form::Select('location_id',$locations, old('location_id'), ['class' => 'form-control', 'placeholder select2' => '']) !!}
                @endif



                <p class="help-block"></p>
                @if($errors->has('location'))
                <p class="help-block">
                    {{ $errors->first('location') }}
                </p>
                @endif
            </div>

            <div class="col-xs-6 form-group">
                {!! Form::label('start_time', 'Start time*', ['class' => 'control-label']) !!}

                @if(isset($_GET['startTime']))

                <input type="time"  required name="start_time" value="{{ date('H:i',strtotime($_GET['startTime'].' -1 hour')) }}" class="form-control">
                @elseif(isset($_GET['start']))
                    <input type="time"  required name="start_time" value="{{ date('H:i',strtotime($_GET['start'].' -2 hour ')) }}" class="form-control">
                @else
                {!! Form::time('start_time', old('start_time'), ['class' => 'form-control ', 'placeholder' => '', 'required' => '','autocomplete'=>'off']) !!}
                @endif



                <p class="help-block"></p>
                @if($errors->has('start_time'))
                <p class="help-block">
                    {{ $errors->first('start_time') }}
                </p>
                @endif
            </div>

            <div class="col-xs-6 form-group">
                {!! Form::label('finish_time', 'Finish time', ['class' => 'control-label']) !!}

                @if(isset($_GET['startTime']))

                <input type="time"  required name="end_time" value="{{ date('H:i',strtotime($_GET['startTime'])) }}" class="form-control">
                @elseif(isset($_GET['start']))
                    @php $endTime = date('H:i',strtotime(date('H:i',strtotime($_GET['start'].' -2 hour ')).' +1 hour'))  @endphp
                    <input type="time"  required name="end_time" value="{{ $endTime }}" class="form-control">

                @else

                {!! Form::time('end_time', old('end_time'), ['class' => 'form-control ', 'placeholder' => '' , 'required' => '','autocomplete'=>'off']) !!}
                @endif



                <p class="help-block"></p>
                @if($errors->has('finish_time'))
                <p class="help-block">
                    {{ $errors->first('finish_time') }}
                </p>
                @endif
            </div>
            <div class="col-xs-6 form-group">
                {!! Form::label('Timing Type', 'Timing Type', ['class' => 'control-label']) !!}
                <select name='timing_type' class="form-control">
                    <option value='available'>Available</option>
                    <option @isset($_GET['location_id'])  selected  @endif value='unavailable'>Unavailable</option>
                </select>
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