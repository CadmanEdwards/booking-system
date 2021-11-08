@extends('layouts.app')

@section('content')
    <div class="row">
    <div class="col-md-6"><h3 class="page-title"><i class="fa fa-user ifont"></i>  @lang('quickadmin.employees.title')</h3></div>
    <div class="col-md-6 tright">
    @can('employee_create')
    <p>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan
    </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading bold">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($employees) > 0 ? 'datatable' : '' }} @can('employee_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('employee_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan 
                        <th width="20%">@lang('quickadmin.employees.fields.first-name')</th>
                        <th width="20%">@lang('quickadmin.employees.fields.last-name')</th>
                        <th width="15%">@lang('quickadmin.employees.fields.phone')</th>
                        <th width="20%">@lang('quickadmin.employees.fields.email')</th> 
                        <!-- <th>MoneyBird Key(Doc Id)</th> -->
                        <th width="25%">&nbsp;</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($employees) > 0)
                        @foreach ($employees as $employee)

                            <tr data-entry-id="{{ $employee->id }}" @if($employee->deleted_at) style="background: lightcoral" @endif>
                                @can('employee_delete')
                                    <td></td>
                                @endcan 
                                <td>{{ $employee->first_name }}</td>
                                <td>{{ $employee->last_name }}</td>
                                <td>{{ $employee->phone }}</td>
                                <td>{{ $employee->email }}</td> 								
                                <!-- <td>{{ $employee->moneybird_key }}</td> -->                                 
                                <td>
                                    <div class="btn-group">
                                    @can('employee_view')
                                    <a href="{{ route('admin.employees.show',[$employee->id]) }}" class="btn btn-xs btn-primary" title="@lang('quickadmin.qa_view')">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('employee_edit')
                                    <a href="{{ route('admin.employees.edit',[$employee->id]) }}" class="btn btn-xs btn-info" title="@lang('quickadmin.qa_edit')">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('employee_delete')

                                            @if($employee->deleted_at)
                                                @php
                                                $text = 'quickadmin.qa_are_you_sure';
                                                if($employee->appointments > 0){
                                                     $text = 'quickadmin.qa_are_you_sure_new';
                                                }
                                                @endphp
                                                {!! Form::open(array(
                                           'style' => 'display: inline-block;',
                                           'method' => 'DELETE',
                                           'onsubmit' => "return confirm('".trans($text)."');",
                                           'route' => ['admin.employees.destroy', $employee->id])) !!}
                                                <button class="btn btn-xs btn-danger" type="submit" value="Delete"><i class="fa fa-trash"></i></button>

                                            <!-- {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!} -->
                                                {!! Form::close() !!}
                                            <a title="Enable Therapist"   href="{{ route('admin.therapist.enable',['id' => $employee->id]) }}" class="btn btn-xs btn-success"  ><i class="fa fa-unlock"></i></a>

                                            @else
                                                <a title="Disable Therapist"   href="{{ route('admin.therapist.disable',[ 'id' => $employee->id]) }}" class="btn btn-xs btn-danger"  ><i class="fa fa-lock"></i></a>
                                            @endif
                                        @endcan



                                    <a title="Invoice" alt="Invoice" href="javascript:void(0)" class="btn btn-xs btn-success" onclick="viewinvoice({{ $employee->id }})"><i class="fa fa-eur"></i></a>


                                    @can('employee_service')
                                    <a title="@lang('quickadmin.qa_service')" href="{{ route('admin.employees.services',[$employee->id]) }}" class="btn btn-xs btn-primary">
                                        <i class="fa fa-cutlery"></i>
                                     </a>
                                    @endcan

                                    
                                     @can('leave_access')
                                    <a title="@lang('quickadmin.qa_leave')" alt="@lang('quickadmin.qa_leave')" href="{{ route('admin.leave.leavelist',[$employee->id]) }}" class="btn btn-xs btn-info">
                                        <i class="fa fa-calendar"></i>
                                    </a>
                                    @endcan
                                    
                                    

                                     @can('employee_custom_timing_access')
                                    <a title="@lang('quickadmin.qa_custom_timing')" alt="@lang('quickadmin.qa_custom_timing')" href="{{ route('admin.employeecustomtiming.employeecustomtiminglist',[$employee->id]) }}" class="btn btn-xs btn-warning">
                                        <i class="fa fa-calendar-plus-o"></i>
                                    </a>
                                    @endcan
                                    </div>
                                   


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
@stop

@section('javascript') 
    <script>
        function viewinvoice(empid)
        {
            window.open("{{ url('admin') }}/employees/pview/"+empid, "socialPopupWindow",
                "location=no,width=600,height=600,scrollbars=yes,top=100,left=700,resizable = no");
        }
        @can('employee_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.employees.mass_destroy') }}';
        @endcan

    </script>
@endsection