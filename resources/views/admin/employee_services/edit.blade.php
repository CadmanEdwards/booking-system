@extends('layouts.app')

@section('content')
    <h3 class="page-title"><i class="fa fa-user ifont"></i>  @lang('quickadmin.employees-service.title')</h3>  
      
    {!! Form::model($employeeservices, ['method' => 'PUT', 'route' => ['admin.employees_services.update', $employeeservices->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading bold">
            @lang('quickadmin.qa_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('employee_id', 'Employee*', ['class' => 'control-label']) !!}
                    {!! Form::hidden('employee_id', $employee->id) !!}
                    <div class="form-control" readonly='readonly'>{{ $employee->first_name }} {{ $employee->last_name }}</div>  
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('service_id', 'Service*', ['class' => 'control-label']) !!}
                    {!! Form::select('service_id', $services, old('service_id'), ['class' => 'form-control', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('service_id'))
                        <p class="help-block">
                            {{ $errors->first('service_id') }}
                        </p>
                    @endif
                </div> 
                
            </div>
          {{--   <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('price', 'Price*', ['class' => 'control-label']) !!}
                    {!! Form::text('price', old('price'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('price'))
                        <p class="help-block">
                            {{ $errors->first('price') }}
                        </p>
                    @endif
                </div> 
                <div class="col-xs-6 form-group">
                    {!! Form::label('weekend_price', 'Evening/Weekend Price*', ['class' => 'control-label']) !!}
                    {!! Form::text('weekend_price', old('weekend_price'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('weekend_price'))
                        <p class="help-block">
                            {{ $errors->first('weekend_price') }}
                        </p>
                    @endif
                </div> 
            </div> --}}
             <div class="row">
                 <div class="col-xs-6 form-group">
                    {!! Form::label('discount', 'Discount*', ['class' => 'control-label']) !!}
                    {!! Form::text('discount', old('discount'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('discount'))
                        <p class="help-block">
                            {{ $errors->first('discount') }}
                        </p>
                    @endif
                </div> 
                <div class="col-xs-6 form-group">
                    {!! Form::label('moneybird_username', 'Therapis Title*', ['class' => 'control-label']) !!}
                    {!! Form::text('moneybird_username', old('moneybird_username'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('moneybird_username'))
                        <p class="help-block">
                            {{ $errors->first('moneybird_username') }}
                        </p>
                    @endif
                </div> 
            </div>
            
            
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

