@extends('layouts.app')

@section('content')
    <div class="row">
    <div class="col-md-6"><h3 class="page-title"><i class="fa fa-users ifont"></i>  @lang('quickadmin.clients.title')</h3></div>
    <div class="col-md-6 tright">
    @can('client_create')
    <p>
        <a href="{{ route('admin.clients.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        
    </p>
    @endcan

    </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading bold">
            @lang('quickadmin.qa_list')
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped datatable1">
                <thead>
                    <tr>
                        @can('client_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" name="select_all" /></th>
                        @endcan

                        <th>@lang('quickadmin.clients.fields.first-name')</th>
                        <th>@lang('quickadmin.clients.fields.last-name')</th>
                        <th>@lang('quickadmin.clients.fields.phone')</th>
                        <th>@lang('quickadmin.clients.fields.email')</th>
                        <th>Created Date </th>
                        <th>Comment </th>
                        <th>Parent Name</th>
                        <th>Money Bird Contact Id</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                
                <tbody>
                  
                </tbody> 
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        var handleCheckboxes = function (html, rowIndex, colIndex, cellNode) {
        var $cellNode = $(cellNode);
        var $check = $cellNode.find(':checked');
        return ($check.length) ? ($check.val() == 1 ? 'Yes' : 'No') : $cellNode.text();
    };
        window.route_all_data = '{{ url("admin/get-client-datatable") }}';
        @can('client_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.clients.mass_destroy') }}';
        @endcan
       
        window.route_mass_send_email = '{{ route('admin.clients_mass_email_send') }}';
        window.email_templates = {!! $emailTemplate !!};

     

           $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    /*
    var table = $('.datatable1').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url("admin/get-client-datatable") }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'detail', name: 'detail'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });*/
  
 </script>
@endsection