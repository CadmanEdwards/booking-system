@extends('layouts.app')

@section('content')
    <div class="row">
    <div class="col-md-6"><h3 class="page-title"><i class="fa fa-user ifont"></i>  @lang('quickadmin.emailtemplates.title')</h3></div>
    <div class="col-md-6 tright">
    @can('emailtemplate_create')
    <p>
        <a href="{{ route('admin.emailtemplates.create') }}" class="btn btn-success">
            @lang('quickadmin.qa_add_new')
        </a>
    </p>
    @endcan
    </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading bold">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($emailtemplates) > 0 ? 'datatable' : '' }} ">
                <thead>
                    <tr>
                       
                        <th>@lang('quickadmin.emailtemplates.fields.subject')</th>
                        <th>@lang('quickadmin.emailtemplates.fields.email_type')</th>
                        
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($emailtemplates) > 0)
                        @foreach ($emailtemplates as $employee)
                            <tr data-entry-id="{{ $employee->id }}">
                               
                                <td>{{ $employee->email_subject }}</td>
                                <td>{{ $employee->email_type }}</td>
                                <td>
                                  
                                    @can('emailtemplate_edit')
                                    <a href="{{ route('admin.emailtemplates.edit',[$employee->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('emailtemplate_delete')
                                        @if($employee->email_type=="")
                                            {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.emailtemplates.destroy', $employee->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                        @endif
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
@stop

