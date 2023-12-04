@extends('layouts.master')
@section('title', 'Add Damage')

@section('page-header')
<header class="header bg-ui-general">
  <div class="header-info">
    <h1 class="header-title">
      <strong>Add Damage</strong>
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
  <div class="card">
    <h4 class="card-title"><strong>Add Damage</strong></h4>

    <div class="card-body card-body-soft p-10">

      <form action="{{ route("damage.store") }}" method="POST">
        @csrf
        @livewire('damage.product-unit',['selected_product'=>old('product_id')])


        <div class="form-group">
          <label for="">Date</label>
          <input type="text" data-provide="datepicker" data-date-today-highlight="true" data-date-format="yyyy-mm-dd"
              class="form-control" name="date" value="{{ date('Y-m-d') }}">
          @if($errors->has('date'))
            <div class="alert alert-danger">{{ $errors->first('date') }}</div>
          @endif
        </div>

        <div class="form-group">
          <label for="">Note</label>
          <textarea name="note" id="" cols="30" rows="4" class="form-control">{{ old("note") }}</textarea>
          @if($errors->has('note'))
            <div class="alert alert-danger">{{ $errors->first('note') }}</div>
          @endif
        </div>

        <input type="submit" class="btn btn-success" value="Add Damage">
      </form>


    </div>
  </div>
</div>

@endsection

@section('styles')
    @livewireStyles
@endsection

@section('scripts')
    @livewireScripts
@endsection
