@extends('layouts.master')
@section('title', 'POS Update')

@section('page-header')
    <header class="header bg-ui-general">
        <div class="header-info">
            <h1 class="header-title">
                <strong>Sales Update</strong>
            </h1>
        </div>
    </header>
@endsection

@section('content')

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">`
                <div class="form-row mb-3">
                    <div class="col-md-12">
                        <input type="text" id="product_search" class="form-control"
                               placeholder="Start to write product name..."
                               name="p_name"/>
                        <input type="hidden" id="search_product_id">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card card-body">

                    <form action="{{ route('pos.update', $pos) }}" id="sale-manage-form" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12 mt-4">
                                <table class="table table-bordered">
                                    <thead class="bg-primary">
                                    <tr>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Sub Total</th>
                                        <th>
                                            <i class="fa fa-undo"></i>

                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="tbody">
                                        @foreach($pos->items as $pos_item)
                                        <tr>
                                            <td>{{ $pos_item->product_name }} <input type="hidden"
                                                value="{{ $pos_item->product_name }}" name="name[]">
                                                <input type="hidden" value="{{ $pos_item->product->id }}" name="old_product_id[]">
                                                {{-- <input type="hidden" value="{{ $pos_item->pos_id }}" name="pos_id"> --}}
                                                <input type="hidden" value="{{ $pos_item->id }}" name="old_id[{{ $pos_item->id }}]">
                                            </td>
                                            <td style="width:250px">
                                                <div class="form-row">
                                                    @php
                                                        $product=$pos_item->product;
                                                    @endphp
                                                    @if(!$pos_item->product->sub_unit)
                                                        {{-- ONLY MAIN UNIT --}}
                                                        <input type="text" class="has_sub_unit" hidden value="false">
                                                        <label class="ml-2 mr-2">{{ $product->main_unit->name }}:</label>
                                                        <input type="number" value="{{ $pos_item->main_unit_qty }}" class="form-control col main_qty" name="old_main_qty[{{ $pos_item->id }}]" data-old="{{ $pos_item->main_unit_qty }}" data-value="{{ $product->stock() }}" data-related="{{ $product->main_unit->related_by }}" onkeydown="return event.keyCode !== 190" min="1">
                                                    @else
                                                        {{-- HAS SUB UNIT --}}
                                                        <input type="text" class="has_sub_unit" hidden value="true">
                                                        <input type="text" class="conversion" hidden value="{{ $product->main_unit->related_by }}">
                                                        <label class="mr-1 ml-1">{{ $product->main_unit->name }}:</label>
                                                        <input type="number" value="{{ $pos_item->main_unit_qty }}" class="form-control col main_qty mr-1" name="old_main_qty[{{ $pos_item->id }}]" data-old="{{ $pos_item->main_unit_qty }}" data-value="{{ $product->stock() }}" data-related="{{ $product->main_unit->related_by }}" onkeydown="return event.keyCode !== 190" min="1">
                                                        <label class="mr-1">{{ $product->sub_unit->name }}:</label>
                                                        <input type="number" value="{{ $pos_item->sub_unit_qty }}" class="form-control col sub_qty mr-1" name="old_sub_qty[{{ $pos_item->id }}]" data-old="{{ $pos_item->sub_unit_qty }}"  onkeydown="return event.keyCode !== 190" min="1" max="{{ $product->main_unit->related_by-1 }}">
                                                    @endif
                                                    {{-- <div class="plusminus horiz">
                                                        <input type="number" data-check="${data.checkSaleOverStock}"
                                                            data-old="{{ $pos_item->qty }}" data-value="{{ $pos_item->product->stock() }}" class="old_qty form-control" name="old_qty[]"
                                                            value="{{ $pos_item->qty }}"/>
                                                    </div> --}}
                                                </div>
                                            </td>
                                            <td style="width:100px">
                                                <input type="text" value="{{ $pos_item->rate }}"
                                                       class="form-control rate" name="old_rate[{{ $pos_item->id }}]"/>
                                            </td>
                                            <td style="width:150px">
                                                <input type="text" readonly name="old_sub_total[{{ $pos_item->id }}]"
                                                       class="form-control sub_total"
                                                       value="{{ $pos_item->sub_total }}"/>
                                            </td>
                                            <td>
                                                <a href="#" class="item-index"
                                                   onclick="partial_handle({{ $pos_item->id }})"><i
                                                        class="fa fa-undo"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <td></td>
                                    </tbody>
                                    <tfoot class="bg-danger">
                                    <tr>
                                        <th class="text-center" colspan="2">Total Qty: <strong
                                                id="totalQty">{{ $pos->items()->sum('qty') }}</strong>
                                        </th>
                                        <th class="text-center" colspan="3">Total: <strong
                                                id="totalAmount">{{ $pos->items()->sum('sub_total') }}</strong> Tk
                                        </th>
                                    </tr>
                                    </tfoot>
                                </table>

                                <div class="form-gorup text-center">
                                    <button type="submit" id="submit-btn" class="btn btn-primary">
                                        <i class="fa fa-undo"></i>
                                        Update
                                    </button>

                                </div>
                            </div>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>

    <div class="modal fade show" id="partial-confirm-modal" tabindex="-1" aria-modal="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">You want to Return ?</h4>
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<form id="partial-delete-form" action="" method="POST">
				@csrf
				{{--                    @method('DELETE')--}}
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">No. Back !</button>
					<button type="submit" class="btn btn-primary">Yes, Delete</button>
				</div>
			</form>
		</div>
	</div>
</div>


@endsection

@section('styles')
    <style>
        hr {
            margin: 5px auto;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('dashboard/css/pos.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    @include('pages.pos.edit-scripts')


    <script>
        var db_pid_array;
        // product search

        $(document).ready(function () {
            getPosItemId({{$pos->id}});
            // console.log(db_pid_array);
        });


        $(function () {

            $("#product_search").autocomplete({
          source: function (req, res) {
            let url = "{{ route('product-search') }}";
            $.get(url, {req: req.term}, (data) => {
              res($.map(data, function (item) {
                return {
                  id: item.id,
                  value: item.name+" "+item.code,
                  price: item.price
                }
              })); // end res

            });
          },
          select: function (event, ui) {

            $(this).val(ui.item.value);
            $("#search_product_id").val(ui.item.id);
            let url = "{{ route('product.details', 'placeholder_id') }}".replace('placeholder_id', ui.item.id);
            $.get(url, (product) => {
                console.log(product);
                // check stock
                  if(product.stock <= 0) {
                    toastr.warning('This product is Stock out. Please Purchases the Product.');
                    return false;
                  }


                if (pExist(product.id) == true) {
                    toastr.warning('Please Increase the quantity.');
                } else {
                    addProductToCard(product);
                }

            });

            $(this).val('');

            return false;
          },
          response: function (event, ui) {
            if(ui.content.length == 1) {
              ui.item = ui.content[0];
              $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
              $(this).autocomplete('close');

            }
          },
          minLength: 0
     });

        });

        //  Set Product Id
        function productSelected(id) {
            console.log(id);

        }


        function partial_handle(id) {
			//alert('HELLO');
            var url = "{{ route('pos.partial_destroy', 'id') }}".replace('id', id);
            $("#partial-delete-form").attr('action', url);
            $("#partial-confirm-modal").modal('show');
        }


    </script>


@endsection
