@extends('layouts.master')
@section('title', 'Low Stock Report')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Low Stock Report</strong>
          </h1>
     </div>

     {{-- <div class="header-action">
          <nav class="nav">
               <a class="nav-link active" href="{{ route('purchase.index') }}">
                    Purchases
               </a>
               <a class="nav-link" href="{{ route('purchase.create') }}">
                    <i class="fa fa-plus"></i>
                    Purchase
               </a>
               <a class="nav-link" href="{{ route('pos.create') }}">
                    <i class="fa fa-shopping-basket"></i>
                    Pos
               </a>
          </nav>
     </div> --}}
</header>
@endsection

@section('content')


<div class="col-12">
     <div class="card card-body mb-2 print_hidden">
        <form action="{{ route('report.low_stock') }}">
            <div class="form-row">
                <div class="form-group col-md-3">
                  {{-- <label for="">Select Product</label> --}}
                  <select name="product_id" id="" class="form-control" data-provide="selectpicker"
                data-live-search="true" data-size="10">
                      <option value="">Select a Product</option>
                    @foreach (\App\Product::all() as $item)
                      <option value="{{ $item->id }}" {{ isset($product_id)&&$product_id==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-md-3">
                    <input type="text" name="code" class="form-control" placeholder="Product Code" value="{{ isset($code)?$code:"" }}">
                </div>
                <div class="form-group col-md-3">
                    <input type="text" class="form-control" name="name" placeholder="Product Name">
                </div>
                <div class="form-group col-md-3">
                    <div class="form-group">
                        <select name="category" id="" class="form-control">
                        <option value="">Select Category</option>
                        @foreach (\App\Category::all() as $item)
                            <option value="{{ $item->id }}" {{ isset($category)&&$category==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
                        @endforeach
                        </select>
                        @if($errors->has('category'))
                        <div class="alert alert-danger">{{ $errors->first('category') }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-row mt-2">
                <div class="form-group float-right">
                    <button class="btn btn-primary" type="submit">
                            <i class="fa fa-sliders"></i>
                            Filter
                    </button>
                    <a href="{{ request()->url() }}" class="btn btn-info">Reset</a>
                </div>
            </div>
        </form>
    </div>

     <div class="card">


        <div class="print_area">
          <h3 class="card-title" style="display: inline-block;"><strong>Low Stock Products</strong></h3>
          <a href="" class="btn btn-primary float-right print_hidden" onclick="window.print()" style="margin-top:10px;">Print</a>

          <div class="card-body">
               @if($products->count() > 0)
               <div class="">
                    <table class="table table-responsive table-bordered pt-2"
                    {{-- data-provide="datatables" --}}
                    >
                         <thead>
                              <tr class="bg-primary">
                                   <th>#</th>
                                   <th>Image</th>
                                   <th>Product</th>
                                   <th>Category</th>
                                   <th>Price</th>
                                   <th>Sale</th>
                                   <th>Purchases</th>
                                   <th>Available Stock</th>
                                   <th>Sell Value</th>
                              </tr>
                         </thead>
                         <tbody>
                              @foreach($products as $key => $product)
                              <tr>
                                   <th scope="row">{{ ++$key }}</th>
                                   <td style="padding:5px" class="text-center">
                                     <img src="{{ asset($product->image) }}" width="40" alt="Image">
                                   </td>
                                   <td>
                                        <a href="#">{{ $product->name." - ".$product->code  }}</a>
                                   </td>
                                   <td>
                                        {{ $product->category ? $product->category->name : 'No Category' }}
                                   </td>
                                   <td>
                                        {{ $product->price }} Tk

                                   </td>
                                   <td>
                                        {{ $product->readable_qty($product->sell_count()) }}
                                   </td>
                                   <td>
                                        {{ $product->readable_qty($product->purchase_count()) }}
                                   </td>
                                   <td>
                                        {{ $product->readable_qty($product->stock) }}
                                   </td>
                                   <td>
                                       {{ $product->quantity_worth($product->stock,$product->price) }} Tk
                                   </td>
                              </tr>
                              @endforeach
                         </tbody>
                    </table>
                    {!! $products->appends(Request::except("_token"))->links() !!}
               </div>
               @else
               <div class="alert alert-danger" role="alert">
                    <strong>You have no Stocks</strong>
               </div>
               @endif
          </div>

        </div>
     </div>
</div>
@endsection

@section('styles')
<style>
     .table tr td {
          vertical-align: middle;
          padding: 5px;
          text-align: center;
          font-weight: bold;
     }

     .table tr th {
          text-align: center;
     }


</style>

<style>
    @media print{
        table,table th,table td{
            color:black !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>

</script>
@endsection
