@extends('layouts.master')
@section('title', 'Pos Receipt')


@section('content')
<div class="col-md-12">
    <div class="row justify-content-center">
        <div class="col-md-7 card card-body print">
            <div id="print-area">
                <div class="invoice-header">
                    <div class="logo-area">
                        @if($pos_setting->invoice_logo_type=="Logo"&&$pos_setting->logo!=null)
                            <img src="{{ asset($pos_setting->logo) }}" alt="logo">
                        @elseif($pos_setting->invoice_logo_type=="Name"&&$pos_setting->company!=null)
                        {{-- <img src="{{ asset($pos_setting->logo) }}" alt="logo"> --}}
                            <h4>{{ $pos_setting->company }}</h4>
                        @else
                            <img src="{{ asset($pos_setting->logo) }}" alt="logo">
                            <div class="clearfix"></div>
                            <h4>{{ $pos_setting->company }}</h4>
                        @endif
                    </div>
                    <address>
                        Address : <strong>{{ $pos_setting->address }}</strong>
                        <br>
                        Phone : <strong>{{ $pos_setting->phone }}</strong>
                        <br>
                        Email : <strong>{{ $pos_setting->email }}</strong>
                        {{-- <br>
                        Facebook Page : <strong>{{ $pos_setting->page_link }}</strong>
                        <br>
                        Website : <strong>{{ $pos_setting->website }}</strong> --}}
                    </address>

                </div>

                {{-- @dd($pos) --}}

                <div class="bill-date">
                    <div class="bill-no">
                        Invoice No: {{ $pos->id }}
                    </div>
                    <div class="date">
                        Date: <strong>{{ date('d M, Y', strtotime($pos->sale_date)) }}</strong>
                    </div>
                </div>
                <div class="name">
                    Client Name : <span>{{ $pos->customer ? $pos->customer->name : 'Walk-in Customer' }}</span>
                </div>
                <div class="address">
                    Address : <span>{{ $pos->customer ? $pos->customer->address : 'Walk-in Customer'}}</span>
                </div>
                <div class="mobile-no">
                    Mobile : <span>{{ $pos->customer ? $pos->customer->phone : 'Walk-in Customer'}}</span>
                </div>

                {{-- <div class="cus_info">
                    Courier : <span>{{ $pos->delivery_method ? $pos->delivery_method->name : '-'}}</span>
                </div> --}}

                {{-- cus_info --}}



                {{-- <div class="clearfix"></div> --}}
                {{-- items Design --}}
                <table class="table table-bordered table-plist my-3 order-details">
                    <tr class="bg-primary">
                        <th>#</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Net.A</th>
                    </tr>
                    @foreach ($pos->items as $key => $item)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ $item->rate }} Tk</td>
                        <td>{{ $item->sub_total }} Tk</td>
                    </tr>
                    @endforeach
                    @php
                        $currentDue = $pos->due;

                        $previousDue = $pos->customer ? $pos->customer->receivable() - $pos->customer->paid() : 0;

						$wallet_due=$pos->customer?$pos->customer->wallet_balance():0;
						if($wallet_due<0){
							$previousDue+=abs($wallet_due);
						}
                        if($previousDue>=$currentDue){
							$previousDue = $previousDue - $currentDue;
                        }

                        $totalDue = $previousDue + $currentDue;
                    @endphp
                    <tr>
                        <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Total : </td>
                        <td>
                            <strong>{{ number_format($pos->items->sum('sub_total'),2) }} </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Discount : </td>
                        <td>
                            <strong>
                            @if(empty($pos->discount))
                                0 Tk
                            @elseif(strpos($pos->discount, '%'))
                                {{ $pos->discount }}
                            @elseif (strpos($pos->discount, '%') == false)
                                {{ number_format($pos->discount,2) }} Tk
                            @endif
                            </strong>
                        </td>
                    </tr>


                    <tr>
                        <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Grand Total : </td>
                        <td>
                            <strong>{{ number_format($pos->receivable,2) }} </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Total Paid : </td>
                        <td>
                            <strong>{{ number_format($pos->paid,2) }} </strong>Tk
                        </td>
                    </tr>

                    {{-- <tr>
                         <td colspan="4" class="text-right">Delivery Charge : </td>
                         <td>
                             <strong>{{ round($pos->delivery_cost) }} </strong>Tk
                         </td>
                     </tr> --}}

                    @if($pos->previous_returned_product_value()>0)
                        <tr>
                            <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                            <td class="text-right">Previous Returned : </td>
                            <td>
                                <strong>{{ number_format($pos->previous_returned_product_value(),2) }} </strong>Tk
                            </td>
                        </tr>
                    @endif

                    @if($previousDue>0)
                    <tr>
                        <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Previous Due : </td>
                        <td>
                            <strong>{{ number_format($previousDue,2) }}
                            </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Current Due : </td>
                        <td>
                            <strong>{{ number_format($currentDue,2)  }}
                            </strong>Tk
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Total Due : </td>
                        <td>
                            <strong>{{ number_format($totalDue,2)  }}
                            </strong>Tk
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                        <td class="text-right">Total Due : </td>
                        <td>
                            <strong>{{ number_format($totalDue,2)  }}
                            </strong>Tk
                        </td>
                    </tr>
                    @endif
                </table>


                <div class="row mt-4">
                    <div class="col-6">
                        <h3 class="">Payments</h3>
                    </div>
                    <div class="col-6">
                        <a href="{{ route("pos.add_payment",$pos->id) }}" class="edit btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#edit" id="Add Payment">
                            {{-- <i class="fa fa-money text-primary"></i> --}}
                            Add Payment
                        </a>
                    </div>
                </div>


                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pos->payments as $payment)
                            <tr>
                                <td>{{ date('d/m/Y',strtotime($payment->payment_date)) }}</td>
                                <td>{{ $payment->pay_amount }}</td>
                                <td>
                                    <a href="{{ route('payment.partial_delete', $payment->id) }}" class="btn btn-danger delete">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($pos->returns->count())
                    <h3 class="mt-4">Returns</h3>

                    @foreach($pos->returns as $return)


                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($return->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{{ $item->unit_price }}</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td>{{ $return->return_product_value }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                @endif

            </div>




            <div class="row">
                <div class="col-12 text-center">
                    <button class="btn btn-secondary print_hidden" onclick="print_receipt('print-area')">
                        <i class="fa fa-print"></i>
                        Print
                    </button>
                </div>
            </div>

            {{-- <div class="row mt-4">
                <div class="col-6">
                    <a href="{{ route('pos.create') }}" class="btn btn-primary btn-block">
                        <i class="fa fa-reply"></i>
                        New Sale
                    </a>
                </div>

                <div class="col-6">
                    <a href="{{ route('pos.index') }}" class="btn btn-primary btn-block">
                        <i class="fa fa-reply"></i>
                        Sale List
                    </a>
                </div>
            </div> --}}

        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://fonts.googleapis.com/css?family=Petrona&display=swap" rel="stylesheet">
<style rel="stylesheet">
    .page-footer hr{
        margin:2px;
    }

    .signature {
        margin-top: 50px;
        display: flex;
        justify-content: space-between;
    }

    .signature p {
        margin-top: -10px;
    }


    .order-details th{
        font-weight:bold;
    }

    strong {
        font-weight: 800;
    }

    address {
        margin-bottom: 0px;
    }

    .invoice-header {
        width: 100%;
        display: block;
        box-sizing: border-box;
        overflow: hidden;
    }

    .invoice-header address {
        width: 50%;
        float: left;
        padding: 5px;
    }

    .logo-area img {
        @if($pos_setting->invoice_logo_type=="Both")
            width: 30%;
        @else
            width: 40%;
        @endif
        display: inline;
        float: left;
    }

    .logo-area h1 {
        display: inline;
        float: left;
        font-size: 17px;
        padding-left: 8px;
    }

    .logo-area h4{
        font-weight: bold;
        margin-top:5px;
    }

    .invoice-header .logo-area {
        width: 50%;
        float: left;
        padding: 5px;
    }

    .bill-date {
        width: 100%;
        border: 1px solid #000;
        overflow: hidden;
        padding: 0 15px;
    }

    .date {
        width: 50%;
        float: left;
    }

    .bill-no {
        width: 50%;
        float: left;
    }

    .name,
    .address,
    .mobile-no,.cus_info {
        width: 100%;
        border-left: 1px solid #000;
        border-bottom: 1px solid #000;
        border-right: 1px solid #000;
        padding: 0 15px;
    }

    .name span,
    .address span,
    .mobile-no span, .cus_info span {
        padding-left: 15px;
        font-weight: 800;
    }

    .sign {
        width: 250px;
        border-top: 1px solid #000;
        float: right;
        margin: 40px 20px 0 0;
        text-align: center;
    }

    @media print {
        body * {
            visibility: visible;
        }

        .table-rheader td {
            border-top: 0px;
            padding: 5px;
            vertical-align: baseline !important;
        }

        .table-plist td {
            padding: 0px;
            text-align: center;
        }

        .table-plist th {
            padding: 0px;
            text-align: center;
        }

        .border-bottom {
            border-bottom: 1px dotted #000;
        }
        .print{
            margin-bottom: 0;
        }

        .table-bordered td, .table-bordered th {
            border: 1px solid #000!important;
        }
    }

    body {
        font-family: 'Petrona', serif;
    }

    .note,.in_word,.signature,.bill-no,.date,.name,.mobile-no,.address,th,td,address,h4{
          color:black;
     }

</style>
{{-- <link rel="stylesheet" href="{{ asset('dashboard/css/receipt.css') }}"> --}}

<style>
    .table-rheader td {
        border-top: 0px;
        padding: 5px;
        vertical-align: baseline !important;
    }

    .table td {
        padding: 0px;
        text-align: center;
    }

    .table th {
        padding: 0px;
        text-align: center;
        background: #ddd;
    }

    .border-bottom {
        border-bottom: 1px dotted #000;
    }

</style>
@endsection

@section('scripts')
<script>
    // clear localstore
    localStorage.removeItem('pos-items');

    function print_receipt(divName) {
        let printDoc = $('#' + divName).html();
        let originalContents = $('body').html();
        $("body").html(printDoc);
        window.print();
        $('body').html(originalContents);
    }

</script>
@include('includes.delete-alert')
@include('includes.placeholder_model')
<script src="{{ asset('js/modal_form.js') }}"></script>
@endsection
