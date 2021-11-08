@extends('layouts.app')
 
@section('content')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <style>
        .swiper-container {
            width: 100%;
            height: 100%;
        }
        .swiper-wrapper{
            padding-bottom: 30px;
        }

        .swiper-container-horizontal>.swiper-pagination-bullets, .swiper-pagination-custom, .swiper-pagination-fraction{
            bottom: 0px !important;
        }
        .swiper-container {
            width: 100%;
            height: 100%;
        }

        .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #fff;

            /* Center slide text vertically */
            display: -webkit-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
        }

        .swiper-pagination-bullet {
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            font-size: 12px;
            color: #000;
            opacity: 1;
            background: rgba(0, 0, 0, 0.2);
        }

        .swiper-pagination-bullet-active {
            color: #fff;
            background: #007aff;
        }
    </style>
    <div class="row">
    <div class="col-md-6"><h3 class="page-title"><i class="fa fa-users ifont"></i>  @lang('quickadmin.clients.title')</h3></div>
    <div class="col-md-6 tright">

    @can('client_create')
    <p>
        <a href="{{ route('admin.clients.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
    </p>
        @if(Auth::user()->id == 1)
        <p>
            <a href="{{ route('admin.clients.view.excel') }}" class="btn btn-success">Add via excel sheet</a>

        </p>
         @endif

        @if(Auth::user()->id == 1)
        <p>
            <a href="{{ route('admin.clients.download.excel') }}" class="btn btn-success">Download Excel</a>
        </p>
        @endif
    @endcan

    </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading bold">
            @lang('quickadmin.qa_list')
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped datatable1">
                <thead>
                    <tr>
                        <th style="text-align:center;" width="5%">
                        @can('client_delete')
                            <input type="checkbox" id="select-all" name="select_all" />
                        @endcan
                        </th>
                       <th width="10%">@lang('quickadmin.clients.fields.first-name')</th>
                       <th width="10%">@lang('quickadmin.clients.fields.last-name')</th>
                       <th width="10%">@lang('quickadmin.clients.fields.phone')</th>
                       <th width="15%">@lang('quickadmin.clients.fields.email')</th>
                       <th width="10%">Created Date </th>
                       <th width="15%">Comment </th>
                       <th width="10%">Parent Name</th>
                        <!-- <th>Money Bird Contact Id</th> -->
                       <th width="15%">&nbsp;</th>
                    </tr>
                </thead>
                
                <tbody>
                  
                </tbody> 
            </table>
        </div>
    </div>

    <div class="modal fade" id="calendarModal" style="overflow:hidden;" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                    <h4 id="modalTitle" class="modal-title"></h4>
                </div>
                <div id="modalBody" class="modal-body">
                    {!! Form::open(['method' => 'POST', 'route' => ['admin.clients.store']]) !!}

                    <div class="panel panel-default">
                        <div class="row">
                            <div class="col-xs-6 form-group">
                                {!! Form::label('first_name', 'First name*', ['class' => 'control-label']) !!}
                                {!! Form::text('first_name', '', ['class' => 'form-control', 'placeholder' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('first_name'))
                                    <p class="help-block">
                                        {{ $errors->first('first_name') }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-xs-6 form-group">
                                {!! Form::label('last_name', 'Last name*', ['class' => 'control-label']) !!}
                                {!! Form::text('last_name', '', ['class' => 'form-control', 'placeholder' => '']) !!}
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
                                {!! Form::text('postcode', '', ['class' => 'form-control', 'placeholder' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('postcode'))
                                    <p class="help-block">
                                        {{ $errors->first('postcode') }}
                                    </p>
                                @endif
                            </div>
                        <div class="col-xs-6 form-group">
                                {!! Form::label('house_number', 'House Number', ['class' => 'control-label']) !!}
                                {!! Form::text('house_number', '', ['class' => 'form-control', 'placeholder' => '']) !!}
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
                                {!! Form::text('address', '', ['class' => 'form-control', 'placeholder' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('address'))
                                    <p class="help-block">
                                        {{ $errors->first('address') }}
                                    </p>
                                @endif
                            </div> 
                            <div class="col-xs-6 form-group">
                                {!! Form::label('city_name', 'City', ['class' => 'control-label']) !!}
                                {!! Form::text('city_name', '', ['class' => 'form-control', 'placeholder' => '','id'=>'city_name']) !!}
                        
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
                                 {!! Form::label('Parent', 'Parent', ['class' => 'control-label']) !!}
                                {!! Form::Select('parent_id',$parentClient, '', ['class' => 'form-control parent_id','id'=>'parent_id', 'placeholder ' => '']) !!}

                                
                            </div>

                             <div class="col-xs-6 form-group email">
                                {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                                {!! Form::email('email', '', ['class' => 'form-control input-group ', 'placeholder' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('email'))
                                    <p class="help-block">
                                        {{ $errors->first('email') }}
                                    </p>
                                @endif
                            </div> 

                            
                            
                        </div>
                        <div class="row">
                            <div class="col-xs-6 form-group">
                                {!! Form::label('Company', 'Company Name', ['class' => 'control-label']) !!}
                                {!! Form::text('company_name', '', ['class' => 'form-control', 'placeholder' => '','company_name'=>'company_name']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('company_name'))
                                    <p class="help-block">
                                        {{ $errors->first('company_name') }}
                                    </p>
                                @endif
                            </div>
                        

                            <div class="col-xs-6 form-group">
                                {!! Form::label('phone', 'Phone*', ['class' => 'control-label']) !!}
                                {!! Form::text('phone', '', ['class' => 'form-control', 'placeholder' => '']) !!}
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
                                {!! Form::label('password', 'Password*', ['class' => 'control-label']) !!}
                                {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '', 'required' => '','id'=>'pass']) !!}<span class="btn btn-danger" name='password' id='passwordgenerate'>Generate Password</span><span id='passwordshow'></span>
                                <p class="help-block"></p>
                                @if($errors->has('password'))
                                    <p class="help-block">
                                        {{ $errors->first('password') }}
                                    </p>
                                @endif
                            </div>
                         
                            <div class="col-xs-6 form-group">
                                {!! Form::label('confirm_password', 'Confirm Password*', ['class' => 'control-label']) !!}
                                {!! Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => '', 'required' => '','id'=>'confpass']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('confirm_password'))
                                    <p class="help-block">
                                        {{ $errors->first('confirm_password') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 form-group">
                                {!! Form::label('dob', 'DOB', ['class' => 'control-label']) !!}
                                {!! Form::text('dob', '', ['class' => 'form-control date', 'placeholder' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('dob'))
                                    <p class="help-block">
                                        {{ $errors->first('dob') }}
                                    </p>
                                @endif
                            </div>
                           
                            
                             <div class="col-xs-6 form-group">
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
                    <div class="row">
                        <div class="col-xs-6">
                            {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
                        </div>
                        <div class="col-xs-6 text-right">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                    
                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="emailModal" style="overflow:hidden;" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                    <h4 id="modalTitle" class="modal-title"></h4>
                </div>
                <div  class="modal-body">
                    <div class="swiper-container">
                        <div class="swiper-wrapper" id="emailModalBody">


                        </div>
                        <!-- Add Pagination -->
                        <div class="swiper-pagination"></div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="sendMail" class="btn btn-primary">Send Mail(s)</button>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    @parent
    <script src="{{ url('quickadmin/js') }}/timepicker.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script> -->
    <script>


        function loadcolydiew(id)
        {
            $('#first_name').val(''); 
            $('#last_name').val(''); 
            $('#postcode').val(''); 
            $('#house_number').val(''); 
            $('#address').val(''); 
            $('#city_name').val(''); 
            $('#email').val(''); 
            $('#phone').val(''); 
            $('#pass').val(''); 
            $('#confpass').val(''); 
            $('#dob').val(''); 
            $('#comment').val(''); 
            $('#parent_id').val(''); 
            $('#company_name').val(''); 
            $('.email').show();


            $.ajax({
                url: '{{ url("admin/client_copy") }}/'+id,
                type: 'GET',
                dataType: 'json',  //3
                success:function(data){
                    if(data.message=='success')
                    {
                        client = data.client;
                        $('#first_name').val(client.first_name); 
                        $('#last_name').val(client.last_name); 
                        $('#postcode').val(client.postcode); 
                        $('#house_number').val(client.house_number); 
                        $('#address').val(client.address); 
                        $('#city_name').val(client.city_name); 
                        $('#email').val(client.email); 
                        $('#phone').val(client.phone); 
                        $('#pass').val(''); 
                        $('#confpass').val(''); 

                        $('#dob').val(client.dob); 
                        $('#comment').val(client.comment); 

                        $('#parent_id').val(client.parent_id); 
                        $('#company_name').val(client.company_name); 

                         if($('#parent_id').val() > 0)
                             { $('.email').hide();}
                          else
                             {$('.email').show();}
                    }
                    else
                    {
                        alert(data.message);
                    }
                    
                }
            });  
        }



        var handleCheckboxes = function (html, rowIndex, colIndex, cellNode) {
        var $cellNode = $(cellNode);
        var $check = $cellNode.find(':checked');
        return ($check.length) ? ($check.val() == 1 ? 'Yes' : 'No') : $cellNode.text();
    };
        window.route_all_data = '{{ url("admin/get-client-datatable") }}';
        window.hasClients=1;
        @can('client_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.clients.mass_destroy') }}';
        @endcan
       
        window.route_mass_send_email = '{{ route('admin.clients_mass_email_send') }}';
        window.email_templates = {!! $emailTemplate !!};

     

           $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
  //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
   $(document).ready(function(){
           $("#passwordgenerate").trigger('click');
           $('.parent_id').on('change',function(){
           
              if($(this).val() > 0)
                 { $('.email').hide();}
              else
                 {$('.email').show();}

           })
        })
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
                                
                                var obj = jQuery.parseJSON(option);
                               
                                if(option.message=='success')
                                {
                                     $("#address").val(option.address);
                                     
                                  $("#location_id option[rel='"+obj.city+"']").attr("selected","selected");

                                  $('#location_id option[rel="'+obj.city+'"]').prop('selected', true);
                                  $("#city_name").val(obj.city);
                                }
                                /*$("#start_time").show();
                                $(".innerHtml").empty();
                                $(".innerHtml").html(option);
        */
                                
                            }
                        });
                     }

        })
  //XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
 </script>
 <style type="text/css">
    table.dataTable tbody tr td:last-of-type{padding-right:0px;padding-left: 1px }
 </style>
@endsection
