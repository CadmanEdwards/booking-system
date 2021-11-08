@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.appointments.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.post.edit.upcoming.booking'] ,'enctype' => 'multipart/form-data']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            Edit Incoming Invoice
        </div>

        <div class="panel-body">
            <div class="row">
                <input type="hidden" name="id" value="{{ $upcoming_booking->id }}">
                <div class="col-xs-12 form-group">
                    {!! Form::label('title ', 'Title', ['class' => 'control-label']) !!}
                    {!! Form::text('title', $upcoming_booking->title, ['class' => 'form-control', 'placeholder' => '']) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('amount ', 'Amount in Euros', ['class' => 'control-label']) !!}
                    <input type="text" class="form-control" name="amount" id="currency-field" pattern="^{1,3}(,\d{3})*(\.\d+)?$" value="{{ $upcoming_booking->amount }}" data-type="currency" placeholder="€1,000,000.00">

                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('invoice_date ', 'Invoice Date', ['class' => 'control-label']) !!}
                    <input type="text"  class="form-control date" placeholder="dd-mm-yyyy" name="invoice_date" id="invoice_date" value="{{ $upcoming_booking->invoice_date }}">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('comment', 'Comments', ['class' => 'control-label']) !!}
                    {!! Form::textarea('comment', $upcoming_booking->comment, ['class' => 'form-control ', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('comments'))
                        <p class="help-block">
                            {{ $errors->first('comments') }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('Status', 'status', ['class' => 'control-label']) !!}
                    <select id='searchByStatus'  name="status" class="form-control">
                        <option value=''>-- Select Status--</option>
                        <option value='paid_pin' {{ ($upcoming_booking->status == 'paid_pin') ? 'selected' : '' }}>Paid Pin</option>
                        <option value='bank_paid' {{ ($upcoming_booking->status == 'bank_paid') ? 'selected' : '' }}>Bank Paid</option>
                        <option value='cash_paid' {{ ($upcoming_booking->status == 'cash_paid') ? 'selected' : '' }}>Cash Paid</option>
                        <option value='booking_unpaid' {{ ($upcoming_booking->status == 'booking_unpaid') ? 'selected' : '' }}>Booking unpaid</option>
                        <option value='booking_confirmed' {{ ($upcoming_booking->status == 'booking_confirmed') ? 'selected' : '' }}>Booking Confirmed</option>
                        <option value='booking_pending' {{ ($upcoming_booking->status == 'booking_pending') ? 'selected' : '' }}>Booking pending</option>
                        <option value='mollie_paid' {{ ($upcoming_booking->status == 'mollie_paid') ? 'selected' : '' }}>Mollie Paid</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('invoice_pdf ', 'Invoice Pdf', ['class' => 'control-label']) !!}
                    <input type="file" name="invoice_pdf" id="invoice_pdf">
                    @if(!empty($upcoming_booking->invoice_pdf))
                    <p><a target="_blank" href="{{ URL::to('/').'/public/upload/'.$upcoming_booking->invoice_pdf }}">{{ $upcoming_booking->invoice_pdf }}</a></p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('invoice_image ', 'Invoice Image', ['class' => 'control-label',]) !!}
                    <input type="file" name="invoice_image"  id="invoice_image" accept="image/*" capture>
                    @if(!empty($upcoming_booking->invoice_image))
                    <img src="{{ URL::to('/').'/public/upload/'.$upcoming_booking->invoice_image }}" width="300" height="200"  class="img img-responsive">
                        @endif
                </div>
            </div>
        </div>

        {!! Form::submit(trans('quickadmin.qa_save'), ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>
        @stop
        <style>

        </style>
@section('javascript')
    @parent

            <script>
                $('.date').datepicker({
                    autoclose: true,
                    dateFormat: "dd-mm-yy"
                });
                $("input[data-type='currency']").on({
                    keyup: function() {
                        formatCurrency($(this));
                    },
                    blur: function() {
                        formatCurrency($(this), "blur");
                    }
                });


                function formatNumber(n) {
                    // format number 1000000 to 1,234,567
                    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                }


                function formatCurrency(input, blur) {
                    // appends $ to value, validates decimal side
                    // and puts cursor back in right position.

                    // get input value
                    var input_val = input.val();

                    // don't validate empty input
                    if (input_val === "") { return; }

                    // original length
                    var original_len = input_val.length;

                    // initial caret position
                    var caret_pos = input.prop("selectionStart");

                    // check for decimal
                    if (input_val.indexOf(".") >= 0) {

                        // get position of first decimal
                        // this prevents multiple decimals from
                        // being entered
                        var decimal_pos = input_val.indexOf(".");

                        // split number by decimal point
                        var left_side = input_val.substring(0, decimal_pos);
                        var right_side = input_val.substring(decimal_pos);

                        // add commas to left side of number
                        left_side = formatNumber(left_side);

                        // validate right side
                        right_side = formatNumber(right_side);

                        // On blur make sure 2 numbers after decimal
                        if (blur === "blur") {
                            right_side += "00";
                        }

                        // Limit decimal to only 2 digits
                        right_side = right_side.substring(0, 2);

                        // join number by .
                        input_val = "" + left_side + "." + right_side;

                    } else {
                        // no decimal entered
                        // add commas to number
                        // remove all non-digits
                        input_val = formatNumber(input_val);
                        input_val = "" + input_val;

                        // final formatting
                        if (blur === "blur") {
                            input_val += ".00";
                        }
                    }

                    // send updated string to input
                    input.val(input_val);

                    // put caret back in the right position
                    var updated_len = input_val.length;
                    caret_pos = updated_len - original_len + caret_pos;
                    input[0].setSelectionRange(caret_pos, caret_pos);
                }
            </script>



@stop