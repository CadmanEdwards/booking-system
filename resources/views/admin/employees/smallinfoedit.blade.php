@extends('layouts.app')
<style type="text/css">
    .select2 select2-container select2-container--default{
        width:400px !important;
    }

</style>
@section('content')
    <h3 class="page-title"><i class="fa fa-user ifont"></i>  @lang('quickadmin.employees.title')</h3>
    
    {!! Form::model($employee, ['method' => 'PUT', 'route' => ['admin.employees.smallinfoupdate', $employee->id]]) !!}
     


    <div class="panel panel-default">
        <div class="panel-heading bold">
            Small Info Edit
        </div>

        <div class="panel-body">
              <div class="row">
              
              <div class="tab-content">
                  
                
               <div class="col-xs-6 form-group">
                    {!! Form::label('small_info', 'Small info', ['class' => 'control-label']) !!}
                    {{-- {!! Form::text('small_info',old('small_info'),['class'=>'form-control']) !!} --}}
                    {!! Form::textarea('small_info',old('small_info'),['class'=>'form-control', 'rows' => 1, 'cols' => 5]) !!}
                    
                    
                  <p class="help-block"></p>
                    @if($errors->has('registration_no'))
                        <p class="help-block">
                            {{ $errors->first('small_info') }}
                        </p>
                    @endif
                </div> 

                 
            </div>
              
        </div>
    </div>
</div>
    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop
@section('javascript')
    @parent
@stop