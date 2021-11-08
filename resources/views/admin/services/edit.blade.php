@extends('layouts.app')

@section('content')
    <h3 class="page-title"><i class="fa fa-hourglass ifont"></i> @lang('quickadmin.services.title')</h3>
    
    {!! Form::model($service, ['method' => 'PUT', 'route' => ['admin.services.update', $service->id]]) !!} 
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_edit')
        </div>
        
        <div class="panel-body">
            <div class="row">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#general">General</a></li>
                    <li><a data-toggle="tab" href="#cost">Cost</a></li>
                 </ul>
                <div class="tab-content">
                    <div id="general" class="tab-pane fade in active"> 
                            <div class="col-xs-12 form-group">
                              {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
                               {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('name'))
                                    <p class="help-block">
                                        {{ $errors->first('name') }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-xs-6 form-group">
                                {!! Form::label('vat_status', 'VAT Status', ['class' => 'control-label']) !!}
                                {!! Form::select('vat_status', $vatstaus, old('vat_status'), ['class' => 'form-control', 'required' => '']) !!}
                            </div>
                            <div class="col-xs-6 form-group">
                                {!! Form::label('tax_class', 'Tax Class', ['class' => 'control-label']) !!}
                               {{--  {!! Form::text('tax_amount', old('tax_amount'), ['class' => 'form-control', 'placeholder' => '']) !!} 
                                {!! Form::select('tax_rate_id_moneybrid', $taxrate, old('tax_rate_id_moneybrid'), ['class' => 'form-control select2']) !!}--}}

                                <select id="tax_rate_id_moneybrid" name="tax_rate_id_moneybrid" class="form-control select2" required>
                                    <option value="">Please select</option>
                                    @foreach($taxrate as $taxrates)
                                        <option value="{{ $taxrates->moneybird_tax_id }}" 
                                            {{ ($service->tax_rate_id_moneybrid == $taxrates->moneybird_tax_id ? "selected":"") }}>{{ $taxrates->name }}</option>
                                    @endforeach
                                </select>
                               
                            </div>
                            <div class="col-xs-6 form-group">
                                {!! Form::label('booking_duration_block', 'Booking Duration Block time*', ['class' => 'control-label']) !!}
                                {!! Form::text('booking_block_duration', old('booking_block_duration'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                            </div>
                            <div class="col-xs-6 form-group">
                                {!! Form::label('booking_duration_block_unit', 'Booking Duration Block Unit*', ['class' => 'control-label']) !!}
                                 {!! Form::select('booking_block_duration_unit', $blockdurationunit, old('booking_block_duration_unit'), ['class' => 'form-control', 'required' => '']) !!}
                            </div>
                            <div class="col-xs-6 form-group">
                                {!! Form::label('min_block_duration', 'Minimum duration*', ['class' => 'control-label']) !!}
                                {!! Form::text('min_block_duration', old('min_block_duration'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                            </div>
                            <div class="col-xs-6 form-group">
                                {!! Form::label('max_block_duration', 'Maximum duration*', ['class' => 'control-label']) !!}
                                {!! Form::text('max_block_duration', old('max_block_duration'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                            </div>
                            <div class="col-xs-6 form-group">
                    {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
                    {!! Form::textarea('description',old('description'),['class'=>'form-control', 'rows' => 5, 'cols' => 20]) !!}
            <p class="help-block"></p>
                    @if($errors->has('description'))
                        <p class="help-block">
                            {{ $errors->first('description') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6 form-group">
                        {!! Form::label('description2', 'Description 2', ['class' => 'control-label']) !!}
                        {!! Form::textarea('description_second',old('description_second'),['class'=>'form-control', 'rows' => 5, 'cols' => 20]) !!}
                <p class="help-block"></p>
                        @if($errors->has('description'))
                            <p class="help-block">
                                {{ $errors->first('description_second') }}
                            </p>
                        @endif
                    </div>
                    </div>
                    <div id="cost" class="tab-pane fade">
                        <div class="col-xs-6 form-group">
                            {!! Form::label('Basic costs', 'Basic costs', ['class' => 'control-label']) !!}
                            {!! Form::text('basic_cost', old('basic_cost'), ['class' => 'form-control', 'placeholder' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('basic_cost'))
                                <p class="help-block">
                                    {{ $errors->first('basic_cost') }}
                                </p>
                            @endif
                        </div>
                        <div class="col-xs-6 form-group">
                            {!! Form::label('Block costs', 'Block costs*', ['class' => 'control-label']) !!}
                            {!! Form::text('block_cost', old('block_cost'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('block_cost'))
                                <p class="help-block">
                                    {{ $errors->first('block_cost') }}
                                </p>
                            @endif
                        </div>
                        <div class="col-xs-6 form-group">
                            {!! Form::label('Show costs', 'Show costs', ['class' => 'control-label']) !!}
                            {!! Form::text('show_cost', old('show_cost'), ['class' => 'form-control', 'placeholder' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('show_cost'))
                                <p class="help-block">
                                    {{ $errors->first('show_cost') }}
                                </p>
                            @endif
                        </div>

                        <div class="col-xs-12 table-responsive ">
                                <table class="table table-bordered table-striped" id="user_table">
                                       <thead>
                                        <tr>
                                            <th width="20%">Series type </th>
                                            <th width="40%">Range</th>
                                            <th width="10%">Basic costs</th>
                                            <th width="10%">Block costs</th>
                                            <th width="10%">Remove</th>
                                        </tr>

                                       </thead>
                                       <tbody>
                                        @foreach ($service->extra_cost as $extra_cost)
                      <tr>
                                            <td>{!! Form::select('booking_series_type', $bookingservicetype, $extra_cost->booking_series_type, ['class' => 'form-control', 'required' => '','name'=>'booking_series_type[]',]) !!}</td>
                                            <td><div class="row"> <div class="col-xs-5">{!! Form::time('booking_pricing_time_from',$extra_cost->booking_pricing_time_from,['class' => 'form-control', 'placeholder' => '','name' => 'booking_pricing_time_from[]']) !!}</div><div class="col-xs-1">To</div><div class="col-xs-5">{!! Form::time('booking_pricing_time_to',$extra_cost->booking_pricing_time_to, ['class' => 'form-control', 'placeholder' => '','name' => 'booking_pricing_time_to[]']) !!}</div></div></td>
                                            <td>  {!! Form::select('booking_basic_cost_duration_type_unit', $basiccostdurationunit, $extra_cost->booking_basic_cost_duration_type_unit, ['class' => 'form-control','name'=>'booking_basic_cost_duration_type_unit[]']) !!}<br/>{!! Form::text('booking_basic_price',$extra_cost->booking_basic_pricing, ['class' => 'form-control', 'placeholder' => '','name' => 'booking_basic_price[]']) !!}</td>
                                            <td> {!! Form::select('booking_block_cost_duration_type_unit', $blockcostdurationunit, $extra_cost->booking_block_cost_duration_type_unit, ['class' => 'form-control', 'required' => '','name'=>'booking_block_cost_duration_type_unit[]']) !!}<br/>{!! Form::text('booking_block_price', $extra_cost->booking_block_pricing, ['class' => 'form-control', 'placeholder' => '','name' => 'booking_block_price[]']) !!}</td>
                                            <td><button type="button" name="remove" id="" class="btn btn-danger remove">Remove</button></td>
                                        </tr>  
                          @endforeach
                                         
                                       </tbody>
                                </table>
                            </div>
                        
                       
                             <div class="col-xs-12 form-group"><button type="button" name="add" id="add" class="btn btn-success">Add Series Type</button></div>
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
<script>
$(document).ready(function(){

 var count = {{ count($service->extra_cost) }};

 ///dynamic_field(count);

 function dynamic_field(number)
 {

  html = '<tr>';
        html += '<td>{!! Form::select('booking_series_type', $bookingservicetype, old('booking_series_type'), ['class' => 'form-control', 'required' => '','name'=>'booking_series_type[]']) !!}</td>';
        html += '<td><div class="row"> <div class="col-xs-5">{!! Form::time('booking_pricing_time_from', old('booking_pricing_time_from'), ['class' => 'form-control', 'placeholder' => '','name' => 'booking_pricing_time_from[]']) !!}</div><div class="col-xs-1">To</div><div class="col-xs-5">{!! Form::time('booking_pricing_time_to', old('booking_pricing_time_to'), ['class' => 'form-control', 'placeholder' => '','name' => 'booking_pricing_time_to[]']) !!}</div></div></td>';
         html += '<td>  {!! Form::select('booking_basic_cost_duration_type_unit', $basiccostdurationunit, old('booking_basic_cost_duration_type_unit'), ['class' => 'form-control','name'=>'booking_basic_cost_duration_type_unit[]']) !!}<br/>{!! Form::text('booking_basic_price', old('booking_basic_price'), ['class' => 'form-control', 'placeholder' => '','name' => 'booking_basic_price[]']) !!}</td>';
          html += '<td> {!! Form::select('booking_block_cost_duration_type_unit', $blockcostdurationunit, old('booking_block_cost_duration_type_unit'), ['class' => 'form-control', 'required' => '','name'=>'booking_block_cost_duration_type_unit[]']) !!}<br/>{!! Form::text('booking_block_price', old('booking_block_price'), ['class' => 'form-control', 'placeholder' => '','name' => 'booking_block_price[]']) !!}</td>';
       /* if(number > 1)
        {*/
            html += '<td><button type="button" name="remove" id="" class="btn btn-danger remove">Remove</button></td></tr>';
            $('tbody').append(html);
        /*}
        else
        {*/   
            //html += '<td></td></tr>';
            //alert(html);
           // $('tbody').html(html);
       // }
 }

 $(document).on('click', '#add', function(){
  count++;
  dynamic_field(count);
 });

 $(document).on('click', '.remove', function(){
  count--;
  $(this).closest("tr").remove();
 });

}); 
</script>
@stop

