@extends('layouts.app')
<style type="text/css">
    .prv_image_small {height: 100px}
    .prv_image_small img{max-height: 100%;max-width: 100%}
     

</style>
@section('content')
    <h3 class="page-title"><i class="fa fa-user-circle ifont"></i>  Company</h3>
    
    {!! Form::model($company, ['method' => 'PUT', 'route' => ['admin.companies.update', $company->id], 'files' => true]) !!}


    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_edit')
        </div>

         <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_name', 'Company Name *', ['class' => 'control-label']) !!}
                    {!! Form::text('cmp_name', old('cmp_name'), ['class' => 'form-control', 'placeholder' => 'Company Name', 'required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_name'))
                        <p class="help-block">{{ $errors->first('cmp_name') }}</p>
                    @endif
                </div>
                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_streetname', 'Street', ['class' => 'control-label']) !!}
                    {!! Form::text('cmp_streetname', old('cmp_streetname'), ['class' => 'form-control', 'placeholder' => 'Street']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_streetname'))
                        <p class="help-block">{{ $errors->first('cmp_streetname') }}</p>
                    @endif
                </div>
                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_postalcode', 'Postal Code', ['class' => 'control-label']) !!}
                    {!! Form::text('cmp_postalcode', old('cmp_postalcode'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_postalcode'))
                        <p class="help-block">{{ $errors->first('cmp_postalcode') }}</p>
                    @endif
                </div>
                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_place', 'Place', ['class' => 'control-label']) !!}
                    {!! Form::text('cmp_place', old('cmp_place'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_place'))
                        <p class="help-block">{{ $errors->first('cmp_place') }}</p>
                    @endif
                </div>
                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_email', 'Email', ['class' => 'control-label']) !!}
                    {!! Form::text('cmp_email', old('cmp_email'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_email'))
                        <p class="help-block">{{ $errors->first('cmp_email') }}</p>
                    @endif
                </div>
                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_web', 'Web Url', ['class' => 'control-label']) !!}
                    {!! Form::text('cmp_web', old('cmp_web'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_web'))
                        <p class="help-block">{{ $errors->first('cmp_web') }}</p>
                    @endif
                </div>
                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_contact_no', 'Contact No.', ['class' => 'control-label']) !!}
                    {!! Form::text('cmp_contact_no', old('cmp_contact_no'), ['class' => 'form-control', 'placeholder' => 'Contact No.']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_contact_no'))
                        <p class="help-block">{{ $errors->first('cmp_contact_no') }}</p>
                    @endif
                </div>

                <div class="col-xs-12 form-group">
                    {!! Form::label('reminders_days', 'Reminder Emails (No of days. max 5 times)', ['class' => 'control-label']) !!}
                    {!! Form::text('reminders_days', old('reminders_days'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('reminders_days'))
                        <p class="help-block">{{ $errors->first('reminders_days') }}</p>
                    @endif
                </div>
                <div class="col-xs-12 form-group">
                    {!! Form::label('expired_days', 'Expiration Days', ['class' => 'control-label']) !!}
                    {!! Form::text('expired_days', old('expired_days'), ['class' => 'form-control', 'placeholder' => 'Contact No.']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('expired_days'))
                        <p class="help-block">{{ $errors->first('expired_days') }}</p>
                    @endif
                </div>
                
                <div class="col-xs-12 form-group hide">
                    {!! Form::label('cmp_chmbrcno', 'Chamber of Commerce', ['class' => 'control-label']) !!}
                    {!! Form::text('cmp_chmbrcno', old('cmp_chmbrcno'), ['class' => 'form-control', 'placeholder' => 'Chamber of Commerce']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_chmbrcno'))
                        <p class="help-block">{{ $errors->first('cmp_chmbrcno') }}</p>
                    @endif
                </div>

                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_vat_no', 'kvk nummer (chambair of commerce) ', ['class' => 'control-label']) !!}
                    {!! Form::text('cmp_vat_no', old('cmp_vat_no'), ['class' => 'form-control', 'placeholder' => 'VAT number']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_vat_no'))
                        <p class="help-block">{{ $errors->first('cmp_vat_no') }}</p>
                    @endif
                </div>

                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_bank_ac_no', 'BTW nummer', ['class' => 'control-label']) !!}
                    {!! Form::text('cmp_bank_ac_no', old('cmp_bank_ac_no'), ['class' => 'form-control', 'placeholder' => 'Bank number']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_bank_ac_no'))
                        <p class="help-block">{{ $errors->first('cmp_bank_ac_no') }}</p>
                    @endif
                </div>

                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_bank_ac_name', 'Banknummer', ['class' => 'control-label']) !!}
                    {!! Form::text('cmp_bank_ac_name', old('cmp_bank_ac_name'), ['class' => 'form-control', 'placeholder' => 'Bank Acount Name']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_bank_ac_name'))
                        <p class="help-block">{{ $errors->first('cmp_bank_ac_name') }}</p>
                    @endif
                </div>
                <!-- XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX -->
                <div class="col-xs-12 form-group">
                    <div class="col-xs-8 no-padding">
                        {!! Form::label('logo1_file','Logo 1',['class'=>'control-label']) !!}
                        {!! Form::file('logo1_file', ['class' => 'control-label']) !!} 
                        <p class="help-block"></p>
                        @if($errors->has('logo1_file'))
                            <p class="help-block">{{ $errors->first('logo1_file') }}</p>
                        @endif
                    </div>
                    @if(!empty($company->cmp_logo1))
                    <div class="col-xs-4 prv_image_small">
                        <img src="{{ url('/public/upload/'.$company->cmp_logo1) }}"></img>
                    </div>
                    @endif
                </div>

                <div class="col-xs-12 no-padding hide">
                    <div class="col-xs-8 no-padding">
                        {!! Form::label('logo2_file','Logo 2',['class'=>'control-label']) !!}
                        {!! Form::file('logo2_file', ['class' => 'control-label']) !!} 
                        <p class="help-block"></p>
                        @if($errors->has('logo2_file'))
                            <p class="help-block">{{ $errors->first('logo2_file') }}</p>
                        @endif
                    </div>
                    @if(!empty($company->cmp_logo2))
                    <div class="col-xs-4 prv_image_small">
                        <img src="{{ url('/public/upload/'.$company->cmp_logo2) }}"></img>
                    </div>
                    @endif
                </div>
                <!-- XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX --> 

                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_paid_inv_desc', 'Paid Invoice Description', ['class' => 'control-label']) !!}
                    {!! Form::textarea('cmp_paid_inv_desc',old('cmp_paid_inv_desc'),['class'=>'form-control', 'rows' => 5, 'cols' => 20,'id'=> 'cmp_paid_inv_desc']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_paid_inv_desc'))
                        <p class="help-block">
                            {{ $errors->first('cmp_paid_inv_desc') }}
                        </p>
                    @endif
                </div> 
                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_cashpaid_inv_desc', 'Cash Paid Invoice Description', ['class' => 'control-label']) !!}
                    {!! Form::textarea('cmp_cashpaid_inv_desc',old('cmp_cashpaid_inv_desc'),['class'=>'form-control', 'rows' => 5, 'cols' => 20,'id'=> 'cmp_cashpaid_inv_desc']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_cashpaid_inv_desc'))
                        <p class="help-block">
                            {{ $errors->first('cmp_cashpaid_inv_desc') }}
                        </p>
                    @endif
                </div> 
                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_unpaid_inv_desc', 'Unpaid Invoice Description', ['class' => 'control-label']) !!}
                    {!! Form::textarea('cmp_unpaid_inv_desc',old('cmp_unpaid_inv_desc'),['class'=>'form-control', 'rows' => 5, 'cols' => 20,'id'=> 'cmp_unpaid_inv_desc']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_unpaid_inv_desc'))
                        <p class="help-block">
                            {{ $errors->first('cmp_unpaid_inv_desc') }}
                        </p>
                    @endif
                </div> 
                
                <div class="col-xs-12 form-group">
                    {!! Form::label('cmp_partial_paid_inv_desc', 'Partitial payment amount', ['class' => 'control-label']) !!}
                    {!! Form::textarea('cmp_partial_paid_inv_desc',old('cmp_partial_paid_inv_desc'),['class'=>'form-control', 'rows' => 5, 'cols' => 20,'id'=> 'cmp_partial_paid_inv_desc']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cmp_partial_paid_inv_desc'))
                        <p class="help-block">
                            {{ $errors->first('cmp_partial_paid_inv_desc') }}
                        </p>
                    @endif
                </div> 

            </div>    
                    
                      
                  
        </div>
    </div>
    
    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

 