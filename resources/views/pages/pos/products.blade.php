<div class="card">
    <div class="row">
        <div class="col-md-3">
            <div class="card-header">
                <h3 class="card-title">Category</h3>
            </div>
            <div class="card-body category">
                <ul class="list-group">
                    @foreach(\App\Category::orderBy('name', 'ASC')->get() as $item)
                        <li class="list-group-item px-0 py-1"><a href="#"
                                                                 class="btn btn-primary btn-block"
                                                                 onclick="getProductsByCat({{ $item->id }})">{{ $item->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card-header">
                <h3 class="card-title">Product List</h3>
            </div>
            <div class="card-body">
                {{-- <div class="row"> --}}
                {{-- <div class="col-12"> --}}
                <form action="{{ route('pos.products') }}" class="product-filter">
                    <div class="row">
                        <div class="col-md-6" style="padding-left:4px;">
                            <input type="text" name="code" class="form-control code">
                        </div>
                        <div class="col-md-6">
                            <input type="submit" class="btn btn-success" value="Search">
                            <a href="{{ route('pos.create') }}" class="btn btn-info">Reset</a>
                        </div>
                    </div>
                </form>
                {{-- </div> --}}
                {{-- </div> --}}
                <div class="row">
                    @forelse($products as $product)
                        <div class=" product text-center col-md-3 col-sm-4 product" data-value="{{ $product->id }}">
                            <img src="/{{ $product->image }}" class="align-self-start img-thumbnail"
                                 alt="{{ $product->name }}"
                                 style="width:80px"/>
                            <br/>
                            <span>{{ $product->name." - ".$product->code }}</span>
                            <br/>
                            <small class="font-weight-bold">{{ $product->price }}</small> Tk
                            <br>
                            <small class="stock">Stock : {{ $product->readable_qty($product->stock) }}</small>
                        </div>
                    @empty
                        <div class="alert alert-danger" role="alert">
                            Products not available! Please add.
                        </div>
                    @endforelse
                </div>
                {!! $products->appends(request()->query())->links() !!}
            </div>
        </div>
    </div>
</div>

