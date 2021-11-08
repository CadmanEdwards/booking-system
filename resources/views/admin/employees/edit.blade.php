@extends('layouts.app')
<style type="text/css">
    .prv_image_small {height: 100px}
    .prv_image_small img{max-height: 100%;max-width: 100%}
    .select2 select2-container select2-container--default{
        width:400px !important;
    }

</style>
@section('content')
    <h3 class="page-title"><i class="fa fa-user ifont"></i>  @lang('quickadmin.employees.title')</h3>
    
    {!! Form::model($employee, ['method' => 'PUT', 'route' => ['admin.employees.update', $employee->id], 'files' => true]) !!}

    <div class="panel panel-default">
        <div class="panel-heading bold">
            @lang('quickadmin.qa_edit')
        </div>

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
                    {!! Form::label('first_name', 'First name', ['class' => 'control-label']) !!}
                    {!! Form::text('first_name', old('first_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('first_name'))
                        <p class="help-block">
                            {{ $errors->first('first_name') }}
                        </p>
                    @endif
                </div> 
                <div class="col-xs-6 form-group">
                    {!! Form::label('last_name', 'Last name', ['class' => 'control-label']) !!}
                    {!! Form::text('last_name', old('last_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('last_name'))
                        <p class="help-block">
                            {{ $errors->first('last_name') }}
                        </p>
                    @endif
                </div>
           
                <div class="col-xs-6 form-group">
                    {!! Form::label('phone', 'Phone', ['class' => 'control-label']) !!}
                    {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('phone'))
                        <p class="help-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                </div> 
                <div class="col-xs-6 form-group">
                    {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                    {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email'))
                        <p class="help-block">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>
                   <div class="col-xs-6 form-group">
                    {!! Form::label('moneybird_username', 'Therapist Title', ['class' => 'control-label']) !!}
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
                <!-- XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXx --> 
                <div class="col-xs-6 form-group">
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-8 no-padding">
                            {!! Form::label('logo1_file','Logo 1',['class'=>'control-label']) !!}
                            {!! Form::file('logo1_file', ['class' => 'control-label']) !!} 
                            <p class="help-block"></p>
                            @if($errors->has('logo1_file'))
                                <p class="help-block">{{ $errors->first('logo1_file') }}</p>
                            @endif
                        </div>
                        @if(!empty($employee->logo1))
                        <div class="col-xs-4 prv_image_small">
                            <img src="{{ url('/public/upload/'.$employee->logo1) }}"></img>
                        </div>
                        @endif
                    </div>

                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-8 no-padding">
                            {!! Form::label('logo2_file','Logo 2',['class'=>'control-label']) !!}
                            {!! Form::file('logo2_file', ['class' => 'control-label']) !!} 
                            <p class="help-block"></p>
                            @if($errors->has('logo2_file'))
                                <p class="help-block">{{ $errors->first('logo2_file') }}</p>
                            @endif
                        </div>
                        @if(!empty($employee->logo2))
                        <div class="col-xs-4 prv_image_small">
                            <img src="{{ url('/public/upload/'.$employee->logo2) }}"></img>
                        </div>
                        @endif
                    </div>

                          
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('agb_code', 'Invoice Line 1', ['class' => 'control-label']) !!}
                    
                 {!! Form::textarea('agb_code',old('agb_code'),['class'=>'form-control', 'rows' => 5, 'cols' => 20]) !!}

                    
                  <p class="help-block"></p>
                    @if($errors->has('agb_code'))
                        <p class="help-block">
                            {{ $errors->first('agb_code') }}
                        </p>
                    @endif
                </div> 
                <div class="col-xs-6 form-group">
                    {!! Form::label('rcbz_no', 'Invoice Line 2', ['class' => 'control-label']) !!}

                    {!! Form::textarea('rcbz_no',old('rcbz_no'),['class'=>'form-control', 'rows' => 5, 'cols' => 20]) !!}

                    
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
                    {!! Form::label('address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::textarea('address',old('address'),['class'=>'form-control', 'rows' => 5, 'cols' => 20]) !!}
            <p class="help-block"></p>
                    @if($errors->has('address'))
                        <p class="help-block">
                            {{ $errors->first('address') }}
                        </p>
                    @endif
                </div>
               
                 <div class="col-xs-6 form-group">
                    {!! Form::label('password', 'Password*', ['class' => 'control-label']) !!}
                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '','id' => 'pass']) !!}
                    <span class="btn btn-danger" name='password' id='passwordgenerate'>Generate Password</span><span id='passwordshow'></span>
                    <p class="help-block"></p>
                    @if($errors->has('password'))
                        <p class="help-block">
                            {{ $errors->first('password') }}
                        </p>
                    @endif
                </div>  
            </div>
            <div id="roomlocation" class="tab-pane fade">
              <?php $i = 1; ?>
             
              @foreach($roomLoctions as $key => $roomLoction)
                <div class="row_{!! $i !!} rows">
                    <div class="col-sm-12">
                   
                      <div class="col-xs-5 form-group" rel="{{ $i }}">
                        {!! Form::label('location', 'Location*', ['class' => 'control-label ']) !!}
                        {!! Form::Select('location_id',$locations, $key, ['class' => 'form-control location newselect2', 'placeholder ' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('location'))
                            <p class="help-block">
                                {{ $errors->first('location') }}
                            </p>
                        @endif
                    </div>

                    <div class="col-xs-5 form-group selectOp_{{ $i }}" rel="{{ $i }}">
                        {!! Form::label('room_id', 'Room*', ['class' => 'control-label']) !!}
           
                        <select name="room_id[]" class="form-control js-example-basic-multiple newselect2 optionAdd" multiple>
                            {{-- 
                        {!! Form::Select('room_id[]', $rooms,$roomLoction['room'], ['class' => 'form-control js-example-basic-multiple newselect2 optionAdd', 'required' => '','multiple'=>"multiple"]) !!} --}}
                       
                          @foreach(DB::table('rooms')->select('rooms.id','rooms.room_name')->leftjoin('employees_rooms',function ($join) use ($employeeId,$key) {                            
            $join->on('employees_rooms.room_id', '=', 'rooms.id')
                 ->where('employees_rooms.employee_id', '=', $employeeId)
                 ->where('employees_rooms.location_id', '=', $key);
                   })->where('rooms.deleted_at','=',NULL)->orderByRaw('IF(employees_rooms.orders IS NULL , 1 , 0)')->orderByRaw('employees_rooms.orders ASC')->get() as $room)
                             <option value="{{ $room->id }}_{{ $key }}" {{ in_array($room->id,$roomLoction['room'])?'selected':''}}>{{ $room->room_name }}</option>
                            @endforeach 
                        </select>    
                        <p class="help-block"></p>
                        @if($errors->has('id'))
                            <p class="help-block">
                                {{ $errors->first('id') }}
                            </p>
                        @endif 
                    </div>

                        @if($i==1)  
                         <div class="col-xs-2 form-group"><button type="button" name="add" id="add" class="btn btn-success add">Add</button></div>
                         @else
                          <div class="col-xs-2 form-group"><button type="button" name="remove" id="{{ $i }}" class="btn btn-danger remove">Remove</button></div>  
                        @endif
                    </div>
                </div>
            
             <?php $i++; ?>
                @endforeach 
            
             
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
                                        @foreach ($working_type as $key => $values )
                                           <tr>
                                            <td>
                                                <input type="hidden" name='day[]' value="{{ $values }}"> {{ $values }} 


                                            </td>
                                            <td><div class="row"> <div class="col-xs-5">{!! Form::time('booking_pricing_time_from',
                                            isset($empworkinghHours[$values]['start_time']) ? $empworkinghHours[$values]['start_time']:''

                                            , ['class' => 'form-control', 'placeholder' => '','name' => 'booking_pricing_time_from['.$values.']']) !!}</div><div class="col-xs-1">To</div>
                                                <div class="col-xs-5">
                                                   {!! Form::time('booking_pricing_time_to', isset($empworkinghHours[$values]['finish_time']) ? $empworkinghHours[$values]['finish_time'] : '', ['class' => 'form-control', 'placeholder' => '','name' => 'booking_pricing_time_to['.$values.']']) !!}
                                                </div></div>
                                            </td>
                                            <td>
                                                <div class="col-xs-12">
                                                <div class="col-xs-2">{!! Form::checkbox('repeated[]', old('repeated'),null,array('id' => $key,'class'=>'checkbox')) !!}</div>
                                                <div class="col-xs-10">

                                                {!! Form::text('repeated_number', old('repeated_number'),['class' => 'form-control hide','style'=>'width:50%','placeholder' => '','placeholder'=>' Repeated Number','name' => 'repeated_number['.$values.']','id'=>'repeatedValue'.$key]) !!}</div>
                                                 </div>
                                            </td>
                                            <td class="col-sm-12">
                                                {!! Form::select('working_location_id', $locations,isset($empworkinghHours[$values]['location_id']) ? $empworkinghHours[$values]['location_id'] : '' , ['class' => 'form-control col-sm-12 select2',  'name' => 'working_location_id['.$values.']']) !!}

                                            </td> 
                                          
                                           </tr>
                                        @endforeach
                                       </tbody>
                                </table>
                            </div>
                        
                       
                           
                    </div> 
                   
        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop
@section('javascript')
    @parent
<script>
$(document).ready(function(){
 
 $('.newselect2').select2({tags: true});
 $('.select2').css('width','100%');
  //$(".newselect2").val(["Kamer H-108", "Ruimte H-006"]).trigger("change");
  //select2:opening
 function selectItem(target, id) { // refactored this a bit, don't pay attention to this being a function
  var option = $(target).children('[value='+id+']');
  option.detach();
  $(target).append(option).change();
} 

function customPreSelect() {
  let items = $('#selected_items').val().split(',');
  $("select").val('').change();
  initSelect(items);
}

function initSelect(items) { // pre-select items
  items.forEach(item => { // iterate through array of items that need to be pre-selected
    let value = $('select option[value='+item+']').text(); // get items inner text
    $('select option[value='+item+']').remove(); // remove current item from DOM
    $('select').append(new Option(value, item, true, true)); // append it, making it selected by default
  });
}


 

 $(".newselect2").on("select2:select", function (evt) {
          /*var element = evt.params.data.element;
        
          var $element = $(element);
          
          $element.detach();
          $(this).append($element);
          $(this).trigger("change");*/
          selectItem(evt.target, evt.params.data.id);
        });

var count = {{ count($roomLoctions) }};
if(count==0)
 {dynamic_field_location(count);}

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
 function dynamic_field_location(number)
 {
  html = '<div class="row_'+number+' row"><div class="col-sm-12"><div class="col-xs-5 form-group" rel="'+number+'">';
        html += '{!! Form::label('location', 'Location*', ['class' => 'control-label ']) !!}{!! Form::Select('emp_location_id[]',$locations, old('location_id'), ['class' => 'form-control location', 'placeholder newselect2' => '']) !!} </div>';
 html += '<div class="col-xs-5 form-group selectOp_'+number+'" rel="'+number+'">{!! Form::label('room_id', 'Room*', ['class' => 'control-label']) !!}{!! Form::Select('room_id[]', $rooms, $employee->rooms, ['class' => 'form-control js-example-basic-multiple newselect2 optionAdd', 'required' => '','multiple'=>"multiple"]) !!} </div>';
         
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
            html += '<td><button type="button" name="remove" id="'+number+'" class="btn btn-danger remove">Remove</button></td></tr>';
            $('tbody').append(html);
        }
        else
        {   
            html += '<td></td></tr>';
            //alert(html);
            $('tbody').html(html);
       }
 }
$(document).on('change', '.location', function(){
      
    UpdateRoom($(this).val(),$(this).parent().attr('rel'));
 });

 $(document).on('click', '#add', function(){
  count++;
  dynamic_field_location(count);
 });

 $(document).on('click', '.remove', function(){
  count--;
  //$(this).closest(".row").remove();
  var IdVal = $(this).attr('id');
  // $(this).closest('div.row_'+IdVal).remove();
  //alert('.row_'+IdVal);
  $(this).parent().parent().remove();
 });

}); 
</script>
@stop