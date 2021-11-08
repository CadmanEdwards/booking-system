@extends('layouts.app')

@section('content')
    <h3 class="page-title"><i class="fa fa-user-circle ifont"></i> @lang('quickadmin.emailtemplates.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.emailtemplates.store'], 'files' => true  ]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_create')
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
                </div> 
                  <div class="col-xs-12 form-group">
                    {!! Form::label('email_content', 'Email Content*', ['class' => 'control-label']) !!}


                      <textarea class="form-control edi" rows="10" id="edi"  cols="60" name="email_content" id="email_content">{!! old('email_content') !!}</textarea>
            <p class="help-block"></p>
                    @if($errors->has('email_content'))
                        <p class="help-block">
                            {{ $errors->first('email_content') }}
                        </p>
                    @endif
                </div> 
                <p style="margin-left:20px">
                    <span style="font-weight:bold;color:red">These are shortcodes which you can use while creating email tempalte<br/></span>
                     1 Customer name = {clientname} <br/>
                     2 booking time and date = {booking_date} <br/>
                     3 therapist name = {therapistname} <br/>
                     4 therapy name = {thrapyname} <br/>
                     5 therapist title = {therapistitle} <br/>
                     6 therapist telephone number = {therapisttelephone} <br/>
                     7 location streetname = {locationstreetname} <br/>
                     8 location city = {location} <br/>
                     9 location address = {location_address} <br/>
                     10 route direction to location = {route_directions} <br/>
                     11 therapist registrations = {therapistregistrations} <br/>
                     12 therapy discription = {therapistdes} <br/>
                     13 therapy discription2 = {therapistdes2} <br/>
                     14 Customer Phone = {customertelephonenumber} <br/>
                     15 Customer Email = {customeremail} <br/>
                     16 Start Time = {booking_time} <br/>
                     17 Appointment Verify Link For Therapist Email = {appointmentverifylink} <br/>
                     18 Therapist e-mailadres = {therapistemail} <br/>
                     19 Go to calandar booking date = {r_calandar_booking_date} <br/>
                     20 Booking view = {go_booking_view} <br/>
                     21 Email Verify Link Customer = {customeremailverifylink} <br/>

                   </p>
            </div>
             

        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop
@section('javascript')
    <script src="https://cdn.tiny.cloud/1/mahjowjx5cbjsjflu83u2suiolbmvkxa7e5ntqflqrx52tgt/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
{{--    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>--}}
   <script>
       tinymce.init({
           selector: 'textarea',
         //  plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak code',
           plugins: 'print preview  importcss tinydrive searchreplace autolink autosave save directionality  visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
           toolbar_mode: 'floating',
           height: 500
       });
       // CKEDITOR.replace( 'edi' );
   </script>
@endsection

