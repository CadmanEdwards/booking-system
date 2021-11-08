@extends('layouts.app')
<style>
    .ui-datepicker table{
        display: none;
    }
</style>
@section('content')
<h3 class="page-title">Incoming Invoices</h3>
<div class="panel panel-default">
    <div class="panel-heading" style="    padding: 15px;">
        <span>
            @lang('quickadmin.qa_list')
        </span>
        <button style="    margin-top: -7px;" class="btn btn-primary pull-right" type="button" onclick="printer()">Print</button>
        <a style="    margin-top: -7px;
    margin-right: 5px;" href="{{ url('admin/create-incoming-invoice') }}" class="btn btn-primary pull-right" type="button" >Create</a>

    </div>
<div class="row" style="margin:20px;">
    <form>

<div class="col-md-6">    


        <div class="form-group">
            <input type="text" class="form-control date"  value="{{ $sort_by_date }}" autocomplete="false" placeholder="yyyy"  name="sort_by_date">
            
        </div>
    <div class="form-group">
        <select id='searchByStatus'  name="status" class="form-control">
            <option value=''>-- Select Status--</option>
            <option value='paid_pin' {{ ($status == 'paid_pin') ? 'selected' : '' }}>Paid Pin</option>
            <option value='bank_paid' {{ ($status == 'bank_paid') ? 'selected' : '' }}>Bank Paid</option>
            <option value='cash_paid' {{ ($status == 'cash_paid') ? 'selected' : '' }}>Cash Paid</option>
            <option value='booking_unpaid' {{ ($status == 'booking_unpaid') ? 'selected' : '' }}>Booking unpaid</option>
            <option value='booking_confirmed' {{ ($status == 'booking_confirmed') ? 'selected' : '' }}>Booking Confirmed</option>
            <option value='booking_pending' {{ ($status == 'booking_pending') ? 'selected' : '' }}>Booking pending</option>
            <option value='mollie_paid' {{ ($status == 'mollie_paid') ? 'selected' : '' }}>Mollie Paid</option>
        </select>

    </div>
    </div>

<div class="col-md-6">    


        <div class="form-group">
          <button type="submit" class="btn btn-primary">Sort</button>
            <button type="reset" class="btn btn-danger" onclick="resetForm()">Reset</button>
        </div>

    </div>

        
    </form>
      

</div>
   {!! $table !!}
</div>

@stop

@section('javascript')
<script>
    function printer() {
        var w = window.open();
        w.document.write(document.getElementById('printable').innerHTML);
       // w.print();
        w.moveTo(0, 0);
        w.resizeTo(screen.width, screen.height);
        setTimeout(function() {
            console.log(isSafariBrowser())
            if(isSafariBrowser()){
                window.onload = function() {
                    $('body').html(document.getElementById('printable').innerHTML);
                    setTimeout(
                        function(){
                            w.print()
                            w.close()
                        },500);
                };
            }else{
                w.print()
                w.close();
            }
        }, 250);
     //   w.close();

    }
    var is_safari = navigator.userAgent.indexOf("Safari") > -1;
    console.log(navigator.userAgent.indexOf("Safari"))
    function isSafariBrowser(){
        if (is_safari){
              return true;
        }
        return false;
    }


        $('.date').datepicker({
        autoclose: true,
         changeYear: true,
        minDate: new Date((new Date().getFullYear() - 90), new Date().getMonth(), new Date().getDate()),
        maxDate: new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()),
        defaultDate: new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()),
        dateFormat: "yy",
            onClose: function (dateText, inst) {
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            },
            beforeShow: function (elem,dp) {
                $('.ui-datepicker table').hide()
                $(dp.dpDiv).addClass('hide-day-calender'); //here a change
            }
    });

    function resetForm(){
        window.location.href= "{{ URL::to('/').'/admin/incoming-invoices' }}"
    }
</script>


 
@endsection