@extends('layouts.app')

@section('content')
    <div class="row">
    <div class="col-md-6"><h3 class="page-title"><i class="fa fa-user-circle ifont"></i>  Pages</h3></div>
    <div class="col-md-6 tright">
    <!-- @can('user_create')
    <p>
        <a href="{{ route('admin.rooms.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan -->
    </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Pages
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($pages) > 0 ? 'datatable' : '' }} @can('page_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('page_delete')
                            <th class="hide" style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>Page Title</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($pages) > 0)
                        @foreach ($pages as $user)
                            <tr data-entry-id="{{ $user->id }}">
                                @can('page_delete')                                 
                                    <td  class="hide" ></td>
                                @endcan

                                <td>{{ $user->page_subject }}</td>
                                
                                <td>
                                   
                                    @can('page_edit')
                                    
                                    <a href="{{ route('admin.pages.edit',[$user->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                   
                                    @endcan
                                    @can('page_delete')
                                    @if($user->id!=1)
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.pages.destroy', $user->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endif
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
 