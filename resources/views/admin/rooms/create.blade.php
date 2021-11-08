@extends('layouts.app')

@section('content')
    <h3 class="page-title"><i class="fa fa-user-circle ifont"></i> @lang('quickadmin.rooms.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.rooms.store']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                 <ul class="nav nav-tabs">
                 <li class="active"><a data-toggle="tab" href="#general">General</a></li>
                 <li><a data-toggle="tab" href="#cost">Availabilities</a></li>
                 
              </ul>
              <div class="tab-content">
                <div id="general" class="tab-pane fade in active">
                <div class="col-xs-6 form-group">
                    {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
                    {!! Form::text('room_name', old('room_name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
               </div>
               <div id="cost" class="tab-pane fade">
                    <div class="col-sm-12">
                     {{--  <p> It will create Schedule for 7 Days From Today i.e ({{ date('d-m-Y') }} To {{ date('d-m-Y', strtotime('+7 days')) }} ) .</p> --}}
                    </div>
                    <div class="col-xs-12 table-responsive ">
                                <table class="table table-bordered table-striped" id="user_table">
                                       <thead>
                                        <tr>
                                            <th width="10%">Days</th>
                                            <th width="40%">Availabilities</th>
                                            {{-- <th width="30%">Repeated</th>
                                            <th width="20%">Location</th>
                                            {{-- <th width="10%">Remove</th> --}}
                                        </tr>

                                       </thead>
                                       <tbody>
                                        @foreach ($working_type as $key => $values )
                                           <tr>
                                            <td>
                                                <input type="hidden" name='day[]' value="{{ $values }}"> {{ $values }}
                                            </td>
                                            <td><div class="row"> <div class="col-xs-5">{!! Form::time('booking_pricing_time_from', old('booking_pricing_time_from'), ['class' => 'form-control', 'placeholder' => '','name' => 'booking_pricing_time_from['.$values.']']) !!}</div><div class="col-xs-1">To</div>
                                                <div class="col-xs-5">
                                                   {!! Form::time('booking_pricing_time_to', old('booking_pricing_time_to'), ['class' => 'form-control', 'placeholder' => '','name' => 'booking_pricing_time_to['.$values.']']) !!}
                                                </div></div>
                                            </td>
                                            {{-- <td>
                                                <div class="col-xs-12">
                                                <div class="col-xs-2">{!! Form::checkbox('repeated[]', old('repeated'),null,array('id' => $key,'class'=>'checkbox')) !!}</div>
                                                <div class="col-xs-10">

                                                {!! Form::text('repeated_number', old('repeated_number'),['class' => 'form-control hide','style'=>'width:50%','placeholder' => '','placeholder'=>' Repeated Number','name' => 'repeated_number['.$values.']','id'=>'repeatedValue'.$key]) !!}</div>
                                                 </div>
                                            </td>
                                            <td class="col-sm-12">
                                                {!! Form::select('location_id', $locations, null, ['class' => 'form-control col-sm-12 select2', 'required' => '', 'name' => 'location_id['.$values.']']) !!}

                                            </td>  --}}
                                            {{-- <td><button type="button" name="remove" id="" class="btn btn-danger remove">Remove</button></td> --}}
                                           </tr>
                                        @endforeach
                                       </tbody>
                                </table>
                            </div>
                        
                       
                             {{-- <div class="col-xs-12 form-group"><button type="button" name="add" id="add" class="btn btn-success">Add Series Type</button></div>   --}}
                    </div> 

              </div>   
                
                
            </div>
             

        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

