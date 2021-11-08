@extends('layouts.app')

@section('content')
    <h3 class="page-title"><i class="fa fa-user-circle ifont"></i>  {{-- @lang('quickadmin.emailtemplates.title') --}} {{ str_replace('_', ' ', strtoupper($emailtemplates->email_type)) }}</h3>
    
    {!! Form::model($emailtemplates, ['method' => 'PUT', 'route' => ['admin.emailtemplates.update', $emailtemplates->id], 'files' => true]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_edit')  
        </div>

         <div class="panel-body">
            <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('email_user_type', 'User Type', ['class' => 'control-label']) !!}
                    {!! Form::select('email_user_type', $email_user_type, old('email_user_type'), ['class' => 'form-control select2', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email_user_type'))
                        <p class="help-block">
                            {{ $errors->first('email_user_type') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('subject', 'Subject*', ['class' => 'control-label']) !!}
                    {!! Form::text('email_subject', old('email_subject'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('email_id', 'Email Id', ['class' => 'control-label']) !!}
                    {!! Form::text('email_id', old('email_id'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email_id'))
                        <p class="help-block">
                            {{ $errors->first('email_id') }}
                        </p>
                    @endif
                </div>  
                <div class="col-xs-6 form-group">
                    {!! Form::label('file_id', 'File Id', ['class' => 'control-label']) !!}
                    {!! Form::file('attachment', old('attachment'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('attachment'))
                        <p class="help-block">
                            {{ $errors->first('attachment') }}
                        </p>
                    @endif

                    @if($emailtemplates->attachment!="")
                    <p>{{ $emailtemplates->attachment }} <a href="{{ route('admin.emailtemplates.rmatch',[$emailtemplates->id]) }}" onclick=" return confirm('Are you sure to remove this attachment.?')" ><i class="fa fa-remove"></i></a></p>
                    @endif
                </div>
                  <div class="col-xs-12 form-group">
                    {!! Form::label('email_content', 'Email Content*', ['class' => 'control-label']) !!}

                      <textarea class="form-control edi" rows="10" id="edi" cols="60" name="email_content" id="email_content">{!!  $emailtemplates->email_content !!}</textarea>
                    <p class="help-block"></p>
                    @if($errors->has('email_content'))
                        <p class="help-block">
                            {{ $errors->first('email_content') }}
                        </p>
                    @endif
                </div> 
                <p style="margin-left:20px">

                    <span style="font-weight:bold;color:red">These are shortcodes which you can use while creating email tempalte<br/></span>
                    <ul class="tags">
                        <li>Customer name = {clientname}</li>
<li>booking time and date = {booking_date}</li>
<li>therapist name = {therapistname}</li>
<li>therapy name = {thrapyname}</li>
<li>therapist title = {therapistitle}</li>
<li>therapist telephone number = {therapisttelephone}</li>
<li>location streetname = {locationstreetname}</li>
<li>location city = {location}</li>
<li>location address = {location_address}</li>
<li>route direction to location = {route_directions}</li>
<li>therapist registrations = {therapistregistrations}</li>
<li>therapy discription = {therapistdes}</li>
<li>therapy discription2 = {therapistdes2}</li>
<li>Customer Phone = {customertelephonenumber}</li>
<li>Customer Email = {customeremail}</li>
<li>Start Time = {booking_time}</li>
<li>Appointment Verify Link For Therapist Email = {appointmentverifylink}</li>
<li>Therapist e-mailadres = {therapistemail}</li>
<li>Go to calandar booking date = {r_calandar_booking_date}</li>
<li>Booking view = {go_booking_view}</li>
<li>Email Verify Link Customer = {customeremailverifylink}</li>
                    </ul> 
                </p>
                @if($emailtemplates->id>22 && $emailtemplates->id<27)
                <div class="col-xs-12 form-group">
                    {!! Form::label('email_content_h', 'Invoice Template *', ['class' => 'control-label']) !!}
                    {!! Form::textarea('email_content_h',old('email_content_h'),['class'=>'form-control', 'rows' => 10, 'cols' => 60]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email_content_h'))
                        <p class="help-block">
                            {{ $errors->first('email_content_h') }}
                        </p>
                    @endif
                    <button onclick="modalsearch()" type="button" class="btn btn-success">View Original Template</button>
                </div> 

                @endif
                <div class="col-xs-12 form-group">
                    <p style="margin-left:20px">
                    
                    
                    <span style="font-weight:bold;color:red">These are shortcodes which you can use while creating <strong>INVOICE</strong> email tempalte <br/></span>
                        <ul class="tags">
                            <li>[INVOICE NUMBER]</li>
                            <li>Due Amount = [DUE AMOUNT]</li>
                            <li>invoice date = [DATE OF START]</li>
                            <li>Due Date = [DATE OF BOOKING]</li>
                            <li>Invoice Amount = [TOTALAMOUNT]</li>
                            <li>[COMPANY LOGO IMAGE]</li>
                            <li>[COMPANY NAME]</li>
                            <li>[COMPANY WEB]</li>
                            <li>[COMPANY PHONE]</li>
                            <li>[COMPANY STREET]</li>
                            <li>[COMPANY POSTCODE]</li>
                            <li>[COMPANY PLACE]</li>
                            <li>[COMPANY EMAIL]</li>
                            <li>[COMPANY VAT NUMBER]</li>
                            <li>[COMPANY BANK AC NUMBER]</li>
                            <li>[COMPANY BANK NAME]</li>
                            <li>[THARAPIST IMAGE1]</li>
                            <li>[THARAPIST IMAGE2]</li>
                            <li>[THARAPIST AGB_CODE]</li>
                            <li>[THARAPIST BEHANDELAAR]</li>
                            <li>[THARAPIST RBCZ_NUMBER]</li>
                            <li>[THARAPIST ADDRESS]</li>
                            <li>[THARAPIST CITY]</li>
                            <li>[PAYMENT_URL]</li>
                            <li>[CUSTOMER ADDRESS]</li>
                            <li>[CUSTOMER POSTCODE]</li>
                            <li>[CUSTOMER CITY]</li>
                            <li>[CUSTOMER DATEOFBIRTH]</li>
                            <li>Doctor Name = [refferer]</li>
                            <li>[DOCTOR EMAIL ADDRESS]</li>
                            <li>[DOCTOR PHONE]</li>
                            <li>[DOCTOR Address]</li>
                            <li>[DOCTOR NUMBER]</li>
                            <li>[DOCTOR POSTCODE]</li>
                            <li>[DOCTOR CITY]</li>
                            <li>[DOCTOR NUMBER]</li>
                            <li>[EXTRA ADDED]</li>
                            <li>CLIENT REPORT PROBLEEM</li>
                            <li>CLIENT REPORT BEREIKEN</li>
                            <li>CLIENT REPORT WAARNEMINGEN</li>
                            <li>CLIENT REPORT PROCEDIAGNOSE</li>
                            <li>CLIENT REPORT FYSIEKE</li>
                            <li>CLIENT REPORT SAMENVATTING</li>
                            <li>{first therapy session client}</li>
                            <li>{all booking dates}</li>
                        </ul>
                    </p>
                </div>
        </div>
    </div>
    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
    <div class="modal fade" id="SearchModal" style="overflow:hidden;" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="closebtn btn  " data-dismiss="modal">
                      <span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                    
                </div>
                <div id="modalBody" class="modal-body">
                    {!! $invoice_template !!}  
                </div>
            </div>
        </div>
        </div>
@stop
@section('css')
<style type="text/css">
.tags{width: 100%}
.tags li{width: 100%}

</style>
@endsection       
@section('javascript')
            <script src="https://cdn.tiny.cloud/1/mahjowjx5cbjsjflu83u2suiolbmvkxa7e5ntqflqrx52tgt/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
{{--            <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>--}}
            <script>
                tinymce.init({
                    selector: 'textarea',
                  //  plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak code',
                    plugins: 'print preview  importcss tinydrive searchreplace autolink autosave save directionality  visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
                    toolbar_mode: 'floating',
                    height: 500
                });

                // CKEDITOR.replace( 'textarea' );
                function modalsearch(){
                    $('#SearchModal').modal('show')
                }
            </script>
@endsection       

