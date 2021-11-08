@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <style>
        .swiper-container {
            width: 100%;
            height: 100%;
        }
        .swiper-wrapper{
            padding-bottom: 30px;
        }

        .swiper-container-horizontal>.swiper-pagination-bullets, .swiper-pagination-custom, .swiper-pagination-fraction{
            bottom: 0px !important;
        }
        .swiper-container {
            width: 100%;
            height: 100%;
        }

        .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #fff;

            /* Center slide text vertically */
            display: -webkit-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
        }

        .swiper-pagination-bullet {
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            font-size: 12px;
            color: #000;
            opacity: 1;
            background: rgba(0, 0, 0, 0.2);
        }

        .swiper-pagination-bullet-active {
            color: #fff;
            background: #007aff;
        }
    </style>
    <div class="row">
        <div class="col-md-6"><h3 class="page-title"><i class="fa fa-users ifont"></i>  With Out Moneybird</h3></div>
        <div class="col-md-12 ">
            @if(Session::has('msg'))
                <div class="alert alert-success">
                    <ul>
                        <li>{!! \Session::get('msg') !!}</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading bold">
            @lang('quickadmin.qa_list')
        </div>
        <div class="panel-body table-responsive">

            <table id="without" class="table table-bordered table-striped {{ count($clients) > 0 ? 'datatable' : '' }} {{ count($clientsOther) > 0 ? 'datatable' : '' }} @can('client_delete') dt-select @endcan">
                <thead>
                <tr>
                    @can('client_delete')
                        <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
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
                @if (count($clients) > 0)
                    @foreach ($clients as $client)
                        <tr data-entry-id="{{ $client->id }}">
                            @can('client_delete')
                                <td></td>
                            @endcan

                            <td>{{ $client->first_name }}</td>
                            <td>{{ $client->last_name }}</td>
                            <td>{{ $client->phone }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ date('d-m-Y',strtotime($client->created_at)) }}</td>
                            <td>
                                @if(!empty($client->comment_log))
                                    <ul class="comment_log_view">
                                        {!! $client->comment_log !!}
                                    </ul>
                                @endif
                            </td>
                            <td>{{ getParentDetails($client->parent_id) }}</td>

                            <td>{{ $client->moneybird_contact_id }}</td>
                            <td>
                                @can('client_view')
                                    <a href="{{ route('admin.client.showwithoutmoneybird',[$client->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                @endcan
                                @can('client_edit')
                                    <a href="{{ route('admin.clients.editwithoutmoneybird',[$client->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                @endcan


                                @if($user_role_id == 2)
                                    @can('oappointment_create')
                                        <a href="{{ route('admin.opertorappointments.create',['client_id' => $client->id]) }}" class="btn btn-xs btn-info">
                                            <i class="fa fa-calendar"></i>
                                            <span class="title">@lang('quickadmin.qa_add_new') Booking</span>
                                        </a>
                                    @endcan
                                @else
                                    @can('appointment_create')
                                        <a href="{{ route('admin.appointments.create',['client_id' => $client->id]) }}"
                                           class="btn btn-xs btn-info"><i class="fa fa-calendar"></i><span class="title">@lang('quickadmin.qa_add_new') Booking</span></a>
                                    @endcan
                                @endif
                                 <a href="{{ route('admin.client_vcard',['first_name' => $client->first_name,'last_name' => $client->last_name,'phone' => $client->phone,'email' => $client->email]) }}" class="btn btn-xs btn-info" title="Vcard"><i class="fa fa-id-badge"></i></a>


                                @can('client_delete')
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.clients.destroywithoutmoneybird', $client->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @endif

                @if (count($clientsOther) > 0)
                    @foreach ($clientsOther as $client)
                        <tr data-entry-id="{{ $client->id }}">
                            @can('client_delete')
                                <td></td>
                            @endcan

                            <td>{{ $client->first_name }}</td>
                            <td>{{ $client->last_name }}</td>
                            <td>{{ $client->phone }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ date('d-m-Y',strtotime($client->created_at)) }}</td>
                            <td>
                                @if(!empty($client->comment_log))
                                    <ul>
                                        {!! $client->comment_log !!}
                                    </ul>
                                @endif
                            </td>
                            <td>{{ getParentDetails($client->parent_id) }}</td>

                            <td>{{ $client->moneybird_contact_id }}</td>
                            <td>
                                @can('client_view')
                                    <a href="{{ route('admin.clients.show',[$client->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                @endcan
                                @can('client_edit')
                                    <a href="{{ route('admin.clients.edit',[$client->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                @endcan

                                @can('oappointment_create')
                                    <a href="{{ route('admin.opertorappointments.create',['client_id' => $client->id]) }}" class="btn btn-xs btn-info">
                                        <i class="fa fa-calendar"></i>
                                        <span class="title">@lang('quickadmin.qa_add_new') Booking</span>
                                    </a>
                                @endcan




                                @can('client_delete')
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.clients.destroy', $client->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="emailModal" style="overflow:hidden;" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                    <h4 id="modalTitle" class="modal-title"></h4>
                </div>
                <div  class="modal-body">
                    <div class="swiper-container">
                        <div class="swiper-wrapper" id="emailModalBody">


                        </div>
                        <!-- Add Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="sendMail" class="btn btn-primary">Send Mail(s)</button>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <script>

        @can('client_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.clients.mass_destroy') }}';
        @endcan

            window.route_mass_send_email = '{{ route('admin.clients_mass_email_send') }}';
        window.email_templates = {!! $emailTemplate !!};


    </script>
@endsection