@extends('layouts.master')
@section('title', 'Daily Report')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Profit Loss Report</strong>
          </h1>
     </div>
</header>
@endsection

@section('content')

{{-- Top Sales Products in This Month --}}
<div class="col-12">
     <div class="card">
          <form action="#" method="GET">
               <div class="card-body">
                    <div class="form-row">
                         <div class="form-group col-md-5">
                              <input type="text" name="start_date" class="form-control datepicker"
                              placeholder="Enter End Date" autocomplete="off" value="{{ $start_date!=null?date('Y-m',strtotime($start_date)):'' }}">
                         </div>
                         <div class="form-group col-md-5">
                              <input type="text" name="end_date" class="form-control datepicker"
                                   placeholder="Enter End Date" autocomplete="off" value="{{ $end_date!=null?date('Y-m',strtotime($end_date)):'' }}">
                         </div>
                         <div class="form-group col-md-2">
                              <button class="btn btn-primary" type="submit">Filter</button>
							 <a href="{{ request()->url(0) }}" class="btn btn-primary">Reset</a>
							 <a href="" class="btn btn-primary mt-2" onclick="window.print()">Print</a>

                         </div>
                    </div>
               </div>
          </form>
     </div>
</div>
{{-- Summary Report --}}
{{-- @dd($sells) --}}

<div class="card col-12 print_area">
	<div class=" card-body">
		<h3>Profit Loss Report</h3>
        @if($start_date&&$end_date)
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Sales</th>
                        <th>Cost of Goods Sold</th>
                        <th>Gross Profit</th>
                        <th>Expenses</th>
                        <th>Net Profit</th>
                    </tr>
                </thead>

                @php
                    // dd($start_date);
                    // dd(date('y',strtotime($start_date)));
                    $year = date('Y',strtotime($start_date));
                    $month = (int)date('m',strtotime($start_date));
                    $end_year = date('Y',strtotime($end_date));
                    $end_month = date('m',strtotime($end_date));
                @endphp

                <tbody>
                    @while($year < $end_year || ($year == $end_year && $month <= $end_month))
                        <tr>
                            <th>{{ date('M',strtotime("2022-$month-01")) }} {{ $year }}</th>
                            @php
                                $month_start_date="$year-$month-01";
                                $month_end_date=date('Y-m-t',strtotime("$year-$month-01"));
                                $summary_service=new App\Services\SummaryService();
                                $sell_cost_profit=$summary_service::sell_profit($month_start_date,$month_end_date);
                            @endphp
                            <td>{{ $sell_cost_profit['sell_value'] }}</td>
                            <td>{{ $sell_cost_profit['purchase_cost'] }}</td>
                            <td>{{ $profit=$sell_cost_profit['profit'] }}</td>
                            <td>{{ $expense=$summary_service::expenses($month_start_date,$month_end_date) }}</td>
                            <td>{{ $profit-$expense }}</td>
                        </tr>



                        @php
                            $month++;

                            if ($month == 13)
                            {
                                $year++;
                                $month = 1;
                            }
                        @endphp
                    @endwhile
                </tbody>
            </table>
        @else
            <div class="alert alert-danger">
                Please Select Start and End Month
            </div>
        @endif

	</div>
</div>


</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    @media print{
        table,table th,table td{
            color:black !important;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('.datepicker').datepicker({
            'format':'yyyy-mm',
            viewMode: "years",
            minViewMode: "months",
            autoclose:true
        });
    </script>
@endsection
