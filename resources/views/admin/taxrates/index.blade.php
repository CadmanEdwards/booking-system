@extends('layouts.app')

@section('content')
    <h3 class="page-title">Tax Rate Class (Moneybrid)</h3>
  

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                   
                    <th>Name</th>
                    <th>Percentage</th>
                    <th>Tax Rate Type</th>
                    <th>Active</th>
                    
                    
                    {{-- <th>@lang('quickadmin.clients.fields.phone')</th>
                    <th>Location</th>
                    
                    <th>Therapy Name</th>
                    <th>Therapist Name</th>
                    <th>Room No</th> --}}
                    
                    
                    
                    {{-- <th>@lang('quickadmin.appointments.fields.comments')</th> --}}
                   
                </tr>
                </thead>
              
                <tbody>
                @if (count($appointments) > 0)
                    @foreach ($appointments as $appointment)
                        <tr data-entry-id="{{ $appointment->id }}">
                           
                            <td>{{$appointment->name }}</td>
                            <td>{{ $appointment->percentage }}</td>
                            <td>{{ $appointment->tax_rate_type }}</td>
                            
                            <td>{{ $appointment->active==1 ? 'Enable' : 'Disable' }}</td>
                            {{-- <td>{{ isset($appointment->client) ? $appointment->client->email : '' }}</td> --}}
                           
                            
                            {{-- <td>{!! $appointment->comments !!}</td> --}}
                           
                           
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




@endsection