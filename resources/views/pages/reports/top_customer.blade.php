@extends('layouts.master')
@section('title', 'Top Customers')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Top Customers</strong>
            </h1>
        </div>

        {{-- <div class="header-action">
            <nav class="nav">
                <a class="nav-link active" href="{{ route('customer.index') }}">
                    Customers
                </a>
                <a class="nav-link" href="{{ route('customer.create') }}">
                    <i class="fa fa-plus"></i>
                    New Customer
                </a>
            </nav>
        </div> --}}
    </header>
@endsection

@section('content')
    <div class="col-12">

        <div class="card card-body mb-2">
            <form action="{{ route('report.top_customer') }}">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                               data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                               class="form-control" name="start_date" placeholder="Start Date" autocomplete="off" value="{{ $start_date }}">
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                               data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                               class="form-control" name="end_date" placeholder="End Date" autocomplete="off" value="{{ $end_date }}">
                    </div>
                </div>
                <div class="form-row mt-2">
                    <div class="form-group col-12">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-sliders"></i>
                            Filter
                        </button>
                        <a href="{{ route('report.top_customer') }}" class="btn btn-info">Reset</a>
                        <a href="" class="btn btn-primary float-right" onclick="window.print()">Print</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card col-12 print_area">

            <h2 class="card-title" style="text-align: center;"><strong>Top Customers(Based on Sell Amount)</strong></h2>
            <h3 class="card-title" style="text-align: center;">Report From {{ date('d/m/Y',strtotime($start_date)) }} to {{ date('d/m/Y',strtotime($end_date)) }}</h3>

            <div class="card-body card-body-soft p-4">
                @if($customers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered"
                         {{-- data-provide="datatables" --}}
                         >
                            <thead>
                            <tr class="bg-primary">
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                {{-- <th>Opening Balance</th> --}}
                                <th>Total Sell</th>
                                {{-- <th>Paid</th>
                                <th>Due</th>
                                <th>Wallet Balance</th> --}}
                                {{-- <th>#</th> --}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($customers as $key => $customer)
                                <tr>
                                    <th scope="row">{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}</th>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{!! $customer->address !!}</td>
                                    {{-- <th>{{ $customer->opening_balance }}</th> --}}
                                    <td class="font-weight-bold">
                                        {{ number_format($customer->receivable($start_date,$end_date))  }} Tk
                                    </td>
                                    {{-- <td class="font-weight-bold">
                                        {{ number_format($customer->paid()) }} Tk
                                    </td>

                                    <td class="font-weight-bold">
                                        {{ number_format($customer->receivable() - $customer->paid() ) }} Tk
                                    </td>
                                    <td>
                                        {{ number_format($customer->wallet_balance()) }} Tk
                                    </td> --}}
                                    {{-- <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                                    aria-expanded="false">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start">
                                                <a class="dropdown-item"
                                                   href="{{ route('customer.edit', $customer->id) }}">
                                                    <i class="fa fa-edit"></i>
                                                    Edit
                                                </a>
                                                <a class="dropdown-item"
                                                   href="{{ route('customer.show', $customer->id) }}">
                                                    <i class="fa fa-file-excel-o"></i>
                                                    Report
                                                </a>
                                                <a class="dropdown-item delete" href="{{ route('customer.destroy',$customer->id) }}">
                                                    <i class="fa fa-trash"></i>
                                                    Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td> --}}
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        {!! $customers->appends(Request::except("_token"))->links() !!}
                    </div>
                @else
                    <div class="alert alert-danger" role="alert">
                        <strong>You have no Customers</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    @media print{
        table,table th,table td{
            color:black !important;
        }
    }
</style>
@endsection

@section('scripts')


@endsection
