@extends('layouts.app')
<!-- https://flutter.dev 
https://ionicframework.com/start#basics
-->
@section('content')
 <style type="text/css">
  .closebtn {
    border: 1px solid #red;
    border-radius: 5px;
    float:right;
  }
  
</style>
    <h3 class="page-title">Invoices</h3>
    @can('invoice_create')
        <!-- <p>
            <a href="{{ route('admin.appointments.create') }}"
              class="btn btn-success">@lang('quickadmin.qa_add_new')
            </a>
        </p> -->
    @endcan
 
    <div class="panel panel-default">
        <div class="panel-heading">
            Invoices List
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($appointments) > 0 ? 'datatable1' : '' }} @can('appointment_delete') dt-select @endcan">
                <thead>
                <tr>
                  <th style="text-align:center;">
                   @can('invoice_delete')
                        <input type="checkbox" id="select-all" name="select_all"/>
                    @endcan
                    </th>
                    <th>Invoice No.</th>
                    <th>Invoice Date</th>
                    <th>Amount</th>
                    <th>Customer Name</th>
                    <th>Status</th>
                    <th>Booking status</th>
                    <th>Therapy Name</th>
                    <th>@lang('quickadmin.appointments.fields.start-time')</th>
                    <th>@lang('quickadmin.appointments.fields.finish-time')</th>
                    <th>Therapist Name</th>
                    <th>&nbsp;</th>
                    <!-- @can('appointment_delete')
                        <th style="text-align:center;"><input type="checkbox" id="select-all" name="select_all"/></th>
                    @endcan
                    
                    <th>Price</th>
                    
                    
                    <th>@lang('quickadmin.clients.fields.phone')</th>
                    <th>Location</th>
                    {{-- <th>@lang('quickadmin.clients.fields.email')</th> --}}
                    
                    
                    <th>Room No</th>
                    <th>Created By</th>
                    <th>Client Email Verified</th> 
                    
                    {{-- <th>@lang('quickadmin.appointments.fields.comments')</th> --}}
                    <th>@lang('quickadmin.appointments.fields.moneybird_status')</th>
                     -->
                    
                    
                </tr>
                </thead>
              
                <tbody>
             
                </tbody>
            </table>
        </div>
    </div>


<div class="modal fade" id="SearchModal" style="overflow:hidden;" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="closebtn btn  " data-dismiss="modal">
              <span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
            <h4 id="modalTitle" class="modal-title">Search</h4>
        </div>
        <div id="modalBody" class="modal-body">

          {!! Form::open(['method' => 'POST','id'=>'searchform']) !!}

          <div class="panel panel-default">
               
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-12 no-padding">Invoice No.</label>
                    <div class="col-xs-12 no-pl">   
                      <input type="text" id="search_invoice_no" name="invoice_no" value="" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-12 no-padding">Invoice Date</label>
                    <div class="col-xs-5 no-padding">   
                      <input type="text" id="search_invoice_date_from" name="invoice_date_from"  class="form-control date">
                    </div>
                    <label class="col-xs-1 text-center">TO</label>
                    <div class="col-xs-6  no-pl">   
                      <input type="text" id="search_invoice_date_to" name="invoice_date_to" value="" class="form-control date">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-12 no-padding">
                      <input type="checkbox" name="original_unpaid" id="original_unpaid" value="1"> Original Unpaid Invoices</label>
                     
                  </div>
                </div>

                
                <div class="row">
                  <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-12 no-padding">Customer Name</label>
                    <div class="col-xs-12 no-pl">   
                      <input type="text" id="search_customer_name" name="customer_name" value="" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-12 no-padding">Customer Email</label>
                    <div class="col-xs-12 no-pl">   
                      <input type="text" id="search_customer_email" name="customer_email" value="" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-12 no-padding">Customer Phone</label>
                    <div class="col-xs-12 no-pl">   
                      <input type="text" id="search_customer_phone" name="customer_phone" value="" class="form-control">
                    </div>
                  </div>
                </div>
                @if($CUser->role_id == "1")
                <div class="row">
                  <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-12 no-padding">Therapist Name</label>
                    <div class="col-xs-12 no-pl">   
                      <input type="text" id="search_therapist_name" name="therapist_name" value="" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-12 no-padding">Therapist Email</label>
                    <div class="col-xs-12 no-pl">   
                      <input type="text" id="search_therapist_email" name="therapist_email" value="" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-12 no-padding">Therapist Phone</label>
                    <div class="col-xs-12 no-pl">   
                      <input type="text" id="search_therapist_phone" name="therapist_phone" value="" class="form-control">
                    </div>
                  </div>
                </div>
                @endif
                <div class="row">
                  <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-12 no-padding">Therapy Name</label>
                    <div class="col-xs-12 no-pl">   
                      <input type="text" id="search_therapist_name" name="therapy_name" value="" class="form-control">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-xs-12 form-group">
                    <label class="control-label col-xs-12 no-padding">Invoice Status</label>
                    <div class="col-xs-12 no-pl">   
                      <select id="search_inv_status" name="inv_status" class="form-control">
                        <option value="">--select status--</option>
                        <option value="booking_paid_pin">Paid Pin</option>
                        <option value="booking_unpaid">Unpaid</option>
                        <option value="partial_paid">Partial Paid</option>
                        <option value="booking_confirmed">Confirmed</option>
                        <option value="Cash">Paid Cash</option>
                        <option value="Bank">Paid Bank</option>
                        <option value="Mollie">Paid Mollie</option>
                        <option value="E">Expired</option>
                        
                      </select>
                    </div>
                  </div>
                </div>

              </div>
          </div>
          <button id="btn_search" class="btn btn-success" type="button">Submit</button>
          
          {!! Form::close() !!}

        </div>
      
    </div>
