@extends('layouts.app')

@section('content')
    <div class="row">
    <div class="col-md-6"><h3 class="page-title"><i class="fa fa-user-circle ifont"></i> {{ $emp_general_info[0]->first_name }} @lang('quickadmin.customtiming.title')</h3></div>
    <div class="col-md-6 tright">
    @can('employee_custom_timing_create')
    <p>
        <a href="{{ route('admin.employees_customtiming.create',[$emp_general_info[0]->id]) }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan
    </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($customtiming) > 0 ? 'datatable' : '' }} @can('employee_custom_timing_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('employee_custom_timing_delete')
                            <th class="hide" style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('quickadmin.customtiming.fields.date')</th>
                        <th>@lang('quickadmin.customtiming.fields.start-time')  </th>
                        <th>@lang('quickadmin.customtiming.fields.finish-time')  </th>
                        <th>Availablity</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($customtiming) > 0)
                        @foreach ($customtiming as $user)
                            <tr data-entry-id="{{ $user->id }}">
                                @can('leave_delete')                                 
                                    <td  class="hide" ></td>
                                @endcan
                                <td>{{ $user->date }}</td>
                                <td>{{ $user->start_time }}</td>
                                <td>{{ $user->end_time }}</td>
                                <td>{{ $user->timing_type }}</td>
                                
                                <td>
                                   
                                    @can('employee_custom_timing_edit')
                                 
                                   {{--  <a href="{{ route('admin.employees_customtiming.edit',[$user->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>      --}}
                                   
                                    @endcan
                                    @can('employee_custom_timing_delete')
                                    
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.employees_customtiming.destroy', $user->id])) !!}
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
@stop
 