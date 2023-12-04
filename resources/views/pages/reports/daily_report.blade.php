@extends('layouts.master')
@section('title', 'Daily Report')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Daily Report</strong>
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
                              <input type="text" name="start_date" data-provide="datepicker"
                                   data-date-today-highlight="true" data-orientation="bottom"
                                   data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control"
                                   placeholder="Enter Start Date" autocomplete="off">
                         </div>
                         <div class="form-group col-md-5">
                              <input type="text" name="end_date" data-provide="datepicker"
                                   data-date-today-highlight="true" data-orientation="bottom"
                                   data-date-format="yyyy-mm-dd" data-date-autoclose="true" class="form-control"
                                   placeholder="Enter End Date" autocomplete="off">
                         </div>
                         <div class="form-group col-md-2">
                              <button class="btn btn-primary" type="submit">Filter</button>
							 <a href="" class="btn btn-primary" onclick="window.print()">Print</a>

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
		<h3>Daily Report</h3>

		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Date</th>
					<th>Sell Amount</th>
					<th>Purchase Amount</th>
					<th>Expenses</th>
					<th>Returned</th>
					<th>Sell/Gross Profit</th>
					<th>Net Profit</th>
				</tr>
			</thead>
			<tbody>
				@php
					$summary=new \App\Services\SummaryService();


					$begin = new DateTime( $start_date );
					$end = new DateTime( $end_date );
					$end = $end->modify( '+1 day' );

					$interval = new DateInterval('P1D');
					$daterange = new DatePeriod($begin, $interval ,$end);

					$total_sold=0;
					$total_purchased=0;
					$total_spent=0;
					$total_returned=0;
					$total_gross=0;
					$total_net=0;

				@endphp
				@foreach($daterange as $date)
					@php
					$date=$date->format('Y-m-d');
					$sell_and_profit=$summary::sell_profit($date,$date);
					//dd($sell_and_profit);
					@endphp
					<tr>
						<td>{{$date}}</td>
						<td>{{ $sold=$sell_and_profit['sell_value'],$total_sold+=$sold }}</td>
						<td>{{ $purchased=$summary::date_purchased($date),$total_purchased+=$purchased }}</td>
						<td>{{ $spent=$summary::date_expense($date),$total_spent+=$spent }}</td>
						<td>{{ $returned=$summary::returned($date,$date),$total_returned+=$returned }}</td>
						<td>{{ $gross_profit=$sell_and_profit['profit'],$total_gross+=$gross_profit }}</td>
						@php
							$discount=$summary::date_discount($date);
						@endphp
						<td>{{ $net=$sold-$purchased-$spent-$returned,$total_net+=$net }}</td>
					</tr>
				@endforeach
			</tbody>

			<tfoot class="">
				<tr>
					<td>Total:</td>
					<td>{{$total_sold}}</td>
					<td>{{$total_purchased}}</td>
					<td>{{$total_spent}}</td>
					<td>{{ $total_returned }}</td>
					<td>{{$total_gross}}</td>
					<td>{{$total_net}}</td>
				</tr>
			</tfoot>
		</table>
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
