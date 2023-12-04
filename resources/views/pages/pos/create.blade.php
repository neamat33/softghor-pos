@extends('layouts.master')
@section('title', 'POS Manage')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>POS Manage</strong>
    </h1>
  </div>
</header>
@endsection

@section('content')

<div class="col-md-12">
  <div class="row">
    <div class="col-md-6">
      <div class="card card-body">

          <div class="form-row mb-3">
            <div class="input-group col-md-12">
              <span class="input-group-addon" id="basic-addon1">
                <i class="fa fa-barcode"></i>
              </span>
              <input type="text" id="id_code" class="form-control" placeholder="Scan Barcode" name="code"  autofocus/>
             </div>
          </div>


        <div class="form-row mb-3">
          <div class="col-md-12">
            <input type="text" id="product_search" class="form-control" placeholder="Start to write product name..."
              name="p_name" />
            <input type="hidden" id="search_product_id">

          </div>
        </div>

        <form action="{{ route('pos.store') }}" id="order-form" method="POST">
          @csrf
          <div class="form-row">
            <input type="text" data-provide="datepicker" data-date-today-highlight="true" data-date-format="yyyy-mm-dd"
              class="form-control" name="sale_date" value="{{ date('Y-m-d') }}">
          </div>
          <div class="form-row mt-4">
            <div class="form-group col-9">
              <select name="customer" id="customer" class="form-control" data-provide="selectpicker"
                data-live-search="true" data-size="10">
                <option value="0">Walk-in Customer</option>
                @foreach ($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name." - ".$customer->phone }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-3">
              {{-- <button class="" type="button" data-toggle="modal" data-target="#add-customer-modal">Add</button> --}}
              <a href="{{ route("pos.add_customer") }}" class="edit btn btn-primary" data-toggle="modal" data-target="#edit" id="Add Customer">
                  {{-- <i class="fa fa-money text-primary"></i> --}}
                  Add
              </a>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 mt-4">
              <table class="table table-bordered">
                <thead class="bg-primary">
                  <tr>
                    <th>Name</th>
                    <th style="width:220px;">Quantity</th>
                    <th>Price</th>
                    <th>Sub T</th>
                    <th>
                      <a href="#" id="clearList">
                        <i class="fa fa-trash"></i>
                      </a>
                    </th>
                  </tr>
                </thead>
                <tbody id="tbody"></tbody>
                <tfoot class="bg-danger">
                  <tr>
                    {{-- <th class="text-center" colspan="2">Total Qty: <strong id="totalQty"></strong> </th> --}}
                    <th colspan="2"></th>
                    <th class="text-center" colspan="3">Total: <strong id="totalAmount"></strong> Tk</th>
                  </tr>
                </tfoot>
              </table>

              <div class="form-gorup text-center">
                <button type="button" id="payment-btn" class="btn btn-primary">
                  <i class="fa fa-money"></i>
                  Payment
                </button>
                @if ($errors->any())
                    <div class="text-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
              </div>
              {{-- Payment Modal --}}
              <div class="modal fade" id="payment-modal" tabindex="-1">
                <div class="modal-dialog modal-md">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title" id="myModalLabel">Payment</h4>
                      <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <table class="table table-bordered text-left">
                        <tr>
                          <td width="50%">
                            <strong class="float-left">Paying Items: </strong>
                            <strong class="float-right">(<span id="items">0</span>)</strong>
                          </td>
                          <td>
                            <strong class="float-left">Total Receivable: </strong>
                            <strong class="float-right">(<span id="receivable">0</span> Tk)</strong>
                            <input type="hidden" name="receivable_amount" id="receivable_input">
                          </td>
                        </tr>
                        <tr>
                          <td width="50%">
                            <strong class="float-left">After Discount : </strong>
                            <strong class="float-right"> (<span id="after_discount">0</span> Tk)</strong>
                          </td>
                          <td>
                            <strong class="float-left">Balance </strong>
                            <strong class="float-right"> (<span id="balance">0</span> Tk)</strong>
                            <input type="hidden" name="balance" id="balance_input">
                          </td>
                        </tr>

                        {{-- <tr>
                          <td width="50%">
                            <strong class="float-left">After Delivery Cost : </strong>
                            <strong class="float-right"> (<span id="after_delivery_cost">0</span> Tk)</strong>
                          </td>
                          <td>

                          </td>
                        </tr> --}}

                      </table>
                      <hr>


                      {{-- HIDDEN PORTION --}}

                      {{-- <div class="form-row" style="">
                        <div class="form-group col-md-6">
                          <label for="delivery_method">Deliver Method </label>
                          <select name="delivery_method" id="delivery_method" class="form-control" required>
                            <option value="">Select Delivery Method </option>
                            @foreach ($delivery_methods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label for="delivery_cost">Delivery Cost </label>
                          <input type="number" step="any" name="delivery_cost" id="delivery_cost"
                            placeholder="Enter Delivery Cost" required class="form-control">
                        </div>
                      </div> --}}



                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label for="discount">Discount</label>
                          <input type="text" class="form-control" id="discount" name="discount" placeholder="0%">

                        </div>
                        <div class="form-group col-md-6">
                          <label for="payment_method">Note</label>
                          <textarea name="note" class="form-control"></textarea>
                        </div>
                      </div>
                      <hr>
                      <div class="form-row">

                        <div class="form-group col-md-6">
                            <label for="">Transaction Account</label>
                            <select name="bank_account_id" class="form-control" required>
                              @foreach (\App\BankAccount::all() as $item)
                              <option value="{{ $item->id }}" {{ old("bank_account_id") == $item->id ? "SELECTED":"" }}>
                                {{ $item->name }}</option>
                              @endforeach
                            </select>
                            @if($errors->has('bank_account_id'))
                            <div class="alert alert-danger">{{ $errors->first('bank_account_id') }}</div>
                            @endif
                        </div>

                        {{-- <div class="form-group col-md-6">
                          <label for="payment_method">Payment Method</label>
                          <select name="payment_method" id="" class="form-control">
                            @foreach (\App\PaymentMethod::all() as $item)
                              <option value="{{ $item->id }}" {{ old("payment_method")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
                            @endforeach
                          </select>
                        </div> --}}

                        {{-- <div class="form-group col-md-4">
                          <label for="">Account</label>
                          <select name="account_id" id="" class="form-control">
                            @foreach (\App\Account::all() as $item)
                              <option value="{{ $item->id }}" {{ old("account_id")==$item->id?"SELECTED":"" }}>{{ $item->name }}</option>
                            @endforeach
                          </select>
                          @if($errors->has('account_id'))
                            <div class="alert alert-danger">{{ $errors->first('account_id') }}</div>
                          @endif
                        </div> --}}

                        <div class="form-group col-md-6">
                          <label for="pay_amount">Pay Amount</label>
                          <div class="input-group">
                            <input type="number" step="any" class="form-control" name="pay_amount" id="pay_amount"
                              placeholder="Pay Amount...">
                            <span class="input-group-btn">
                              <button class="btn btn-warning" type="button" id="paid_btn">PAID!</button>
                            </span>
                          </div>
                        </div>
                      </div>
                      <hr>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-bold btn-secondary" data-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-bold btn-primary" id="order-btn">
                        <i class="fa fa-shopping-cart"></i>
                        Order
                      </button>
                    </div>
                  </div>
                </div>
              </div>
        </form>


      </div>
    </div>
  </div>
</div>
<div class="col-md-6">
  <div id="products">
    @include('pages.pos.products')
  </div>
</div>
</div>
</div>
{{-- Alert Modal --}}
<div class="modal fade" id="alert-modal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body">
        <div class="text-center">
          <h3>Please add some products.</h3>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('styles')
<style>
  hr {
    margin: 5px auto;
  }

  .category{
      max-height: 600px;
      overflow: auto;
  }
.list-group {
    width: fit-content;
  }

  .category .list-group-item .btn {
    text-align: left;
  }
</style>
<link rel="stylesheet" href="{{ asset('dashboard/css/pos.css') }}">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('scripts')
@include('includes.placeholder_model')

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@include('pages.pos.scripts')

<script src="{{ asset('js/modal_form.js') }}"></script>
@endsection
