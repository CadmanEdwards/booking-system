@extends('layouts.app')

@section('content')
    <h3 class="page-title"><i class="fa fa-users ifont"></i> @lang('quickadmin.clients.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading bold">
            @lang('quickadmin.qa_view')
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.clients.fields.first-name')</th>
                            <td>{{ $client->first_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.last-name')</th>
                            <td>{{ $client->last_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.phone')</th>
                            <td>{{ $client->phone }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.email')</th>
                            <td>{{ $client->email }}</td>
                        </tr>
                        <tr>
                            <th>Company Name</th>
                            <td>{{ $client->company_name }}</td>
                        </tr>
                        <tr>
                            <th>Created Date</th>
                            <td>{{ date('d-m-Y',strtotime($client->created_at)) }}</td>
                        </tr>
                    </table>
                </div>
                 <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.clients.fields.dob')</th>
                            <td>{{ $client->dob }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.house_number')</th>
                            <td>{{ $client->house_number }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.address')</th>
                            <td>{{ $client->address }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.clients.fields.postcode')</th>
                            <td>{{ $client->postcode }}</td>
                        </tr> 
                        <tr>
                            <th>City</th>
                            <td>{{ $client->city_name }}</td>
                        </tr>
                        <tr>
                            <th>Comment</th>
                            <td>
                                @if(!empty($client->comment_log))
                                <ul class="comment_log_view">
                                    {!! $client->comment_log !!}
                                </ul>        
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    
<li role="presentation" class="active"><a href="#appointments" aria-controls="appointments" role="tab" data-toggle="tab">Appointments</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    
<div role="tabpanel" class="tab-pane active" id="appointments">
<table class="table table-bordered table-striped {{ count($appointments) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('quickadmin.appointments.fields.client')</th>
                        <th>@lang('quickadmin.clients.fields.last-name')</th>
                        <th>@lang('quickadmin.clients.fields.phone')</th>
                        <th>@lang('quickadmin.clients.fields.email')</th>
                        <th>@lang('quickadmin.appointments.fields.employee')</th>
                        <th>@lang('quickadmin.employees.fields.last-name')</th>
                        <th>@lang('quickadmin.appointments.fields.start-time')</th>
                        <th>@lang('quickadmin.appointments.fields.finish-time')</th>
                        <th>@lang('quickadmin.appointments.fields.comments')</th>
                        <th>&nbsp;</th>
        </tr>
    </thead>

    <tbody>
        @if (count($appointments) > 0)
            @foreach ($appointments as $appointment)
                <tr data-entry-id="{{ $appointment->id }}">
                    <td>{{ $appointment->client->first_name or '' }}</td>
<td>{{ isset($appointment->client) ? $appointment->client->last_name : '' }}</td>
<td>{{ isset($appointment->client) ? $appointment->client->phone : '' }}</td>
<td>{{ isset($appointment->client) ? $appointment->client->email : '' }}</td>
                                <td>{{ $appointment->employee->first_name or '' }}</td>
<td>{{ isset($appointment->employee) ? $appointment->employee->last_name : '' }}</td>
                                <td>{{ $appointment->start_time }}</td>
                                <td>{{ $appointment->finish_time }}</td>
                                <td>{!! $appointment->comments !!}</td>
                                <td>
                                    @can('appointment_view')
                                    <a href="{{ route('admin.appointments.show',[$appointment->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                    @endcan
                                    @can('appointment_edit')
                                    <a href="{{ route('admin.appointments.edit',[$appointment->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('appointment_delete')
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.appointments.destroy', $appointment->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="9">@lang('quickadmin.qa_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.clients.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop