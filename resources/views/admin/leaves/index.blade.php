@extends('layouts.app')

@section('content')
@if ($message = Session::get('error'))
    <div class="col-sm-12">   
        <div class="note note-danger" role="alert">
          {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
        </div>
    </div>
@endif
    <div class="row">
    <div class="col-md-6"><h3 class="page-title"><i class="fa fa-user-circle ifont"></i> {{ $emp_general_info[0]->first_name }} @lang('quickadmin.leaves.title')</h3></div>
    <div class="col-md-6 tright">
    @can('leave_create')
    <p>
        <a href="{{ route('admin.employees_leaves.create',[$emp_general_info[0]->id]) }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan
    </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>
       {{--  <div class="col-sm-12" >
            <p style="font-weight: bold;color:red">Spacial Note:</p>
          If you have 1 day leave with different breaks, Suppose you have leave on 20 Jan, Your working hour is 09:00 - 18:00 and you have couple of breaks
            <br>
            1. 11.00-12.00<br>
            2. 14.00-15.00<br>
            so meaning in 1 day you have couple of breaks then you need to create a custom timing for this day, Leave 
            will work on either you have full day leave (09:00-18:00) or half day leave (09:00-14:00) or muliple day
            11 jan 14:00 pm to 14 jan (12:00) .
        </div>  --}}   
        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($leaves) > 0 ? 'datatable' : '' }} @can('leave_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('leave_delete')
                            <th class="hide" style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('quickadmin.leaves.fields.name')</th>
                        <th>@lang('quickadmin.leaves.fields.leave_date')  </th>
                        <th>Leave End Date  </th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($leaves) > 0)
                        @foreach ($leaves as $user)
                            <tr data-entry-id="{{ $user->id }}">
                                @can('leave_delete')                                 
                                    <td  class="hide" ></td>
                                @endcan
                                <td>{{ $user->leave_title }}</td>
                                <td>{{ $user->leave_date }}</td>
                                <td>{{ $user->leave_to_date }}</td>
                                
                                <td>
                                   
                                    @can('leave_edit')
                                 
                                   {{--  <a href="{{ route('admin.employees_leaves.edit',[$user->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a> --}}
                                   
                                    @endcan
                                    @can('leave_delete')
                                    
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.employees_leaves.destroy', $user->id])) !!}
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
 