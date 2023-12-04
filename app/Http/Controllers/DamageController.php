<?php

namespace App\Http\Controllers;

use App\Damage;
use App\Product;
use App\Services\StockService;
use App\Stock;
use Illuminate\Http\Request;

class DamageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-damage',  ['only' => ['create', 'store']]);
        $this->middleware('can:edit-damage',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-damage', ['only' => ['destroy']]);
        $this->middleware('can:list-damage', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $damages = new Damage();
        if ($request->product) {
            $damages = $damages->where('product_id', $request->product);
        }

        if($request->id){
            $damages = $damages->where('id',$request->id);
        }

        $damages = $damages->paginate(20);
        return view('pages.damage.index', compact('damages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.damage.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'product_id' => 'required',
            'date' => 'required',
            'main_unit_qty'=>'nullable|integer',
            'sub_unit_qty'=>'nullable|integer',
            'note'=>'nullable|string|max:2000'
        ]);

        $main_qty=$request->main_unit_qty??0;
        $sub_qty=$request->sub_unit_qty??0;
        $product=Product::find($request->product_id);
        $sub_quantity=$product->to_sub_quantity($main_qty,$sub_qty);

        // check stock

        $purchases = StockService::return_purchase_ids_and_qty_for_the_sell($request->product_id, $sub_quantity);
        // dd($purchases);
        if (count($purchases)>0&&array_key_exists('purchase_items', $purchases)) {
            $damage_data = $validated;
            $damage_data['qty'] = $sub_quantity;

            $damage = Damage::create($damage_data);


            foreach ($purchases['purchase_items'] as $key => $purcahse) {
                $damage->stock()->create([
                    'purchase_id' => $purcahse['purchase_id'],
                    'purchase_item_id' => $purcahse['purchase_item_id'],
                    'product_id' => $request->product_id,
                    'qty' => $purcahse['qty']
                ]);
            }

            session()->flash('success', 'Damage Added');
        } else {
            session()->flash('warning', 'Not enough Stock!');
            return back();
        }





        // dd($request->all());

        return redirect()->route('damage.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Damage  $damage
     * @return \Illuminate\Http\Response
     */
    public function show(Damage $damage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Damage  $damage
     * @return \Illuminate\Http\Response
     */
    public function edit(Damage $damage)
    {
        // dd($damage);
        return view('pages.damage.edit', compact('damage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Damage  $damage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Damage $damage)
    {
        $request->validate([
            'product_id' => 'required',
            'qty' => 'required',
            'date' => 'required',
        ]);



        // find change
        if ($damage->qty > $request->qty) {
            // decreased
            $diff = $damage->qty - $request->qty;
            $damage->update($request->all());

            // if single stock > change_req_quantity

            // Update all the necessary items and update pos_item
            $place_holder_quantity = $diff;
            foreach ($damage->stock as $stock_key => $stock_value) {
                if ($stock_value->qty == $place_holder_quantity) {
                    $stock_value->delete();
                    $place_holder_quantity = 0;
                } elseif ($stock_value->qty < $place_holder_quantity) {
                    // delete & update placeholder
                    $place_holder_quantity -= $stock_value->qty;
                    $stock_value->delete();
                } else {
                    // stock quantity is greater than placeholder
                    $stock_value->update([
                        'qty' => $stock_value->qty - $place_holder_quantity
                    ]);
                    $place_holder_quantity = 0;
                }
                if ($place_holder_quantity == 0) {
                    break;
                }
            }

            $damage->update($request->all());
            session()->flash('success', 'Damage Updated');
        } else {
            // increased
            $diff =  $request->qty - $damage->qty;

            $purchase_distribution = StockService::return_purchase_ids_and_qty_for_the_sell($damage->product_id, $diff);
            // dd($purchase_distribution);
            if (isset($purchase_distribution['purchase_items'])) {
                // dd($request->old_qty[$key]);
                $damage->update($request->all());

                // - Update - Stock
                // Update PosItem
                foreach ($purchase_distribution['purchase_items'] as $p_dist_key => $p_dist_value) {
                    // dd($p_dist_value);
                    // check database to see if --Existing purchased_id mathes
                    $stock = $damage->stock()->where('purchase_item_id', $p_dist_value['purchase_item_id'])->first();
                    if ($stock) {
                        $stock->update([
                            'qty' => $stock->qty + $p_dist_value['qty']
                        ]);
                    } else {
                        // insert stock
                        $damage->stock()->create([
                            'purchase_id' => $p_dist_value['purchase_id'],
                            'purchase_item_id' => $p_dist_value['purchase_item_id'],
                            'product_id' => $damage->product_id,
                            'qty' => $p_dist_value['qty']
                        ]);
                    }
                }

                session()->flash('success', 'Damage Updated');
            } else {
                // Error - Not enought Stock
                $product = Product::find($damage->product_id);
                session()->flash('warning', $product->name . ' Doesn\'t have enough stock. So could not be updated.');
            }
        }

        return redirect()->route('damage.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Damage  $damage
     * @return \Illuminate\Http\Response
     */
    public function destroy(Damage $damage)
    {
        // $damage->stock()->delete();
        $damage->delete();
        session()->flash('success', 'Damage Deleted');
        return redirect()->route('damage.index');
    }
}
