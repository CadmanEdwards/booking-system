@extends('layouts.app')

@section('content')
    <h3 class="page-title"><i class="fa fa-users ifont"></i> Doctor</h3>

    <div class="panel panel-default">
        <div class="panel-heading bold">
            Doctor View
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.clients.fields.first-name')</th>
                            <td>{{ $doctor->first_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.last-name')</th>
                            <td>{{ $doctor->last_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.phone')</th>
                            <td>{{ $doctor->phone }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.email')</th>
                            <td>{{ $doctor->email }}</td>
                        </tr>
                        <tr>
                            <th>Created Date</th>
                            <td>{{ date('d-m-Y',strtotime($doctor->created_at)) }}</td>
                        </tr>
                    </table>
                </div>
                 <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.clients.fields.dob')</th>
                            <td>{{ $doctor->dob }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.house_number')</th>
                            <td>{{ $doctor->house_number }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.address')</th>
                            <td>{{ $doctor->address }}</td>
                        </tr>
                        <tr>
                            <th>Comment</th>
                            <td>{{ $doctor->comment }}</td>
                        </tr>
                    </table>
                </div>
            </div><!-- Nav tabs -->
 
 

            <p>&nbsp;</p>

            <a href="{{ route('admin.doctors.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop