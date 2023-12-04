@extends('layouts.master')
@section('title', 'Sales List')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Sale</strong>
            </h1>
        </div>


                <div class="col-10 offset-2">
                    <table class="table table-bordered top-summary">
                        <tbody>
                            <tr>
                                @php
                                    $summary = new \App\Services\SummaryService();
                                    $today_sell = $summary::sell_profit(date('Y-m-d'), date('Y-m-d'));
                                @endphp
                                <td class="bg-danger">Sold Today:</td>
                                <td class="bg-success">{{ $today_sell['sell_value'] }} Tk</td>
                                <td class="bg-warning">Today Received:</td>
                                <td class="bg-success">{{ $summary->payment_received(date('Y-m-d'),date('Y-m-d')) }} Tk</td>
                                <td class="bg-danger">Today Profit:</td>
                                <td class="bg-success">{{ $today_sell['profit'] }} Tk</td>
                                <td class="bg-warning">Total Sold:</td>
                                {{-- <td class="bg-success">{{ \App\Services\SummaryService::sold() }} Tk</td> --}}
                                <td class="bg-success">{{ $total }} Tk</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

        <div class="header-action">
            <nav class="nav">
                <a class="nav-link active" href="{{ route('pos.index') }}">
                    Sales
                </a>
                <a class="nav-link" href="{{ route('pos.create') }}">
                    <i class="fa fa-plus"></i>
                    New Sale
                </a>
            </nav>
        </div>
    </header>
@endsection

@section('content')
    <div class="col-12" style="">
        <div class="card card-body mb-2">
            <form action="#">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <input type="text"
                               class="form-control" name="bill_no" placeholder="Bill Number" autocomplete="off" value="{{ request('bill_no') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                               data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                               class="form-control" name="start_date" placeholder="Start Date" autocomplete="off" value="{{ request('start_date') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                               data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                               class="form-control" name="end_date" placeholder="End Date" autocomplete="off" value="{{ request('end_date') }}">
                    </div>

                    <div class="form-group col-md-3">
                        <select name="customer" id="" class="form-control" data-provide="selectpicker"
                                data-live-search="true" data-size="10">
                            <option value="">Select Customer</option>
                            @foreach ($customers as $item)
                                <option value="{{ $item->id }}" {{ request('customer')==$item->id?'SELECTED':'' }}>{{ $item->name.' - '.$item->phone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <select name="product_id" id="" class="form-control" data-provide="selectpicker"
                                data-live-search="true" data-size="10">
                            <option value="">Select Product</option>
                            @foreach ($products as $item)
                                <option value="{{ $item->id }}" {{ request('product_id')==$item->id?'SELECTED':'' }}>{{ $item->name .' - '. $item->code }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="form-row mt-2">
                    <div class="form-group col-12">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-sliders"></i>
                            Filter
                        </button>
                        <a href="{{ route('pos.index') }}" class="btn btn-info">Reset</a>
                        <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a>
                    </div>
                </div>
            </form>
        </div>


        <div class="card print_area" style="width:100%;">
            <h4 class="card-title"><strong>Sale</strong></h4>

            <div class="card-body">
                @if($sales->count() > 0)
                    <div class="table-responsive">
                        <table class="table  table-bordered" data-provide="">
                            <thead>
                            <tr class="bg-primary">
                                <th>#</th>
                                <th>Invoice No.</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Date</th>
                                <th>Discount</th>
                                <th>Receivable</th>
                                <th>Paid</th>
                                <th>Product Returned</th>
                                <th>Due</th>
                                @can('pos-purchase_cost')
                                <th>Purchase Cost</th>
                                @endcan
                                @can('pos-profit')
                                <th>Profit</th>
                                @endcan
                                <th>Status</th>
                                <th class="print_hidden">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sales as $key => $sale)
                                <tr>
                                    <td>{{ (isset($_GET['page']))? ($_GET['page']-1)*$sales->count()+$key+1 : $key+1 }}</td>
                                    <td>{{ $sale->id }}</td>
                                    <td>
                                         {{ $sale->customer ? $sale->customer->name : 'Walk-in Customer' }}
                                    </td>
                                    <td>
                                        <ul class="product-list">
                                            @foreach ($sale->items()->with('product')->get() as $item)
                                                @php
                                                    $product=$item->product;
                                                @endphp
                                                <li>{{ $product->name." Code: ".$product->code."  *".$product->readable_qty($item->qty) }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>{{ date('d M, Y', strtotime($sale->sale_date)) }}</td>
                                    <td>{{ is_numeric($sale->discount)?round($sale->discount)." Tk":$sale->discount }}</td>
                                    <td>{{ round($sale->receivable) }} Tk</td>
                                    <td>{{ $paid=$sale->paid }} Tk</td>
                                    <td>{{ $returned=$sale->returned }} Tk</td>
                                    <td>{{ number_format($sale->due) }} Tk</td>
                                    @can('pos-purchase_cost')
                                    <td>
                                        <a href="{{ route('pos.purchase_cost_breakdown',$sale->id) }}">
                                            {{ number_format($sale->total_purchase_cost) }} Tk
                                        </a>
                                    </td>
                                    @endcan
                                    @can('pos-profit')
                                    <td>{{ number_format( $sale->profit ) }}</td>
                                    @endcan
                                    <td>{{ $sale->due<=0 ? 'PAID' : 'UNPAID' }}
                                    </td>
                                    <td class="print_hidden">
                                        <div class="btn-group">
                                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                                    aria-expanded="false">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start">
                                                <a class="dropdown-item" href="{{ route('pos_receipt', $sale->id) }}">
                                                    <i class="fa fa-print text-primary"></i>
                                                    Print
                                                </a>
                                                <a class="dropdown-item"
                                                   href="{{ route('chalan_receipt', $sale->id) }}">
                                                    <i class="fa fa-print text-primary"></i>
                                                    Chalan Print
                                                </a>
                                                <a class="dropdown-item" href="{{ route('pos.edit', $sale->id) }}">
                                                    <i class="fa fa-pencil-square-o text-warning"></i>
                                                    Edit
                                                </a>
                                                <a class="dropdown-item" href="{{ route('pos.show', $sale->id) }}">
                                                    <i class="fa fa-desktop text-info"></i>
                                                    Show
                                                </a>
                                                <a class="dropdown-item" href="{{ route('pos.return', $sale->id) }}">
                                                    <i class="fa  fa-backward text-primary"></i>
                                                    Return
                                                </a>
                                                <a class="dropdown-item" href="{{ route('return.index') }}?pos_id={{ $sale->id }}">
                                                    <i class="fa  fa-backward text-primary"></i>
                                                    Return List
                                                </a>
                                                <a href="{{ route("pos.add_payment",$sale->id) }}" class="edit dropdown-item" data-toggle="modal" data-target="#edit" id="Add Payment">
                                                    <i class="fa fa-money text-primary"></i>
                                                    Add Payment
                                                </a>
                                                    <a class="dropdown-item delete" href="{{ route('pos.destroy',$sale->id) }}">
                                                        <i class="fa fa-trash text-danger"></i>
                                                        Delete
                                                    </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                            {{-- <tfoot>
                            <tr class="bg-dark">
                                <th colspan="6"></th>
                                <th><strong>{{ number_format($pos->totalSaleAmount()) }} Tk</strong></th>
                                <th><strong>{{ number_format($pos->totalCash()) }} Tk</strong></th>
                                <th>
                                    Total Due: {{ number_format($pos->totalSaleAmount() - $pos->totalCash()) }} Tk
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                            </tfoot> --}}
                        </table>
                        {!! $sales->appends(Request::except("_token"))->links() !!}
                    </div>
                @else
                    <div class="alert alert-danger text-center" role="alert">
                        <strong>You have no Sales List </strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .top-summary td{
                width:12.5%;
                font-size:1.5em;
                vertical-align: middle !important;
        }

        .table td,
        .table th {
            padding: 7px;
            vertical-align: baseline;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }

        .product-list li {
            text-align: left;
        }

    </style>

@endsection

@section('scripts')
    @include('includes.delete-alert')
    @include('includes.placeholder_model')
    <script src="{{ asset('js/modal_form.js') }}"></script>
@endsection
