@extends('layouts.app')

@section('content')
    <h3 class="page-title"><i class="fa fa-users ifont"></i>  Customer WithoutMoney Bird</h3>    
    {!! Form::model($client, ['method' => 'PUT', 'route' => ['admin.clients.updatewithoutmoneybird', $client->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading bold">
            @lang('quickadmin.qa_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('first_name', 'First name*', ['class' => 'control-label']) !!}
                    {!! Form::text('first_name', old('first_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('first_name'))
                        <p class="help-block">
                            {{ $errors->first('first_name') }}
                        </p>
                    @endif
                </div> 
                <div class="col-xs-6 form-group">
                    {!! Form::label('last_name', 'Last name*', ['class' => 'control-label']) !!}
                    {!! Form::text('last_name', old('last_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('last_name'))
                        <p class="help-block">
                            {{ $errors->first('last_name') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('postcode', 'Postcode*', ['class' => 'control-label']) !!}
                    {!! Form::text('postcode', old('postcode'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('postcode'))
                        <p class="help-block">
                            {{ $errors->first('postcode') }}
                        </p>
                    @endif
                </div>  

                 <div class="col-xs-6 form-group">
                    {!! Form::label('house_number', 'House Number', ['class' => 'control-label']) !!}
                    {!! Form::text('house_number', old('house_number'), ['class' => 'form-control', 'placeholder' => '','id'=>'house_number']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('house_number'))
                        <p class="help-block">
                            {{ $errors->first('house_number') }}
                        </p>
                    @endif
                </div>
                
            </div>

           <div class="row">     
            <div class="col-xs-6 form-group">
                    {!! Form::label('address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::text('address', old('address'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address'))
                        <p class="help-block">
                            {{ $errors->first('address') }}
                        </p>
                    @endif
                </div> 
            <div class="col-xs-6 form-group">
                    {!! Form::label('city_name', 'City', ['class' => 'control-label']) !!}
                    {!! Form::text('city_name', old('city_name'), ['class' => 'form-control', 'placeholder' => '','id'=>'city_name']) !!}
            
                    <p class="help-block"></p>
                    @if($errors->has('city_name'))
                        <p class="help-block">
                            {{ $errors->first('city_name') }}
                        </p>
                    @endif
                </div>
               
           </div> 
            <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('email', 'Email*', ['class' => 'control-label']) !!}
                    {!! Form::email('email', old('email'), ['class' => 'form-control input-group', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email'))
                        <p class="help-block">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>

                <div class="col-xs-6 form-group">
                    {!! Form::label('phone', 'Phone*', ['class' => 'control-label']) !!}
                    {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('phone'))
                        <p class="help-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                </div>
                
            </div>
             
            
            <div class="row">
               
                <div class="col-xs-6 form-group">
                    {!! Form::label('Company', 'Company Name', ['class' => 'control-label']) !!}
                    {!! Form::text('company_name', old('company_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('company_name'))
                        <p class="help-block">
                            {{ $errors->first('company_name') }}
                        </p>
                    @endif
                </div>                 
                 <div class="col-xs-6 form-group">
                    {!! Form::label('dob', 'DOB', ['class' => 'control-label']) !!}
                    {!! Form::text('dob', old('dob'), ['class' => 'form-control date', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('dob'))
                        <p class="help-block">
                            {{ $errors->first('dob') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('password', 'Password', ['class' => 'control-label']) !!}
                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '', 'id'=>'pass']) !!}<span class="btn btn-danger" name='password' id='passwordgenerate'>Generate Password</span><span id='passwordshow'></span>
                    <p class="help-block"></p>
                    @if($errors->has('password'))
                        <p class="help-block">
                            {{ $errors->first('password') }}
                        </p>
                    @endif
                </div>
                <!--<div class="col-xs-6 form-group">
                    {!! Form::label('location', 'Location*', ['class' => 'control-label']) !!}
                     {!! Form::Select('location_id',$locations, old('location_id'), ['class' => 'form-control', 'placeholder select2' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('location'))
                        <p class="help-block">
                            {{ $errors->first('location') }}
                        </p>
                    @endif 

                    <select id="location_id" name="location_id" class="form-control " required>
                        <option value="">Please select</option>
                        @foreach($locations as $location)
                        <option rel="{{ strtolower($location->location_name) }}" value="{{ $location->id }}" {{ (old("location_id") == $location->id ? "selected":"") }}>{{ $location->location_name }} </option>
                        @endforeach
                    </select>
                </div>
                ALTER TABLE `clients`     ADD COLUMN `comment_log` LONGTEXT CHARSET utf8 COLLATE utf8_unicode_ci NULL AFTER `comment`;
            -->
                
                    <div class="col-xs-6 form-group">
                        @if(!empty($client->comment_log))
                        <ul class="comment_log_view">
                            {!! $client->comment_log !!}
                        </ul>        
                        @endif
                    
                        {!! Form::label('comment', 'Comment', ['class' => 'control-label']) !!}
                        {!! Form::textarea('comment','',['class'=>'form-control', 'rows' => 5, 'cols' => 20]) !!}
                        <p class="help-block"></p>
                        @if($errors->has('comment'))
                            <p class="help-block">
                                {{ $errors->first('comment') }}
                            </p>
                        @endif
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop
@section('javascript')
    @parent
    <script src="{{ url('quickadmin/js') }}/timepicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script><script>
        $('.datetime').datetimepicker({
            autoclose: true,
            dateFormat: "{{ config('app.date_format_js') }}",
            timeFormat: "HH:mm:ss"
        });
   
        $('.date').datepicker({
            autoclose: true,
            dateFormat: "{{ config('app.date_format_js') }}"
        }).datepicker("setDate", "");
     


        $("#passwordgenerate").on('click',function(){
            
            $.ajax({
                    url: '{{ url("admin/generatepassword") }}',
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {service_id:'1'},
                    success:function(option){
                        //alert(option);
                        $("#pass").val(option);
                        $("#confpass").val(option);
                        $("#passwordshow").html('&nbsp;'+option);

                        
                    }
                });
        })
         $("#house_number").on("blur",function(){

                    $.ajax({
                    url: '{{ url("admin/get-autocomplete") }}',
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {text:$("#postcode").val(),house_number:$(this).val()},
                    success:function(option){
                        console.log(option);
                                var obj = $.parseJSON(option);
                               

                                if(obj.message=='success')
                                {
                                   $("#address").val(obj.address);
                                      
                                  $("#location_id option[rel='"+obj.city+"']").attr("selected","selected");

                                  $('#location_id option[rel="'+obj.city+'"]').prop('selected', true);
                                  $("#city_name").val(obj.city);
                                   //   $("#location").val(obj.address);
                                }
                        
                    }
                });

        })
        $("#postcode").on("blur",function(){
               if($("#house_number").val()!='')
                     {
                         $.ajax({
                            url: '{{ url("admin/get-autocomplete") }}',
                            type: 'GET',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: {text:$(this).val(),house_number:$("#house_number").val()},
                            dataType: 'json',
                            success:function(option){
                                console.log(option);
                                var obj = $.parseJSON(option);
                                 if(obj.message=='success')
                                {
                                   $("#address").val(obj.address);
                                      
                                  $("#location_id option[rel='"+obj.city+"']").attr("selected","selected");

                                  $('#location_id option[rel="'+obj.city+'"]').prop('selected', true);
                                  $("#city_name").val(obj.city);
                                   //   $("#location").val(obj.address);
                                }
                                /*$("#start_time").show();
                                $(".innerHtml").empty();
                                $(".innerHtml").html(option);
        */
                                
                            }
                        });
                     }

        })

    </script>
  
@stop

