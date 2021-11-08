@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" integrity="sha512-ZKX+BvQihRJPA8CROKBhDNvoc2aDMOdAlcm7TUQY+35XYtrd3yh95QOOhsPDQY9QnKE0Wqag9y38OIgEvb88cA==" crossorigin="anonymous" />

    <h3 class="page-title"><i class="fa fa-users ifont"></i>  @lang('quickadmin.clients.title')</h3>
    {!! Form::model($client, ['method' => 'PUT', 'route' => ['admin.clients.update', $client->id] ,'enctype' => 'multipart/form-data']) !!}

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#editcontent" aria-controls="editcontent" role="tab" data-toggle="tab">Edit</a>
        </li>
        <li role="presentation">
            <a href="#editreport" aria-controls="editreport" role="tab" data-toggle="tab">Report</a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="editcontent">
            <div class="panel panel-default">
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
                         <div class="col-xs-6 form-group email">
                            {!! Form::label('email_invoice', 'Invoice Email', ['class' => 'control-label']) !!}
                            {!! Form::email('email_invoice', old('email_invoice'), ['class' => 'form-control input-group ', 'placeholder' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('email_invoice'))
                                <p class="help-block">
                                    {{ $errors->first('email_invoice') }}
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
                        <div class="col-xs-6 form-group">
                            {!! Form::label('Doctor', 'Doctor', ['class' => 'control-label']) !!}
                            {!! Form::Select('doctor_id',$DoctorList, old('doctor_id'), ['class' => 'form-control select2 doctor_id']) !!}
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

                            {!! Form::text('dob', $client->dob, ['class' => 'form-control date', 'placeholder' => '']) !!}
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
                            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '',  'id'=>'pass']) !!}<span class="btn btn-danger" name='password' id='passwordgenerate'>Generate Password</span><span id='passwordshow'></span>
                            <p class="help-block"></p>
                            @if($errors->has('password'))
                                <p class="help-block">
                                    {{ $errors->first('password') }}
                                </p>
                            @endif
                        </div>
                        
                        <div class="col-xs-6 form-group">
                            {!! Form::label('comment', 'Comment', ['class' => 'control-label']) !!}
                            {!! Form::textarea('comment',old('comment'),['class'=>'form-control', 'rows' => 5, 'cols' => 20]) !!}
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
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="editreport">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-xs-12 form-group">
                        {!! Form::label('probleem', 'Probleem', ['class' => 'control-label col-xs-12']) !!}
                        {!! Form::textarea('probleem',old('probleem'),['class'=>'form-control col-xs-12', 'rows' => 5]) !!}
                        <p class="help-block"></p>
                        @if($errors->has('probleem'))
                            <p class="help-block">
                                {{ $errors->first('probleem') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-12 form-group">
                        {!! Form::label('bereiken', 'Bereiken/ doel', ['class' => 'control-label col-xs-12']) !!}
                        {!! Form::textarea('bereiken',old('bereiken'),['class'=>'form-control col-xs-12', 'rows' => 5]) !!}
                        <p class="help-block"></p>
                        @if($errors->has('bereiken'))
                            <p class="help-block">
                                {{ $errors->first('bereiken') }}
                            </p>
                        @endif
                    </div> 
                    <div class="col-xs-12 form-group">
                        {!! Form::label('waarnemingen', 'Waarnemingen', ['class' => 'control-label col-xs-12']) !!}
                        {!! Form::textarea('waarnemingen',old('waarnemingen'),['class'=>'form-control col-xs-12', 'rows' => 5]) !!}
                        <p class="help-block"></p>
                        @if($errors->has('waarnemingen'))
                            <p class="help-block">
                                {{ $errors->first('waarnemingen') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-12 form-group">
                        {!! Form::label('procediagnose', 'Procediagnose', ['class' => 'control-label col-xs-12']) !!}
                        {!! Form::textarea('procediagnose',old('procediagnose'),['class'=>'form-control col-xs-12', 'rows' => 5]) !!}
                        <p class="help-block"></p>
                        @if($errors->has('procediagnose'))
                            <p class="help-block">
                                {{ $errors->first('procediagnose') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-12 form-group">
                        {!! Form::label('fysieke', 'Fysieke/ Psychische Klachten', ['class' => 'control-label col-xs-12']) !!}
                        {!! Form::textarea('fysieke',old('fysieke'),['class'=>'form-control col-xs-12', 'rows' => 5]) !!}
                        <p class="help-block"></p>
                        @if($errors->has('fysieke'))
                            <p class="help-block">
                                {{ $errors->first('fysieke') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-xs-12 form-group">
                        {!! Form::label('samenvatting', 'Samenvatting Sessies', ['class' => 'control-label col-xs-12']) !!}
                        {!! Form::textarea('samenvatting',old('samenvatting'),['class'=>'form-control col-xs-12', 'rows' => 5]) !!}
                        <p class="help-block"></p>
                        @if($errors->has('samenvatting'))
                            <p class="help-block">
                                {{ $errors->first('samenvatting') }}
                            </p>
                        @endif
                    </div>

                    <div class="col-xs-12 form-group">

                            <label class="control-label">Notes</label>

                            <input type="file"  name="notes[]" multiple >
                            @foreach($notes as $key => $val)
                                <div class="col-md-2" style="margin-top: 3%;">
                                    <a href="{{ route('admin.clients.remove.notes',['id' => $val->id]) }}">Remove</a>
                                    <a class="example-image-link" href="/storage/app/public/{{ $val->filename }}" data-lightbox="example-1">
                                        <img src="/storage/app/public/{{ $val->filename }}" class="img img-responsive" width="100" height="100">
                                    </a>

                                </div>
                            @endforeach
                            <p class="help-block"></p>

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
    <script src="{{ url('quickadmin/js') }}/timepicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.js" integrity="sha512-UHlZzRsMRK/ENyZqAJSmp/EwG8K/1X/SzErVgPc0c9pFyhUwUQmoKeEvv9X0uNw8x46FxgIJqlD2opSoH5fjug==" crossorigin="anonymous"></script>
       <script>
        $('.datetime').datetimepicker({
            autoclose: true,
            dateFormat: "{{ config('app.date_format_js') }}",
            timeFormat: "HH:mm:ss"
        });
   
        $('#dob').datepicker({
            autoclose: true,
            dateFormat: "dd-mm-yy"
        }).datepicker();
     


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

