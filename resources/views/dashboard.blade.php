@extends('layouts.master')
@section('title', 'Dashboard')
@section('content')

    @can('dashboard')
        @canany(['today_sold', 'today_sold-purchase_cost', 'today_expense', 'today_profit'])
            {{-- Daily Summary Report --}}
            <div class="card col-12 containing-card ">
                <div class="card-header ">
                    <h3 class="card-title ">Today Summary</h3>
                </div>
                <div class="card-body">
                    <div class="grid-of-4">
                        @php
                            $summary = new \App\Services\SummaryService();
                            $today_sell = $summary::sell_profit(date('Y-m-d'), date('Y-m-d'));
                        @endphp
                        @can('today_sold')
                            <div class="card card-body bg-dark">
                                <h6 class="text-white text-uppercase">Today Sold</h6>
                                <p class="fs-18 fw-700">৳ {{ number_format($today_sell['sell_value']) }}</p>
                            </div>
                        @endcan

                        @can('today_sold-purchase_cost')
                            <div class="card card-body card-pink">
                                <h6 class="text-white text-uppercase">
                                    Today Sold - Purchase Cost
                                </h6>
                                <p class="fs-18 fw-700 text-white">৳
                                    {{ number_format($today_sell['purchase_cost']) }}</p>
                            </div>
                        @endcan

                        @can('today_expense')
                            <div class="card card-body card-danger">
                                <h6 class="text-white text-uppercase">
                                    <span>Today Expense</span>
                                </h6>
                                <p class="fs-18 fw-700 text-white">৳ {{ number_format($expense->todayExpense()->sum('amount')) }}</p>
                            </div>
                        @endcan

                        @can('today_profit')
                            <div class="card card-body card-info">
                                <h6 class="text-white text-uppercase">
                                    Today Sell Profit
                                </h6>
                                <p class="fs-18 fw-700 text-white">

                                    {{ number_format($today_sell['profit']) }}
                                </p>
                            </div>
                        @endcan
                    </div>
                </div>

            </div>
            {{-- End Daily report --}}
        @endcanany


        @canany(['current_month_sold', 'current_month_purchased', 'current_month_expense', 'current_month_returned', 'current_month_profit'])
            {{-- Monthly Summary Report --}}
            <div class="card containing-card col-12">
                <div class="card-header ">
                    <h3 class="card-title ">Current Month Summary</h3>
                </div>
                <div class="card-body">
                    <div class="grid-of-5">
                        @php
                            $start_date = date('Y-m-1');
                            $end_date = date('Y-m-t');
                            // $this_month=$summary::sell_profit(,date('Y-m-t'));
                        @endphp
                        @can('current_month_sold')
                            <div class="card card-body bg-primary">
                                <h6 class="text-white text-uppercase">Sold in {{ date('M Y') }}</h6>
                                <p class="fs-18 fw-700">৳ {{ number_format($monthly_sold = $summary::sold($start_date, $end_date)) }}
                                </p>
                            </div>
                        @endcan

                        @can('current_month_purchased')
                            <div class="card card-body card-brown">
                                <h6 class="text-white text-uppercase">
                                    Purchased - in {{ date('M Y') }}
                                </h6>
                                <p class="fs-18 fw-700 text-white">৳
                                    {{ number_format($monthly_purchased = $summary::purchased($start_date, $end_date)) }}</p>
                            </div>
                        @endcan

                        @can('current_month_expense')
                            <div class="card card-body card-danger">
                                <h6 class="text-white text-uppercase">
                                    <span>Expense in {{ date('M Y') }}</span>
                                </h6>
                                <p class="fs-18 fw-700 text-white">৳
                                    {{ number_format($monthly_spent = $expense->date_to_date($start_date, $end_date)) }}</p>
                            </div>
                        @endcan

                        @can('current_month_returned')
                            @php
                                $monthly_returned = $summary::returned($start_date, $end_date);
                            @endphp

                            <div class="card card-body card-cyan">
                                <h6 class="text-white text-uppercase">
                                    <span>Returned in {{ date('M Y') }}</span>
                                </h6>
                                <p class="fs-18 fw-700 text-white">৳ {{ number_format($monthly_returned) }}</p>
                            </div>
                        @endcan


                        @can('current_month_profit')
                            @php
                                $profit = $summary::profit($start_date, $end_date);
                            @endphp

                            <div class="card card-body card-purple">
                                <h6 class="text-white text-uppercase">
                                    Profit {{ date('M Y') }}
                                    {{-- <span style="font-size: .6em;">(Sold - Purchased - Spent - Returned)</span> --}}
                                </h6>
                                <p class="fs-18 fw-700 text-white">

                                    {{-- {{ number_format($monthly_sold - $monthly_purchased - $monthly_spent - $monthly_returned) }} --}}
                                    {{ number_format($profit) }}
                            </div>
                        @endcan

                    </div>
                </div>
            </div>
            {{-- End Monthly Summary Report --}}
        @endcanany



        @canany(['total_sold', 'total_purchased', 'total_expense', 'total_returned', 'total_profit'])
            {{-- Lifetime Report --}}
            <div class="card col-12 containing-card ">
                <div class="card-header ">
                    <h3 class="card-title ">Total</h3>
                </div>
                <div class="card-body">
                    <div class="grid-of-5">

                        @can('total_sold')
                            <div class="card card-body bg-dark">
                                <h6 class="text-white text-uppercase">Total Sold</h6>
                                <p class="fs-18 fw-700">৳ {{ number_format($total_sold = $summary::sold()) }}</p>
                            </div>
                        @endcan

                        @can('total_purchased')
                            <div class="card card-body card-success">
                                <h6 class="text-white text-uppercase">
                                    Total Purchased
                                </h6>
                                <p class="fs-18 fw-700 text-white">৳
                                    {{ number_format($total_purchased = $summary::purchased()) }}</p>
                            </div>
                        @endcan

                        @can('total_expense')
                            <div class="card card-body card-danger">
                                <h6 class="text-white text-uppercase">
                                    <span>Total Expense</span>
                                </h6>
                                <p class="fs-18 fw-700 text-white">৳ {{ number_format($total_spent = $expense->totalExpense()) }}</p>
                            </div>
                        @endcan

                        @can('total_returned')
                            {{-- Returned --}}
                            @php
                                $total_returned = $summary::returned();
                            @endphp

                            <div class="card card-body card-cyan">
                                <h6 class="text-white text-uppercase">
                                    <span>Total Returned</span>
                                </h6>
                                <p class="fs-18 fw-700 text-white">৳ {{ number_format($total_returned) }}</p>
                            </div>
                        @endcan

                        @can('total_profit')
                            <div class="card card-body card-brown">
                                <h6 class="text-white text-uppercase">
                                    Total Profit
                                </h6>
                                <p class="fs-18 fw-700 text-white">

                                    {{ number_format($total_sold - $total_purchased - $total_spent - $total_returned) }}
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
            {{-- End Lifetime Summary --}}
        @endcanany



        @canany(['total_receivable', 'total_payable'])
            {{-- Due Receivable Summary --}}
            <div class="col-12 grid-of-2">
                @can('total_receivable')
                    <div class="card card-body bg-purple">
                        <h6>
                            <span class="text-uppercase text-white">Total Receivable</span>
                        </h6>
                        <p class="fs-28 fw-700">৳ {{ number_format($summary::total_receivable()) }}</p>
                    </div>
                @endcan


                @can('total_payable')
                    <div class="card card-body bg-yellow">
                        <h6>
                            <span class="text-uppercase text-white">Total Payable</span>
                        </h6>
                        <p class="fs-28 fw-700">৳ {{ number_format($summary::total_payable()) }}</p>
                    </div>
                @endcan
            </div>
        @endcanany


        @canany(['stock-purchase_value', 'stock-sell_value'])
            {{-- SELL PURCHASE VALUE --}}
            <div class="col-12 grid-of-2">
                @php
                    $sell_purchase_value = $summary::stock_value();
                @endphp

                @can('stock-purchase_value')
                    <div class="card card-body bg-brown">
                        <h6>
                            <span class="text-uppercase text-white">Stock - Purchase Value</span>
                        </h6>
                        <p class="fs-28 fw-700">৳ {{ number_format($sell_purchase_value['total_purchase_value']) }}</p>
                    </div>
                @endcan


                @can('stock-sell_value')
                    <div class="card card-body bg-info">
                        <h6>
                            <span class="text-uppercase text-white">Stock - Sell Value</span>
                        </h6>
                        <p class="fs-28 fw-700">৳ {{ number_format($sell_purchase_value['total_sell_value']) }}</p>
                    </div>
                @endcan
            </div>
            {{-- END SELL PURCHASE VALUE --}}
        @endcanany

        @canany(['total_customer', 'total_supplier', 'total_invoices', 'total_products'])
            {{-- ETC --}}
            <div class="grid-of-4 col-12">
                @can('total_customer')
                    <div class="card card-body bg-dark">
                        <h6 class="text-white text-uppercase">Total Customer</h6>
                        <p class="fs-18 fw-700"> {{ \App\Customer::count() }}</p>
                    </div>
                @endcan

                @can('total_supplier')
                    <div class="card card-body card-brown">
                        <h6 class="text-white text-uppercase">
                            Total Supplier
                        </h6>
                        <p class="fs-18 fw-700 text-white">
                            {{ \App\Supplier::count() }}</p>
                    </div>
                @endcan

                @can('total_invoices')
                    <div class="card card-body card-danger">
                        <h6 class="text-white text-uppercase">
                            <span>Total Invoices</span>
                        </h6>
                        <p class="fs-18 fw-700 text-white"> {{ \App\Pos::count() }}</p>
                    </div>
                @endcan

                @can('total_products')
                    <div class="card card-body card-purple">
                        <h6 class="text-white text-uppercase">
                            Total Product
                        </h6>
                        <p class="fs-18 fw-700 text-white">

                            {{ \App\Product::count() }}
                        </p>
                    </div>
                @endcan
            </div>
            {{-- END OF ETC --}}
        @endcanany

    @endcan

@endsection

@section('styles')
    <style>
        .main-content {
            padding-top: 25px;
        }

        @media (min-width: 250px) {
            .grid-of-5 {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(2, minmax(100px, auto));
                grid-column-gap: 1.5%;
            }

            .grid-of-4 {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(2, minmax(100px, auto));
                grid-column-gap: 1.5%;
            }
        }

        @media (min-width: 768px) {
            .grid-of-5 {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(4, minmax(100px, 1fr));
                grid-column-gap: 1.5%;
            }

            .grid-of-4 {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(4, minmax(100px, 1fr));
                grid-column-gap: 1.5%;
            }
        }

        @media (min-width: 992px) {
            .grid-of-5 {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(5, minmax(100px, 1fr));
                grid-column-gap: 1.5%;
            }
        }

        .grid-of-2 {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(2, minmax(100px, 1fr));
            grid-column-gap: 1.5%;
        }

        .card .card {
            margin-bottom: 10px;
        }

        .containing-card>.card-body {
            padding: 10px;
        }

        .card-header {
            padding: 5px;
        }
    </style>
@endsection


@section('scripts')
    <script>
        localStorage.removeItem('pos-items');
    </script>
@endsection
