@extends('layouts.app')

@section('content')
    <h3 class="page-title"><i class="fa fa-users ifont"></i>  Employee Excel Upload</h3>
    {!! Form::open(['method' => 'POST','enctype' => 'multipart/form-data' ,'onsubmit' => 'uploadExcelCustomer()','route' => ['admin.clients.post.excel']]) !!}

    <div class="panel panel-default">
        <div class="panel-heading bold">
            Employee Excel Upload
        </div>

        <div class="panel-body">

            <div class="row">


                <div class="col-xs-6 form-group email">
                    {!! Form::label('file', 'File', ['class' => 'control-label']) !!}
                    {!! Form::file('file', ['class' => 'form-control input-group ','id' => 'excelFileInput', 'placeholder' => '',
                    'accept' =>  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('file'))
                        <p class="help-block">
                            {{ $errors->first('file') }}
                        </p>
                    @endif
                </div>



            </div>

        </div>
    </div>

    {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}

    <div class="row" style="margin-top: 50px">
        <div class="col-md-6">

            <div class="progress">
                <div class="progress-bar" id="progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>

@stop
@section('javascript')




@stop

