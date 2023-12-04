@extends('layouts.master')
@section('title', 'Customer Due Report')

@section('page-header')
<header class="header bg-ui-general">
    <div class="header-info">
        <h1 class="header-title">
            <strong>Customer Due Report</strong>
        </h1>
    </div>
</header>
@endsection

@section('content')

<div class="col-12">


    <div class="card card-body">
        <div class="row">
            <div class="col-12">
                {{-- <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a> --}}
                <form action="">
                    <div class="form-row">

                        <div class="form-group col-md-3">
                            <select name="customer_id" id="" class="form-control" data-provide="selectpicker"
                                    data-live-search="true" data-size="10">
                                <option value="">Select Customer</option>
                                @foreach ($filter_customers as $item)
                                    <option value="{{ $item->id }}" {{ request('customer_id')==$item->id?'SELECTED':'' }}>{{ $item->name }}</option>
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
                            <a href="{{ request()->url() }}" class="btn btn-info">Reset</a>
                            <a href="" class="btn btn-primary pull-right" onclick="window.print()">Print</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


	     <div class="card">
        <div class="print_area"  style="width:100%;">
          {{-- <a href="" class="btn btn-primary float-right print_hidden" onclick="window.print()" style="margin-top:10px;">Print</a> --}}



          <h3 class="card-title" style=" text-align: left;"><strong>Customer Due Report</strong></h3>
          <div class="card-body">
               <table class="table table-striped table-bordered">
                <thead>
                    <tr>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Address</th>
						<th>Invoice Due</th>
						<th>Direct Due</th>
						<th>Total Due</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $key => $customer)
                        <tr>
                            {{-- <th scope="row">{{ (isset($_GET['page']))? ($_GET['page']-1)*20+$key+1 : $key+1 }}</th> --}}
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{!! $customer->address !!}</td>

							{{--<td class="font-weight-bold">
                                {{ number_format($customer->receivable())  }} Tk
                            </td>
                            <td class="font-weight-bold">
                                {{ number_format($customer->paid()) }} Tk
								</td>--}}

                            <td class="font-weight-bold">
                                {{ number_format($customer_due=$customer->due() ) }} Tk
                            </td>
							<td class="font-weight-bold">
								@php
                                    $wallet_balance = $customer->wallet_balance();
                                    $wallet_balance=$wallet_balance < 0 ? abs($wallet_balance) : 0;
                                @endphp
								{{ $wallet_balance }}
								Tk
							</td>
							<td class="font-weight-bold">{{ $wallet_balance+$customer_due }} Tk</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="alert alert-danger" role="alert">
                                    <strong>You have no Customers with Due</strong>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                </tbody>
               </table>
			   {{$customers->links()}}
          </div>

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
@include('includes.delete-alert')
<script>

</script>
@endsection
