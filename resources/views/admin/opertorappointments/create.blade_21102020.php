@extends('layouts.app')
<link rel="stylesheet" href="{{ url('quickadmin/css') }}/style.css"/>
<link rel="stylesheet" href="{{ url('quickadmin/css') }}/dateTimePicker.css"/>
@section('content')
    <h3 class="page-title">@lang('quickadmin.appointments.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.opertorappointments.store']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
					{!! Form::label('client_id', 'Client*', ['class' => 'control-label']) !!}
                    <select id="client_id" name="client_id" class="form-control select2" required>
						<option value="">Please select</option>
						@foreach($clients as $client)
						<option value="{{ $client->id }}" {{ (old("client_id") == $client->id ? "selected":"") }}>{{ $client->first_name }} {{ $client->last_name }}</option>
						@endforeach
					</select>

                    <p class="help-block"></p>
                    @if($errors->has('client_id'))
                        <p class="help-block">
                            {{ $errors->first('client_id') }}
                        </p>
                    @endif
                    <p class="help-block"><a  href="javascript:void(0)" data-toggle="modal" data-target="#calendarModal">Create Client</a></p>
                </div>

            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('service_id', 'Service*', ['class' => 'control-label']) !!}
                    <select id="service_id" name="service_id" class="form-control select2" required>
						<option value="">Please select</option>
						@foreach($services as $service)
						<option value="{{ $service->id }}" data-block-duration="{{ $service->booking_block_duration }}" data-block="{{ $service->min_block_duration }}" data-price="{{ $service->block_cost }}" {{ (old("service_id") == $service->id ? "selected":"") }}>{{ $service->name }}</option>
						@endforeach
					</select>
                    <p class="help-block"></p>
                    @if($errors->has('service_id'))
                        <p class="help-block">
                            {{ $errors->first('service_id') }}
                        </p>
                    @endif
                    {{-- <input type="hidden" id="totalprice" name="price" value="0"> --}}
					<input type="hidden" id="price" value="0">
					<input type="hidden" id="minBlock" value="0">
					<input type="hidden" id="blockDuration" value="0">
					<input type="hidden" id="selectedemployeedefault" value="0">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('location_id', 'Location*', ['class' => 'control-label']) !!}
                    <select id="location_id" name="location_id" class="form-control select2" required>
						<option value="">Please select</option>
						@foreach($locations as $location)
							<option value="{{ $location->id }}"  {{ (old("location_id") == $location->id ? "selected":"") }}>{{ $location->location_name }}</option>
						@endforeach
					</select>
                    <p class="help-block"></p>
                    @if($errors->has('location_id'))
                        <p class="help-block">
                            {{ $errors->first('location_id') }}
                        </p>
                    @endif
					<input type="hidden" id="price" value="0">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('Repeated', 'Repeat Appointment', ['class' => 'control-label']) !!}
                    <select id="repeat_appointment" name="repeat_appointment" class="form-control select2" >
                    	<option value="0">Please select</option>
					    <option value="daily">Daily</option>
					    <option value="weekly">Weekly</option>
					    <option value="monthly">Monthly</option>
					    <option value="gap">2 week gap</option>	
					</select>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('Repeated ', 'Repeat Appointment Number', ['class' => 'control-label']) !!}
                     {!! Form::text('repeated_number', old('repeated_number'), ['class' => 'form-control', 'placeholder' => '']) !!}
                </div>
            </div>

              <div class="row">
                <div class="col-xs-12 form-group">
		           {!! Form::label('date', 'Date*', ['class' => 'control-label']) !!} 	
		          <div id="basic" data-toggle="calendar"></div>
		        </div>
		        
		      </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                	<input type="hidden" name="date" id="date" value="{{ date('Y-m-d') }}"/>
                    {{-- {!! Form::label('date', 'Date*', ['class' => 'control-label']) !!}
                    {!! Form::text('date', old('date'), ['class' => 'form-control date', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('date'))
                        <p class="help-block">
                            {{ $errors->first('date') }}
                        </p>
                    @endif --}}
                </div>
            </div>
            
            <div class="row" id="start_time" style="display: none;">
                <div class="col-xs-12 form-group">
					{!! Form::label('start_time', 'Start time*', ['class' => 'control-label']) !!}
					<input type="hidden" name="starting_time" id="starting_time" class="form-control"/>
					<div class="form-inline innerHtml">
					 
					 
					{{-- <select name="starting_hour" id="starting_hour" class="form-control" required style="max-width: 85px;">
						<option value="-1" selected>Please select</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
					</select>
					:
                    <select name="starting_minute" id="starting_minute" class="form-control" required style="max-width: 85px;">
						<option value="-1" selected>Please select</option>
						<option value="00">00</option>
						<option value="15">15</option>
						<option value="30">30</option>
						<option value="45">45</option>
					</select> --}}
					</div>
                </div>
            </div>
            {{-- <div class="row" id="finish_time" style="display: none;">
                <div class="col-xs-12 form-group">
					{!! Form::label('finish_time', 'Finish time*', ['class' => 'control-label']) !!}
					<div class="form-inline">
					<select name="finish_hour" id="finish_hour" class="form-control" required style="max-width: 85px;">
						<option value="-1" selected>Please select</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						<option value="20">20</option>
						<option value="21">21</option>
						<option value="22">22</option>
						<option value="23">23</option>
					</select>
					:
                    <select name="finish_minute" id="finish_minute" class="form-control" required style="max-width: 85px;">
						<option value="-1" selected>Please select</option>
						<option value="00">00</option>
						<option value="15">15</option>
						<option value="30">30</option>
						<option value="45">45</option>
					</select>
					</div>
                </div>
            </div> --}}
            <div class="row room_list_show" style="display:none">
                  <div class="col-xs-12 form-group">
                    {!! Form::label('room_time', 'Select Room*', ['class' => 'control-label']) !!}
                    <div class="form-inline roominnerHtml">
                    </div>    
             </div>   
			<hr />
			<div id="results" style="display: none;">
			<p class="total_time"><strong>Total time: <span id="time">0</span> min(s)</strong></p>
			<p class="total_price"><strong>Total price: € <input style="text-align: left;line-height: 20px; margin:4px;padding-left:10px" type='text' name="price" value='' id="price_total"></span></strong></p>
			</div>
			<div class="row">
                <div class="col-xs-12 form-group">
                	{!! Form::label('Switch Off Confirmed Email', 'Switch Off Confirmed Email', ['class' => 'control-label']) !!}
                	{!! Form::checkbox('switched_off_confirmed_email', old('switched_off_confirmed_email','Y'),null) !!}
                                               
                  </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                	{!! Form::label('Switch Off Reminder Email', 'Switch Off Reminder Email', ['class' => 'control-label']) !!}
                	{!! Form::checkbox('switched_off_reminder_email','Y',false) !!}
                                               
                  </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('comments', 'Comments', ['class' => 'control-label']) !!}
                    {!! Form::textarea('comments', old('comments'), ['class' => 'form-control ', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('comments'))
                        <p class="help-block">
                            {{ $errors->first('comments') }}
                        </p>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}

     <div class="modal fade" id="calendarModal" style="overflow:hidden;" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
            <h4 id="modalTitle" class="modal-title"></h4>
        </div>
        <div id="modalBody" class="modal-body">
            
          <h3 class="page-title">Customer</h3>
    {!! Form::open(['method' => 'POST','id'=>'form' ,'route' => ['admin.clientsjson.opertorjsonstore']]) !!}
   
    <div class="panel panel-default">
        <div class="panel-heading">
            Create customer
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('first_name', 'First name*', ['class' => 'control-label']) !!}
                    {!! Form::text('first_name', old('first_name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('first_name'))
                        <p class="help-block">
                            {{ $errors->first('first_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('last_name', 'Last name*', ['class' => 'control-label']) !!}
                    {!! Form::text('last_name', old('last_name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
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
                    {!! Form::text('postcode', old('postcode'), ['class' => 'form-control', 'placeholder' => '','id'=>'postcode']) !!}
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
                    {!! Form::text('address', old('address'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address'))
                        <p class="help-block">
                            {{ $errors->first('address') }}
                        </p>
                    @endif
                </div>
            <div class="col-xs-6 form-group">
                    {!! Form::label('city_name', 'City', ['class' => 'control-label']) !!}
                    {!! Form::text('city_name', old('city_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
            
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
                    {!! Form::Select('parent_id',$parentClient, old('parent_id'), ['class' => 'form-control  parent_id', 'placeholder ' => '']) !!}
                </div>

                 <div class="col-xs-6 form-group email">
                    {!! Form::label('email', 'Email*', ['class' => 'control-label']) !!}
                    {!! Form::email('email', old('email'), ['class' => 'form-control input-group', 'placeholder' => '', ]) !!}
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
                    {!! Form::label('Company', 'Company Name', ['class' => 'control-label']) !!}
                    {!! Form::text('company_name', old('company_name'), ['class' => 'form-control', 'placeholder' => '','id'=>'company_name']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('company_name'))
                        <p class="help-block">
                            {{ $errors->first('company_name') }}
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

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div> 
@stop
<style>

.popper,
  .tooltip {
    position: absolute;
    z-index: 9999;
    background: #FFC107;
    color: black;
    width: 250px !important;
    border-radius: 1px !important;
    padding: 2px;
    text-align: left;
    opacity: 1 !important;
  }
  .tooltip-inner{
    max-width:350px !important;
    text-align: left !important;
  }
  .style5 .tooltip {
    background: #1E252B;
    color: #FFFFFF;
    max-width: 100px;
    width: auto;
    font-size: .8rem;
    padding: .5em 1em;
  }
  .popper .popper__arrow,
  .tooltip .tooltip-arrow {
    width: 0;
    height: 0;
    border-style: solid;
    position: absolute;
    margin: 5px;
  }

  .tooltip .tooltip-arrow,
  .popper .popper__arrow {
    border-color: #FFC107;
  }
  .style5 .tooltip .tooltip-arrow {
    border-color: #1E252B;
  }
  .popper[x-placement^="top"],
  .tooltip[x-placement^="top"] {
    margin-bottom: 5px;
  }
  .popper[x-placement^="top"] .popper__arrow,
  .tooltip[x-placement^="top"] .tooltip-arrow {
    border-width: 5px 5px 0 5px;
    border-left-color: transparent;
    border-right-color: transparent;
    border-bottom-color: transparent;
    bottom: -5px;
    left: calc(50% - 5px);
    margin-top: 0;
    margin-bottom: 0;
  }
  .popper[x-placement^="bottom"],
  .tooltip[x-placement^="bottom"] {
    margin-top: 5px;
  }
  .tooltip[x-placement^="bottom"] .tooltip-arrow,
  .popper[x-placement^="bottom"] .popper__arrow {
    border-width: 0 5px 5px 5px;
    border-left-color: transparent;
    border-right-color: transparent;
    border-top-color: transparent;
    top: -5px;
    left: calc(50% - 5px);
    margin-top: 0;
    margin-bottom: 0;
  }
  .tooltip[x-placement^="right"],
  .popper[x-placement^="right"] {
    margin-left: 5px;
  }
  .popper[x-placement^="right"] .popper__arrow,
  .tooltip[x-placement^="right"] .tooltip-arrow {
    border-width: 5px 5px 5px 0;
    border-left-color: transparent;
    border-top-color: transparent;
    border-bottom-color: transparent;
    left: -5px;
    top: calc(50% - 5px);
    margin-left: 0;
    margin-right: 0;
  }
  .popper[x-placement^="left"],
  .tooltip[x-placement^="left"] {
    margin-right: 5px;
  }
  .popper[x-placement^="left"] .popper__arrow,
  .tooltip[x-placement^="left"] .tooltip-arrow {
    border-width: 5px 0 5px 5px;
    border-top-color: transparent;
    border-right-color: transparent;
    border-bottom-color: transparent;
    right: -5px;
    top: calc(50% - 5px);
    margin-left: 0;
    margin-right: 0;
  }

</style>
@section('javascript')
    @parent
    <script src="{{ url('quickadmin/js') }}/timepicker.js"></script>
    <script src="{{ url('quickadmin/js') }}/dateTimePicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>   
   <script src="{{ url('quickadmin/js') }}/popper.min.js"></script>
   <script src="{{ url('quickadmin/js') }}/tooltip.min.js"></script
     <script>
        $('.datetime').datetimepicker({
            autoclose: true,
            dateFormat: "{{ config('app.date_format_js') }}",
            timeFormat: "HH:mm:ss"
        });
    </script>
	<script>
$(document).ready(function(){
  
           $("#passwordgenerate").trigger('click');
            $('.parent_id').on('change',function(){
          
              if($(this).val() > 0)
                 { $('.email').hide();}
              else
                 {$('.email').show();}

           })
        })
      $('#form').on('submit', function(e){
    e.preventDefault(); //1

    var $this = $(this); //alias form reference

    $.ajax({ //2
        url: $this.prop('action'),
        method: $this.prop('method'),
        dataType: 'json',  //3
        data: $this.serialize() //4
    }).done( function (response) {
        
        if (response.success) {
            var toAppend = '';
            toAppend += '<option value='+response.client_id+'>'+response.name+'</option>';
            $('#client_id').append(toAppend);
            $('#first_name').val('');
            $('#last_name').val('');
            $('#postcode').val('');
            $('#phone').val('');
            $('#email').val('');
            $('#house_number').val('');
            $('#city_name').val('');
            $('#address').val('');
            $('#company_name').val('');
            $('#dob').val('');
            $('#pass').val('');
            $('#passwordshow').html('');
            $('#confpass').html('');
            $('#client_id option[value="'+response.client_id+'"]').prop('selected', true);
            $('#calendarModal').modal('hide');
            //$('#target-div').html(response.status); //5
        }
        else
        {
           //alert(response.errors)  
           $.each(response.errors, function (i, error) {
                var el = $(document).find('[name="'+i+'"]');
                el.after($('<span style="color: red;">'+error[0]+'</span>'));
            });
        }
    });
});

	$('.date').datepicker({
		autoclose: true,
		dateFormat: "{{ config('app.date_format_js') }}"
	}).datepicker("setDate", "");
$('#basic').calendar(
    {
      
      onSelectDate: function(date, month, year)
        {
          //  alert('heekk');

        	var GivenDate =  year+"- 0"+month+"-"+date;
             
			var CurrentDate = new Date();
			GivenDate = new Date(year,month-1,date);
           /* alert("CurrentDate"+CurrentDate);
             alert("GivenDate"+GivenDate);*/
		    var unselected=false;
		    if(GivenDate >= CurrentDate){
			}else{
			    alert('Given date is not greater than the current date.');
			    unselected=true;
			}
           $("td.cur-month").each(function(){
                 if($(this).text() == date)
                   {  
                   	  if(!unselected)
                         {$(this).addClass('selectedDate')}
                    }
                 else
                   {$(this).removeClass('selectedDate')}
  
           })

		    var service_id = $("#service_id").val();
		    var location_id = $("#location_id").val();
			var date = 	year+"-"+month+"-"+date;
			if(!unselected)
			  {
			  	$("#date").val(date); 
			  	UpdateEmployees(service_id, date,location_id);
			  }
  
		 
        }

	});
    </script>
	<script>
		$("#service_id").on("change", function() {
			$("#price").val($('option:selected', this).attr('data-price'));
			$("#minBlock").val($('option:selected', this).attr('data-block'));	
			$("#blockDuration").val($('option:selected', this).attr('data-block-duration'));	
			var date = $("#date").val();
			var service_id = $("#service_id").val();
			var location_id = $("#location_id").val();
           //$('#price_total').val('');
			UpdateEmployees(service_id, date,location_id);
		});
    
		$("#client_id").on("change", function() {
			
			var client_id = $(this).val();
            $('#price_total').val('');
			UpdateLocation(client_id);
		});
      $("#location_id").on("change", function() {
      	
			var date = $("#date").val();
			var service_id = $("#service_id").val();
			var location_id = $("#location_id").val();
            $('.roominnerHtml').empty();
            $('#price_total').val('');
			UpdateEmployees(service_id, date,location_id);
		});
	  
		$("#date").change(function() {
			var service_id = $("#service_id").val();
			var date = $("#date").val();
            $('#price_total').val('');
			UpdateEmployees(service_id, date);
		});
		
		$("#starting_hour, #finish_hour, #starting_minute, #finish_minute").on("change", function () {
			CountPrice();		
		});

		$('body').on("change", "input[type=radio][name=employee_id]", function() {
			var employee_id = $(this).val();
			var date        = $("#date").val();
			var location_id = $("#location_id").val();
			var service_id  = $("#service_id").val();
            var no_of_block  = $("#no_of_block").val();
             $('.roominnerHtml').empty();
             $('#price_total').val('');
              if(service_id != "" && date != "" && location_id != "" && employee_id!="") {
				$.ajax({
					url: '{{ url("admin/get-employees-time-slot") }}',
					type: 'GET',
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					data: {service_id:service_id, date:date,location_id:location_id,employee_id:employee_id,no_of_block:no_of_block},
					success:function(option){
						//alert(option);
						$("#start_time").show();
						$(".innerHtml").empty();
						$(".innerHtml").html(option);

						
					}
				});

               
			}

        $('body').on("click", ".selectedDiv",function(e){
                            //alert($(".borderTimeing").length());

                         var price = parseFloat($("#price").val());
                var minBlock = parseFloat($("#no_of_block").val());
                var blockDuration = $("#blockDuration").val();
               var actualPrice = price*minBlock

                   e.preventDefault();
                            var employee_id =  $("input[name='employee_id']:checked").val(); 
                            var date        = $("#date").val();
                            var location_id = $("#location_id").val();
                            var service_id  = $("#service_id").val();
                            var no_of_block  = $("#no_of_block").val();
                            var bookTime  = $(this).html();

                            $.ajax({
                                url: '{{ url("admin/get-employees-room") }}',
                                type: 'GET',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: {service_id:service_id, date:date,location_id:location_id,employee_id:employee_id,no_of_block:no_of_block,bookTime:bookTime},
                                success:function(option){
                                    
                                    $(".room_list_show").show();
                                    $(".roominnerHtml").empty();
                                    $(".roominnerHtml").html(option);

                                    
                                }
                            });

                             $.ajax({
                                url: '{{ url("admin/get-appointment-price") }}',
                                type: 'GET',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data: {service_id:service_id, date:date,price:actualPrice,location_id:location_id,employee_id:employee_id,no_of_block:no_of_block,bookTime:bookTime},
                                success:function(option){

                                    $("#price_total").val(option);
                                    /*$(".room_list_show").show();
                                    $(".roominnerHtml").empty();
                                    $(".roominnerHtml").html(option);*/

                                    
                                }
                            });

                        })
           $("#start_time").on('click',".borderTimeing",function(e){
							//alert($(".borderTimeing").length());
                             e.preventDefault();
							 $('.selectedDiv').not(this).removeClass('selectedDiv');
				                $(this).addClass('selectedDiv');
				               //$(this).addClass('selectedDiv');
				            
				               $("#starting_time").val($(this).text());

						})
           CountPrice()
           CountTime()
			/*var starting_hour = parseInt($(".starting_hour_"+employee_id).text());
			var starting_minute = $(".starting_minute_"+employee_id).text();
			var finish_hour = starting_hour+1;
			if(finish_hour < 10) {
				finish_hour = "0"+finish_hour;
			}
			if(starting_hour < 10) {
				starting_hour = "0"+starting_hour;
			}
			$('#starting_hour option[value='+starting_hour+']').prop('selected','true');
			$('#starting_minute option[value='+starting_minute+']').prop('selected','true');
			$('#finish_hour option[value='+finish_hour+']').prop('selected','true');
			$('#finish_minute option[value='+starting_minute+']').prop('selected','true');
			$("#start_time, #finish_time").show();
			CountPrice();*/
		});

        $('body').on("blur", "input[type=text][name=no_of_block]", function() {
            var employee_id = $(".employee_id:checked").val();
            var date        = $("#date").val();
            var location_id = $("#location_id").val();
            var service_id  = $("#service_id").val();
            var no_of_block  = $("#no_of_block").val();
            $('.roominnerHtml').empty();
              if(service_id != "" && date != "" && location_id != "" && employee_id!="") {
                $.ajax({
                    url: '{{ url("admin/get-employees-time-slot") }}',
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {service_id:service_id, date:date,location_id:location_id,employee_id:employee_id,no_of_block:no_of_block},
                    success:function(option){
                        //alert(option);
                        $("#start_time").show();
                        $(".innerHtml").empty();
                        $(".innerHtml").html(option);
                         

                        
                    }
                });
            }
 
     

           $("#start_time").on('click',".borderTimeing",function(e){
                            //alert($(".borderTimeing").length());
                             e.preventDefault();
                             $('.selectedDiv').not(this).removeClass('selectedDiv');
                                $(this).addClass('selectedDiv');
                               //$(this).addClass('selectedDiv');
                            
                               $("#starting_time").val($(this).text());

                        })
           CountPrice()
           CountTime()
        });

		function CountTime(){
			 $("#no_of_block").on('blur',function(){
		   var block = $(this).val();
           var blockTime = $(this).attr('data-block-time'); 
           var price = parseFloat($("#price").val());
           $("#time").html(block*blockTime);
           $("#price_total").val(price*block);
		})
		}
		function CountPrice() {
			var price = parseFloat($("#price").val());
			var minBlock = parseFloat($("#minBlock").val());
			var blockDuration = $("#blockDuration").val();
			$("#totalprice").val(price*minBlock);
			$("#price_total").val(price*minBlock);
			$("#time").html(minBlock*blockDuration);
			$("#results").show();
			
		}
   function selectedEmployeeDefault(employee_id)
   { 
   	   setTimeout(function(){
                        //alert("hello");
                        $("input[name=employee_id][value=" + employee_id + "]").prop('checked', true).trigger('change'); 
                        // $(".employee_id").val(option['employee_id']).prop("checked", true);
					    }, 100);
   }
	function UpdateLocation(client_id)
     {

     	 if(client_id != "") {
				$.ajax({
					url: '{{ url("admin/get-client-location") }}',
					type: 'GET',
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					data: {client_id:client_id},
                    success:function(option){
                    	if(!option['isappointment'])
						  {$('#location_id').val(option['location_id']).trigger('change');}
					   else
					     {
					     $('#location_id').val(option['location_id']).trigger('change');
					     
                           $('#service_id').val(option['service_id']).trigger('change'); 	

                           $('#selectedemployeedefault').val(option['employee_id']);

                          selectedEmployeeDefault(option['employee_id']);
 	
					    
					    
					    }	
					//	$('#location_id').val(option);
					 }
				});
			}

     }

		function UpdateEmployees(service_id, date,location_id)
		{
			if(service_id != "" && date != "" && location_id != "") {
				$.ajax({
					url: '{{ url("admin/get-employees") }}',
					type: 'GET',
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					data: {service_id:service_id, date:date,location_id:location_id},
					success:function(option){
						//alert(option);
						//$(".get-employees").remove();
                        $(".employees").remove();
						$("#date").closest(".row").after(option);
						$("#start_time, #finish_time").hide();
						$("#results").hide();
					  	$( '.tooltip_show' ).each(function() {
                                new Tooltip($(this), {
                                                          title: $(this).attr('title'),
                                                                placement: 'top',
                                                                trigger: 'hover',
                                                                container: 'body'
                                                        });
                                                      });
						selectedEmployeeDefault($('#selectedemployeedefault').val());
					}
				});
			}
		}
		

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
	</script>
	 
    
  

@stop