@extends('layouts.app')

@section('content')

    <h3 class="page-title"><i class="fa fa-user-circle ifont"></i> @lang('quickadmin.customtiming.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.employees_customtiming.update']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
          Update Custom timing
        </div>


        <div class="panel-body">
            <div class="row">
                @if(Session::has('flash_message'))
                    <div class="alert alert-block alert-warning">
                        <i class=" fa fa-close cool-green "></i>
                        {{ nl2br(Session::get('flash_message')) }}
                    </div>
                @endif

                <input type="hidden" name="id" value="{{ $custom_timing->id }}">

                <div class="col-xs-6 form-group">

                    {!! Form::label('date', 'Date*', ['class' => 'control-label']) !!}


                        {!! Form::text('date', $custom_timing->date, ['class' => 'form-control date', 'placeholder' => '', 'required' => '','autocomplete'=>'off']) !!}





                    <p class="help-block"></p>
                    @if($errors->has('date'))
                        <p class="help-block">
                            {{ $errors->first('date') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('location', 'Location', ['class' => 'control-label']) !!}



                        <select class="form-control" name="location_id">
                            <option value="">Select Location</option>
                            @foreach($locations as $k => $v)
                                <option value="{{ $k }}"
                                        @if($custom_timing->location_id == $k) selected  @endif
                                >{{ $v }}</option>

                            @endforeach
                        </select>




                    <p class="help-block"></p>
                    @if($errors->has('location'))
                        <p class="help-block">
                            {{ $errors->first('location') }}
                        </p>
                    @endif
                </div>

                <div class="col-xs-6 form-group">
                    {!! Form::label('start_time', 'Start time*', ['class' => 'control-label']) !!}


                        {!! Form::time('start_time', $custom_timing->start_time, ['class' => 'form-control ', 'placeholder' => '', 'required' => '','autocomplete'=>'off']) !!}




                    <p class="help-block"></p>
                    @if($errors->has('start_time'))
                        <p class="help-block">
                            {{ $errors->first('start_time') }}
                        </p>
                    @endif
                </div>

                <div class="col-xs-6 form-group">
                    {!! Form::label('finish_time', 'Finish time', ['class' => 'control-label']) !!}



                        {!! Form::time('end_time', $custom_timing->end_time, ['class' => 'form-control ', 'placeholder' => '' , 'required' => '','autocomplete'=>'off']) !!}




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
                        <option @if($custom_timing->timing_type == 'available')  selected  @endif value='available'>Available</option>
                        <option @if($custom_timing->timing_type == 'unavailable')  selected  @endif value='unavailable'>Unavailable</option>
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