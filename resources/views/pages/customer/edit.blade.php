@extends('layouts.master')
@section('title', 'Edit Customer')

@section('page-header')
<header class="header bg-ui-general">
     <div class="header-info">
          <h1 class="header-title">
               <strong>Edit Customer</strong>
          </h1>
     </div>

     <div class="header-action">
          <nav class="nav">
               <a class="nav-link" href="{{ route('customer.index') }}">
                    Customers
               </a>
               <a class="nav-link" href="{{ route('customer.create') }}">
                    <i class="fa fa-plus"></i>
                    New Customer
               </a>
          </nav>
     </div>
</header>
@endsection

@section('content')
<div class="col-12">
     <div class="card">
          <h4 class="card-title">Edit Customer</h4>

          <form action="{{ route('customer.update', $customer->id) }}" method="POST">
               @csrf
               @method('PUT')
               <div class="card-body">
                    <div class="form-row">
                         <div class="col-md-6">
                              <div class="form-group">
                                   <label for="">Customer Name<span class="field_required"></span></label>
                                   <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid': '' }}"
                                        name="name" value="{{ $customer->name }}">
                                   @if($errors->has('name'))
                                   <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                                   @endif
                              </div>
                              <div class="form-group">
                                   <label for="email">Email</label>
                                   <input type="email"
                                        class="form-control {{ $errors->has('email') ? 'is-invalid': '' }}" name="email"
                                        value="{{ $customer->email }}">
                                   @if($errors->has('email'))
                                   <span class="invalid-feedback">{{ $errors->first('email') }}</span>
                                   @endif
                              </div>
                              <div class="form-group">
                                   <label for="phone">Address</label>
                                   <textarea name="address"
                                        class="form-control {{ $errors->has('address') ? 'is-invalid': '' }}">{{ $customer->address }}</textarea>
                                   @if($errors->has('address'))
                                   <span class="invalid-feedback">{{ $errors->first('address') }}</span>
                                   @endif
                              </div>
                         </div>

                         <div class="col-md-6">
                              <div class="form-group">
                                   <label for="phone">Phone<span class="field_required"></span></label>
                                   <input type="text"
                                        class="form-control {{ $errors->has('phone') ? 'is-invalid': '' }}" name="phone"
                                        value="{{ $customer->phone }}">
                                   @if($errors->has('phone'))
                                   <span class="invalid-feedback">{{ $errors->first('phone') }}</span>
                                   @endif
                              </div>
                              {{-- <div class="form-group">
                                   <label for="custom_one">Custom Field One</label>
                                   <input type="text"
                                        class="form-control {{ $errors->has('custom_one') ? 'is-invalid': '' }}"
                                        name="custom_one" value="{{ $customer->custom_one }}">
                                   @if($errors->has('custom_one'))
                                   <span class="invalid-feedback">{{ $errors->first('custom_one') }}</span>
                                   @endif
                              </div>
                              <div class="form-group">
                                   <label for="custom_two">Custom Field Two</label>
                                   <input type="text"
                                        class="form-control {{ $errors->has('custom_two') ? 'is-invalid': '' }}"
                                        name="custom_two" value="{{ $customer->custom_two }}">
                                   @if($errors->has('custom_two'))
                                   <span class="invalid-feedback">{{ $errors->first('custom_two') }}</span>
                                   @endif
                              </div> --}}
                         </div>
                    </div> <!-- End form-row -->
                    <div class="form-row justify-content-center">
                         <button class="btn btn-primary">
                              <i class="fa fa-refresh mr-2"></i>
                              Update
                         </button>
                    </div>
               </div>
          </form>
     </div>
</div>
@endsection

@section('styles')

@endsection

@section('scripts')

@endsection
