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
@if ($message = Session::get('success'))
<div class="note note-success">
    <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{{ $message }}</strong>
</div>
@endif
     <div class="row">
    <div class="col-md-6"><h3 class="page-title"><i class="fa fa-hourglass ifont"></i>  @lang('quickadmin.services.title')</h3></div>
    <div class="col-md-6 tright">
    @can('service_create')
    <p>
        <a href="{{ route('admin.services.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan
    </div>
    </div>

    <div class="panel panel-default">

        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($services) > 0 ? 'datatable' : '' }} @can('service_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('service_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('quickadmin.services.fields.name')</th> 
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($services) > 0)
                        @foreach ($services as $service)
                            <tr data-entry-id="{{ $service->id }}">
                                @can('service_delete')
                                    <td></td>
                                @endcan

                                <td>{{ $service->name }}</td> 
                                <td>
                                    {{-- @can('service_view')
                                    <a href="{{ route('admin.services.show',[$service->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                    @endcan --}}
                                    @can('service_edit')
                                    <a href="{{ route('admin.services.edit',[$service->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('service_delete')
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.services.destroy', $service->id])) !!}
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

@section('javascript') 
    {{-- <script>
        @can('service_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.services.mass_destroy') }}';
        @endcan

    </script> --}}
@endsection