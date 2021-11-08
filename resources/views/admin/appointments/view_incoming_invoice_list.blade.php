
<div class="panel-body table-responsive" >
    <table class="table table-bordered table-stripe">
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Amount</th>
            <th>Invoice Date</th>
            <th>Comment</th>
            <th>Invoice Pdf</th>
            <th>Invoice Image</th>
            <th>Created At</th>
            <th>Status</th>
            <th>View</th>
        </tr>
        </thead>
        <tbody>
        @php $amount = 0; @endphp
        @foreach($upcoming_bookings as $key => $val)

            @php
                $val->amount =  str_replace(',','',$val->amount);
                $amount += $val->amount;
            @endphp

            <tr>

                <td>{{ date('Y',strtotime($val->created_at)).'-'.$val->id }}</td>
                <td>{{ $val->title }}</td>
                <td>{{ '€ '.$val->amount }}</td>
                <td>{{ !empty($val->invoice_date) ? date('d-m-Y',strtotime($val->invoice_date)) :  date('d-m-Y',strtotime($val->created_at)) }}</td>
                <td>{{ $val->comment }}</td>


                <td>
                    @if(!empty($val->invoice_pdf))
                        <a target="_blank" href="{{ URL::to('/').'/public/upload/'.$val->invoice_pdf }}">{{ $val->invoice_pdf }}</a>
                    @endif
                </td>
                <td>
                    @if(!empty($val->invoice_image))
                        <img src="{{ URL::to('/').'/public/upload/'.'thumb_'.$val->invoice_image }}" width="100" height="100"   />
                    @endif
                </td>
                <td>{{ date('d-m-Y',strtotime($val->created_at)) }}</td>
                <td>{{ $val->status }}</td>
                <td><a href="{{ route('admin.edit.upcoming_booking',[$val->id])  }}"
                       class="btn btn-xs btn-primary" title="View"><i class="fa fa-eye"></i></a>
                    <a href="{{ route('admin.delete.upcoming.booking',[$val->id])  }}"
                       class="btn btn-xs btn-danger" title="View"><i class="fa fa-trash"></i></a>
                </td>
            </tr>

        @endforeach

        </tbody>
        <tfoot>
        <tr>
            <<td></td>
            <<td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>Total Price</b></td>
            <td><b>{{ '€ '. number_format($amount) }}</b></td>
        </tr>

        </tfoot>
    </table>

    {!! $upcoming_bookings->links()  !!}

    <div id="printable" style="display: none">
        <style>
            table {
                font-family: "Times New Roman", Times, serif;
                border: 1px solid #523d3d;
                text-align: center;
                border-collapse: collapse;
            }
            table td, table th {
                border: 1px solid #523d3d;
                /*padding: 3px 2px;*/
            }
            table tbody td {
                font-size: 13px;
            }
            table tr:nth-child(even) {
                /*background: #D0E4F5;*/
            }
            table thead {
                background: #0B6FA4;
                /*border-bottom: 5px solid #FFFFFF;*/
            }
            table thead th {
                font-size: 17px;
                font-weight: bold;
                color: #FFFFFF;
                text-align: center;
                /*border-left: 2px solid #FFFFFF;*/
            }
            table thead th:first-child {
                /*border-left: none;*/
            }

            table tfoot {
                font-size: 14px;
                font-weight: bold;
                color: #333333;
                background: #D0E4F5;
                border-top: 3px solid #444444;
            }
            table tfoot td {
                font-size: 14px;
            }

        </style>
        <table class="paleBlueRows">
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Amount</th>
                <th>Invoice Date</th>
                <th>Comment</th>
                <th>Invoice Pdf</th>
                <th>Invoice Image</th>
                <th>Created At</th>
                <th>Status</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <<td></td>
                <<td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2"><b>Total Price</b></td>
                <td colspan="2"><b>{{ '€ '. $amount }}</b></td>
            </tr>
            </tfoot>
            <tbody>
         
            @foreach($upcoming_bookings as $key => $val)


                <tr>

                    <td>{{ date('Y',strtotime($val->created_at)).'-'.$val->id }}</td>
                    <td>{{ $val->title }}</td>
                    <td>{{ '€ '.$val->amount }}</td>
                    <td>{{ !empty($val->invoice_date) ? date('d-m-Y',strtotime($val->invoice_date)) :  date('d-m-Y',strtotime($val->created_at)) }}</td>
                    <td>{{ $val->comment }}</td>


                    <td>
                        @if(!empty($val->invoice_pdf))
                            <a target="_blank" href="{{ URL::to('/').'/public/upload/'.$val->invoice_pdf }}">{{ $val->invoice_pdf }}</a>
                        @endif
                    </td>
                    <td>
                        @if(!empty($val->invoice_image))
                            <img src="{{ URL::to('/').'/public/upload/'.'thumb_'.$val->invoice_image }}" width="100" height="100"   />
                        @endif
                    </td>
                    <td>{{  date('d-m-Y',strtotime($val->created_at))  }}</td>
                    <td>{{ $val->status }}</td>
                </tr>

            @endforeach
            </tbody>
        </table>
    </div>


</div>