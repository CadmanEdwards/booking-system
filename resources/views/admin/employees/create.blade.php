@extends('layouts.app')
<style>
    .select2-container{width:100% !important;}
</style>
@section('content')

    <h3 class="page-title"><i class="fa fa-user ifont"></i>  @lang('quickadmin.employees.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.employees.store'], 'files' => true,'id' => 'form','onsubmit' => 'submitData()']) !!}

    <div class="panel panel-default">
        <div class="panel-heading bold">
            @lang('quickadmin.qa_create')
        </div>

        <ul id="errors_list">

        </ul>

        <div class="panel-body">
            <div class="row">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#general">General</a></li>
                    <li><a data-toggle="tab" href="#roomlocation">Preferred Rooms</a></li>
                    <li><a data-toggle="tab" href="#cost">Working Hours</a></li>

                </ul>
                <div class="tab-content">
                    <div id="general" class="tab-pane fade in active">
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
                            {!! Form::label('email', 'Email*', ['class' => 'control-label']) !!}
                            {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('email'))
                                <p class="help-block">
                                    {{ $errors->first('email') }}
                                </p>
                            @endif
                        </div>

                        <div class="col-xs-6 form-group">
                            {!! Form::label('password', 'Password*', ['class' => 'control-label']) !!}
                            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '', 'required' => '','id' => 'pass']) !!}
                            <span class="btn btn-danger" name='password' id='passwordgenerate'>Generate Password</span><span id='passwordshow'></span>
                            <p class="help-block"></p>
                            @if($errors->has('password'))
                                <p class="help-block">
                                    {{ $errors->first('password') }}
                                </p>
                            @endif
                        </div>
                        <div class="col-xs-6 form-group">
                            {!! Form::label('confirm_password', 'Confirm Password*', ['class' => 'control-label']) !!}
                            {!! Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => '', 'required' => '','id' => 'confpass']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('confirm_password'))
                                <p class="help-block">
                                    {{ $errors->first('confirm_password') }}
                                </p>
                            @endif
                        </div>

                        <div class="col-xs-6 form-group">
                            {!! Form::label('moneybird_username', ' Therapist Title', ['class' => 'control-label']) !!}
                            {!! Form::text('moneybird_username', old('moneybird_username'), ['class' => 'form-control', 'placeholder' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('moneybird_username'))
                                <p class="help-block">
                                    {{ $errors->first('moneybird_username') }}
                                </p>
                            @endif
                        </div>
                        <div class="col-xs-6 form-group">
                            {!! Form::label('moneybird_key', 'Document Id', ['class' => 'control-label']) !!}
                            {!! Form::text('moneybird_key', old('moneybird_key'), ['class' => 'form-control', 'placeholder' => '']) !!}

                            <p class="help-block"></p>
                            @if($errors->has('moneybird_key'))
                                <p class="help-block">
                                    {{ $errors->first('moneybird_key') }}
                                </p>
                            @endif
                        </div>

                        <!-- XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXx -->
                        <div class="col-xs-6 form-group">
                            <div class="col-xs-12 no-padding">
                                {!! Form::label('logo1_file', 'Logo 1', ['class' => 'control-label']) !!}
                                {!! Form::file('logo1_file', ['class' => 'control-label']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('logo1_file'))
                                    <p class="help-block">{{ $errors->first('logo1_file') }}</p>
                                @endif
                            </div>
                            <div class="col-xs-12 no-padding">
                                {!! Form::label('logo2_file', 'Logo 2', ['class' => 'control-label']) !!}
                                {!! Form::file('logo2_file') !!}
                                <p class="help-block"></p>
                                @if($errors->has('logo2_file', ['class' => 'control-label']))
                                    <p class="help-block">{{ $errors->first('logo2_file') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-6 form-group">
                            {!! Form::label('agb_code', 'AGB Code', ['class' => 'control-label']) !!}
                            {!! Form::text('agb_code',old('agb_code'),['class'=>'form-control']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('agb_code'))
                                <p class="help-block">
                                    {{ $errors->first('agb_code') }}
                                </p>
                            @endif
                        </div>
                        <div class="col-xs-6 form-group">
                            {!! Form::label('rcbz_no', 'RBCZ number', ['class' => 'control-label']) !!}
                            {!! Form::text('rcbz_no',old('rcbz_no'),['class'=>'form-control']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('rcbz_no'))
                                <p class="help-block">
                                    {{ $errors->first('rcbz_no') }}
                                </p>
                            @endif
                        </div>

                        <!-- XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX -->


                        <div class="col-xs-6 form-group">
                            {!! Form::label('registration_no', 'Registration no', ['class' => 'control-label']) !!}
                            {{-- {!! Form::text('registration_no',old('registration_no'),['class'=>'form-control']) !!} --}}
                            {!! Form::textarea('registration_no',old('registration_no'),['class'=>'form-control', 'rows' => 5, 'cols' => 20]) !!}

                            <p class="help-block"></p>
                            @if($errors->has('registration_no'))
                                <p class="help-block">
                                    {{ $errors->first('registration_no') }}
                                </p>
                            @endif
                        </div>





                        <div class="col-xs-6 form-group">
                            {!! Form::label('location', 'Location*', ['class' => 'control-label']) !!}
                            {!! Form::Select('location_id',$locations, old('location_id'), ['class' => 'form-control', 'placeholder select2' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('location'))
                                <p class="help-block">
                                    {{ $errors->first('location') }}
                                </p>
                            @endif
                        </div>
                        <div class="col-xs-6 form-group">
                            {!! Form::label('address', 'Address', ['class' => 'control-label']) !!}
                            {!! Form::textarea('address',old('address'),['class'=>'form-control', 'rows' => 5, 'cols' => 20]) !!}
                            <p class="help-block"></p>
                            @if($errors->has('address'))
                                <p class="help-block">
                                    {{ $errors->first('address') }}
                                </p>
                            @endif
                        </div>


                    </div>
                    <div id="roomlocation" class="tab-pane fade">
                    </div>
                    <div id="cost" class="tab-pane fade">
                        <div class="col-sm-12">
                            <p> It will create Schedule for 7 Days From Today i.e ({{ date('d-m-Y') }} To {{ date('d-m-Y', strtotime('+7 days')) }} ) .</p>
                        </div>
                        <div class="col-xs-12 table-responsive ">
                            <table class="table table-bordered table-striped" id="user_table">
                                <thead>
                                <tr>
                                    <th width="10%">Days</th>
                                    <th width="40%">Working Hours</th>
                                    <th width="30%">Repeated</th>
                                    <th width="20%">Location</th>
                                    {{-- <th width="10%">Remove</th> --}}
                                </tr>

                                </thead>
                                <tbody>
                                @foreach ($working_type as $k => $v )
                                    @php  if(is_array($v)) {

                                                                echo "<pre>"; print_r($k); die;
                                                    } @endphp
                                    <tr>
                                        <td>
                                            <input type="hidden" name='day[]' value="{{ $v }}"> {{ $v }}
                                        </td>
                                        <td><div class="row">

                                                <div class="col-xs-5">

                                                    <input type="time" id="booking_pricing_time_from{{$k}}" class="form-control" name="booking_pricing_time_from[{{ $v }}]">
                                                </div>

                                                <div class="col-xs-1">To</div>
                                                <div class="col-xs-5">

                                                    <input type="time" id="booking_pricing_time_to{{$k}}" class="form-control" name="booking_pricing_time_to[{{ $v }}]">

                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-xs-12">
                                                <div class="col-xs-2">
                                                    <input type="checkbox" name="repeated[]" id="{{ $k }}" class="checkbox" >


                                                </div>
                                                <div class="col-xs-10">

                                                        <input type="text" class="form-control hide" style="width:50%" placeholder="Repeated Number" id="repeatedValue{{$k}}" name="repeated_number[{{ $v }}]">



                                                </div>
                                            </div>
                                        </td>
                                        <td class="col-sm-12">
                                            {!! Form::select('location_id', $locations, null, ['class' =>
                                'form-control col-sm-12 select2', 'name' => 'location_id['.$v.']']) !!}

                                        </td>
                                        {{-- <td><button type="button" name="remove" id="" class="btn btn-danger remove">Remove</button></td> --}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>


                        {{-- <div class="col-xs-12 form-group"><button type="button" name="add" id="add" class="btn btn-success">Add Series Type</button></div>   --}}
                    </div>



                    {{--  <div class="row">
                         <div class="col-xs-12 form-group">
                             {!! Form::label('service_id', 'Therapy*', ['class' => 'control-label']) !!}
                             {!! Form::select('service_id', $services, null, ['class' => 'form-control select2', 'required' => '', 'multiple' => '', 'name' => 'services[]']) !!}
                             <p class="help-block"></p>
                             @if($errors->has('service_id'))
                                 <p class="help-block">
                                     {{ $errors->first('service_id') }}
                                 </p>
                             @endif
                         </div>
                     </div> --}}

                </div>
            </div>

            {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-lg btn-danger' , 'id' => 'submitBtn']) !!}
            {!! Form::close() !!}

            @stop
            @section('javascript')
                @parent

                <script>
                    function submitData(){
                        event.preventDefault()
                        console.log($('#form')[0])
                        $('#submitBtn').attr('disabled',true).text('Saving....').val('Saving....')
                        var form = new FormData($('#form')[0])
                        $.ajax({
                            url: '/admin/save-therapists',
                            type: 'POST',
                            processData: false,
                            contentType: false,
                            cache: false,
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: form,
                            success:function(data){
                                alert("Therapist saved Successfully.")
                                $('#submitBtn').removeAttr('disabled').text('Save').val('Save')
                                window.location.reload()
                            },
                            error: function(jqXhr, json, errorThrown){// this are default for ajax errors
                                var errors = jqXhr.responseJSON;
                                $.each(errors['errors'], function (index, value) {
                                    $('#errors_list').append('<li class="list-group-item alert alert-danger">' + value + '</li>');
                                });
                                $('#submitBtn').removeAttr('disabled').text('Save').val('Save')

                            }
                        });

                    }
                    $(document).ready(function(){

                        var count = 1;

                        dynamic_field_location(count);



// dynamic_field(count);
                        $(".checkbox").on('click',function(){
                            //alert($(this).attr('id'));
                            if($(this).prop("checked") == true)
                            {
                                $("#repeatedValue"+$(this).attr('id')).removeClass('hide').slow();
                            }
                            else
                            {
                                $("#repeatedValue"+$(this).attr('id')).addClass('hide').slow();
                            }
                        })

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

                        // page is now ready, initialize the calendar...


                        function dynamic_field_location(number)
                        {
                            html = '<div class="row_'+number+' row"><div class="col-sm-12"><div class="col-xs-5 form-group" rel="'+number+'">';
                            html += '{!! Form::label('location', 'Location*', ['class' => 'control-label ']) !!}{!! Form::Select('emp_location_id[]',$locations, old('location_id'), ['class' => 'form-control location', 'placeholder newselect2' => '']) !!} </div>';
                            html += '<div class="col-xs-5 form-group selectOp_'+number+'" rel="'+number+'">{!! Form::label('room_id', 'Room*', ['class' => 'control-label']) !!}{!! Form::Select('room_id[]', $rooms,old('rooms'), ['class' => 'form-control js-example-basic-multiple newselect2 optionAdd', 'required' => '','multiple'=>"multiple"]) !!} </div>';

                            if(number > 1)
                            {
                                html += '<div class="col-xs-2 form-group"><button type="button" name="remove" id="'+number+'" class="btn btn-danger remove">Remove</button></div></div></div>';
                                $('#roomlocation').append(html);
                            }
                            else
                            {
                                html += '<div class="col-xs-2 form-group"><button type="button" name="add" id="add" class="btn btn-success add">Add</button></div></div></div>';
                                //alert(html);
                                $('#roomlocation').html(html);
                            }
                            $('.newselect2').select2({tags: true});
                            $('.select2').css('width','100%');
                            $(".newselect2").on("select2:select", function (evt) {
                                var element = evt.params.data.element;
                                var $element = $(element);
                                console.log("ASDas")
                                $element.detach();
                                $(this).append($element);
                                $(this).trigger("change");
                            });

                        }

                        function UpdateRoom(locationId,rowCount)
                        {

                            if(locationId != "") {
                                $.ajax({
                                    url: '{{ url("admin/get-rooms-location") }}',
                                    type: 'GET',
                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                    data: {location_id:locationId},
                                    success:function(option){
                                        $('.selectOp_'+rowCount+' > select.optionAdd').html(option.html);
                                    }
                                });
                            }
                        }



                        function dynamic_field(number)
                        {

                            html = '<tr>';
                            html += '<td>{!! Form::select('working_type', $working_type, old('working_type'), ['class' => 'form-control', 'required' => '','name'=>'working_type[]']) !!}</td>';
                            html += '<td><div class="row"> <div class="col-xs-5">{!! Form::time('booking_pricing_time_from', old('booking_pricing_time_from'), ['class' => 'form-control', 'placeholder' => '','name' => 'booking_pricing_time_from[]']) !!}</div><div class="col-xs-1">To</div><div class="col-xs-5">{!! Form::time('booking_pricing_time_to', old('booking_pricing_time_to'), ['class' => 'form-control', 'placeholder' => '','name' => 'booking_pricing_time_to[]']) !!}</div></div></td>';

                            if(number > 1)
                            {
                                html += '<td><button type="button" name="remove" id="" class="btn btn-danger remove">Remove</button></td></tr>';
                                $('tbody').append(html);
                            }
                            else
                            {
                                html += '<td></td></tr>';
                                //alert(html);
                                $('tbody').html(html);
                            }
                        }

                        $(document).on('click', '#add', function(){
                            count++;
                            dynamic_field_location(count);
                        });
                        $(document).on('change', '.location', function(){

                            UpdateRoom($(this).val(),$(this).parent().attr('rel'));
                        });
                        $(document).on('click', '.remove', function(){
                            count--;
                            //(this).closest("tr").remove();
                            var IdVal = $(this).attr('id');
                            $(this).parent().parent().remove();
                        });
                        $("#passwordgenerate").trigger('click');

                    });
                </script>
@stop