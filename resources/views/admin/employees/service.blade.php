@extends('layouts.app')

@section('content')
    <div class="row">
    <div class="col-md-6"><h3 class="page-title"><i class="fa fa-user ifont"></i>  @lang('quickadmin.employees-service.title')</h3></div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading bold">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.employees.fields.first-name')</th>
                            <td>{{ $employee->first_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.employees.fields.last-name')</th>
                            <td>{{ $employee->last_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.employees.fields.phone')</th>
                            <td>{{ $employee->phone }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.employees.fields.email')</th>
                            <td>{{ $employee->email }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 tright">
                    @can('employee_service_create')
                    <p>
                        <a href="{{ route('admin.employees_services.create',[$employee->id]) }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
                        
                    </p>
                    @endcan
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    
<li role="presentation" class="active"><a href="#services" aria-controls="services" role="tab" data-toggle="tab">Services</a></li> 
</ul>
<!-- Tab panes -->
<div class="tab-content">    
<div role="tabpanel" class="tab-pane active" id="services">
<table class="table table-bordered table-striped {{ count($employee_services) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            {{-- <th>@lang('quickadmin.employees-service.fields.employee')</th>  --}}
            <th>@lang('quickadmin.employees-service.fields.service')</th>
            <th>Therapis Title</th>
            {{-- <th>@lang('quickadmin.employees-service.fields.price')</th>
            <th>@lang('quickadmin.employees-service.fields.weekend_price')</th> --}}
            <th>@lang('quickadmin.employees-service.fields.discount')</th>
            {{-- <th>@lang('quickadmin.employees-service.fields.tax')</th> --}}

            <th>&nbsp;</th>
        </tr>
    </thead>

    <tbody>
        
        @if (count($employee_services) > 0)
            @foreach ($employee_services as $service)  
                <tr data-entry-id="{{ $service->id }}">
                   {{--  <td>{{ $service->employee->first_name or '' }}{{ isset($service->employee) ? $service->employee->last_name : '' }}</td> --}}                    

                    <td>{{ $service->services->name}} </td>
                    <td>{{ $service->moneybird_username }}</td>
                  {{--   <td>{{ $service->weekend_price }}</td> --}}
                    <td>{{ $service->discount }}</td>
                    {{-- <td>{{ $service->tax }}</td>  --}}
                    <td> 
                        @can('employee_service_edit')
                        <a href="{{ route('admin.employees_services.edit',[$service->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                        @endcan
                        @can('employee_service_delete')
                        {!! Form::open(array(
                            'style' => 'display: inline-block;',
                            'method' => 'DELETE',
                            'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                            'route' => ['admin.employees_services.destroy', $service->id])) !!}
                        {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                        {!! Form::close() !!}
                        @endcan
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8">@lang('quickadmin.qa_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>
 
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.employees.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop