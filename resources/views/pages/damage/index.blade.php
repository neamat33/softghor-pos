@extends('layouts.master')
@section('title', 'Damages')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Damages</strong>
            </h1>
        </div>

        {{-- <div class="header-action">
    <nav class="nav">
      <a class="nav-link active" href="{{ route('brand.index') }}">
        Brands
      </a>
       <a class="nav-link" href="{{ route('brand.import') }}">Import Brands</a>
      <a class="nav-link" href="{{ route('brand.create') }}">
        <i class="fa fa-plus"></i>
        Add Brand
      </a>
    </nav>
  </div> --}}
    </header>
@endsection

@section('content')
    <div class="col-12">


        <div class="card card-body mb-2">
            <form action="">
                <div class="form-row">
                    {{-- <div class="form-group col-md-4">
                  <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                          data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                          class="form-control" name="start_date" placeholder="Start Date" autocomplete="off">
              </div>
              <div class="form-group col-md-4">
                  <input type="text" data-provide="datepicker" data-date-today-highlight="true"
                          data-orientation="bottom" data-date-format="yyyy-mm-dd" data-date-autoclose="true"
                          class="form-control" name="end_date" placeholder="End Date" autocomplete="off">
              </div> --}}
                    <div class="form-group col-md-4">
                        <select name="product" id="" class="form-control" data-provide="selectpicker"
                            data-live-search="true" data-size="10">
                            <option value="">Select Product</option>
                            @foreach (\App\Product::all() as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request('product') ? 'SELECTED' : '' }}>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" name="id" value="{{ request('id') }}" class="form-control">
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

        <div class="card print_area">
            <div class="row">
                <div class="col-12" style="display:flex; justify-content:space-between">
                    <h4 class="card-title"><strong>Damages</strong></h4>
                    <a href="" class="btn btn-primary print_hidden mt-2 mr-2" onclick="window.print()"
                        style="height: fit-content;">Print</a>
                </div>
            </div>

            <div class="card-body">
                @if ($damages->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-primary">
                                    <th>#</th>
                                    <th>Id</th>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Note</th>
                                    <th class="print_hidden">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($damages as $key => $item)
                                    <tr>
                                        <td>{{ isset($_GET['page']) ? ($_GET['page'] - 1) * 20 + $key + 1 : $key + 1 }}</td>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ date('d/m/Y', strtotime($item->date)) }}</td>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->product->readable_qty($item->qty) }}</td>
                                        <td>{{ $item->note }}</td>
                                        <td class="print_hidden">
                                            <div class="btn-group">
                                                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fa fa-cogs"></i>
                                                </button>
                                                <div class="dropdown-menu" x-placement="bottom-start">
                                                    {{-- <a class="dropdown-item" href="{{ route('damage.edit', $item->id) }}">
                      <i class="fa fa-edit"></i>
                      Edit
                    </a> --}}

                                                    <a class="dropdown-item delete"
                                                        href="{{ route('damage.destroy', $item->id) }}">
                                                        <i class="fa fa-trash text-danger"></i>
                                                        Delete
                                                    </a>

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $damages->links() }}
                    </div>
                @else
                    <div class="alert alert-danger" role="alert">
                        <strong>You have no damages</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('styles')

@endsection

@section('scripts')
    @include('includes.delete-alert')
@endsection