</div>
</div>
@stop

@section('javascript')
    <script src="{{ url('quickadmin/js') }}/timepicker.js"></script>
    <script>
       function modalsearch()
       {
        $('#SearchModal').modal(); 
       }


      function viewinvoice(id)
      {
        window.open("{{ url('admin') }}/appointments/pview/"+id, "socialPopupWindow",
                "location=no,width=600,height=600,scrollbars=yes,top=100,left=700,resizable = no");
      }
          var handleCheckboxes = function (html, rowIndex, colIndex, cellNode) {
                var $cellNode = $(cellNode);
                var $check = $cellNode.find(':checked');
                return ($check.length) ? ($check.val() == 1 ? 'Yes' : 'No') : $cellNode.text();
            };
           window.route_all_data = '{{ url("admin/get-appointment-invoicedatatable") }}';
        window.hasInvoice = 1;
        @can('invoice_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.appointments.mass_destroy') }}';
        @endcan
    
        $('.datetime').datetimepicker({
            autoclose: true,
            dateFormat: "{{ config('app.date_format_js') }}",
            timeFormat: "HH:mm:ss"
        });
    
      

    $('.date').datepicker({
        autoclose: true,
        dateFormat: "{{ config('app.date_format_js') }}"
    });/*.datepicker("setDate", "0")*/
    </script>
    <script>
         
        function CountPrice() {
            var start_hour = parseInt($("#starting_hour").val());
            var start_minutes = parseInt($("#starting_minute").val());
            var finish_hour = parseInt($("#finish_hour").val());
            var finish_minutes = parseInt($("#finish_minute").val());
            var total_hours = (((finish_hour*60+finish_minutes)-(start_hour*60+start_minutes))/60);
            var price = parseFloat($("#price").val());
            $("#price_total").html(price*total_hours);
            $("#time").html(total_hours);
            if(start_hour != -1 && start_minutes != -1 && finish_hour != -1 && finish_minutes != -1) {
                $("#results").show();
            }
        }
        
        function UpdateEmployees(service_id, date)
        {
            if(service_id != "" && date != "") {
                $.ajax({
                    url: '{{ url("admin/get-employees") }}',
                    type: 'GET',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {service_id:service_id, date:date},
                    success:function(option){
                        //alert(option);
                        $(".employees").remove();
                        $("#date").closest(".row").after(option);
                        $("#start_time, #finish_time").hide();
                        $("#results").hide();
                    }
                });
            }
        }
    </script>


@endsection

