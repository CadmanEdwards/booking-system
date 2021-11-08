@extends('layouts.app')

@section('content')
    <h3 class="page-title"><i class="fa fa-user-circle ifont"></i> @lang('quickadmin.locations.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.locations.store']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
                    {!! Form::text('location_name', old('location_name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div> 
                   <div class="col-xs-6 form-group">
                    {!! Form::label('room_id', 'Room*', ['class' => 'control-label']) !!}
                    {!! Form::select('room_id[]', $rooms, old('room_id'), ['class' => 'form-control js-example-basic-multiple select2', 'required' => '','multiple'=>"multiple"]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('roomn_id'))
                        <p class="help-block">
                            {{ $errors->first('room_id') }}
                        </p>
                    @endif
                </div> 
                 <div class="col-xs-6 form-group">
                    {!! Form::label('location_address', 'Location Address', ['class' => 'control-label']) !!}
                    {!! Form::text('location_address', old('location_address'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    
                </div> 
                <div class="col-xs-6 form-group">
                    {!! Form::label('location_description', 'Route Description*', ['class' => 'control-label']) !!}
                    {!! Form::textarea('location_description',old('location_description'),['class'=>'form-control', 'rows' => 4, 'cols' => 60]) !!}
            <p class="help-block"></p>
                    @if($errors->has('location_description'))
                        <p class="help-block">
                            {{ $errors->first('location_description') }}
                        </p>
                    @endif
                </div> 
            </div>
             

        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

