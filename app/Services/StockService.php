<?php
namespace App\Services;

use App\PosItem;
use App\Product;
use App\PurchaseItem;

class StockService{
    public static function return_purchase_ids_and_qty_for_the_sell($product_id, $qty)
    {
        // dd("HE");
        // if not enough stock return error
        $product = Product::find($product_id);
        // dd($product->stock());
        if ($product->stock() < $qty || $qty == 0) {
            return $data = [];
        }

        $purchase_items = PurchaseItem::where('product_id', $product_id)
            // ->where('remaining', '>', '0')
            ->get();
        // $purchase_items=$purchase_items->where()
        $purchase_items = $purchase_items->filter(function ($item) {
            // dd($item);
            if ($item->remaining() > 0) {
                return  $item;
            }
        })->values();


        $data = [];
        $total_price = 0;
        $placeholder_qty = $qty;

        foreach ($purchase_items as $item) {
            if ($placeholder_qty <= 0) {
                break;
            }
            // dd("remaining: ".$item->remaining());
            if ($item->remaining() >= $placeholder_qty) {
                $data['purchase_items'][] = [
                    'purchase_item_id' => $item->id,
                    'purchase_id' => $item->purchase_id,
                    'qty' => $placeholder_qty,
                    'price' => $item->rate
                ];

                $product = $item->product;

                $total_price += $product->quantity_worth($placeholder_qty, $item->rate);

                $placeholder_qty = 0;
            } else {
                $data['purchase_items'][] = [
                    'purchase_item_id' => $item->id,
                    'purchase_id' => $item->purchase_id,
                    'qty' => $item->remaining(),
                    'price' => $item->rate
                ];

                $total_price += $product->quantity_worth($item->remaining(), $item->rate);

                $placeholder_qty -= $item->remaining();
            }
        }

        $average_price = $total_price / $qty;

        $data['average_price'] = $average_price;
        $data['total_price'] = $total_price;

        return $data;
    }

    public static function add_new_pos_items_and_recalculate_cost($request, $pos)
    {

        if ($request->product_id) {



            // dd($request->name);
            foreach ($request->product_id as $key => $value) {
                // dd($request->qty[$key]);
                $main_qty = 0;
                $sub_qty = 0;

                if ($request->main_qty&&array_key_exists($request->product_id[$key], $request->main_qty)) {
                    $main_qty = $request->main_qty[$request->product_id[$key]];
                }

                if ($request->sub_qty&&array_key_exists($request->product_id[$key], $request->sub_qty)) {
                    $sub_qty = $request->sub_qty[$request->product_id[$key]];
                }

                if($main_qty==0&&$sub_qty==0){
                    throw new \Exception('Quantity Empty');
                }

                $product = Product::find($request->product_id[$key]);
                $qty = $product->to_sub_quantity($main_qty, $sub_qty);

                $purchase_distribution = StockService::return_purchase_ids_and_qty_for_the_sell($request->product_id[$key], $qty);
                // dd($purchase_distribution);
                if (isset($purchase_distribution['purchase_items'])) {
                    // Insert POS Item
                    $pos_item = PosItem::create([
                        'pos_id'       => $pos->id,
                        'product_name' => $request->name[$key],
                        'product_id'   => $request->product_id[$key],
                        'rate'         => $request->rate[$key],
                        // 'unit_cost'    => $purchase_distribution['average_price'],
                        'total_purchase_cost'    => $purchase_distribution['total_price'],
                        'main_unit_qty' =>  $main_qty,
                        'sub_unit_qty'  => $sub_qty,
                        'qty'          => $qty,
                        'sub_total'    => $request->sub_total[$key]
                    ]);



                    foreach ($purchase_distribution['purchase_items'] as $pd_key => $pd_value) {
                        // insert into Stock Table
                        $pos_item->stock()->create([
                            'purchase_id' => $pd_value['purchase_id'],
                            'purchase_item_id' => $pd_value['purchase_item_id'],
                            'product_id' => $request->product_id[$key],
                            'qty' => $pd_value['qty']
                        ]);
                    }
                } else {
                    throw new \Exception('Low Stock');
                }
            }
        }

        // return $problems;
    }

    // When Sell Edited - Update
    public static function update_pos_items_and_recalculate_cost($request, $pos)
    {
        // now both or any field can change

        foreach ($request->old_id as $key => $value) {
            info($value);
            // check if quantity changed
            $main_qty = 0;
            $sub_qty = 0;

            if ($request->old_main_qty&&array_key_exists($request->old_id[$key], $request->old_main_qty)) {
                $main_qty = $request->old_main_qty[$request->old_id[$key]];
            }

            if ($request->old_sub_qty&&array_key_exists($request->old_id[$key], $request->old_sub_qty)) {
                $sub_qty = $request->old_sub_qty[$request->old_id[$key]];
            }
            // dd($main_qty);
            $pos_item = PosItem::find($value);

            $product = $pos_item->product;
            $qty = $product->to_sub_quantity($main_qty, $sub_qty);



            // quantity changed
            if ($pos_item->qty != $qty) {


                // Quantity Increased
                if ($qty > $pos_item->qty) {

                    $new_quantity = $qty - $pos_item->qty;

                    // check if existing purchase_item has ->required quantity remaining
                    foreach ($pos_item->stock as $stock_item) {
                        // IF Existing purchase IDs - can fullfill the requested amount - then update
                        // dd($item->purchase_item->remaining());
                        if ($stock_item->purchase_item->remaining() > $new_quantity) {
                            $existing_quantity = $stock_item->qty;

                            $pos_item->update([
                                'main_unit_qty' => $main_qty,
                                'sub_unit_qty' => $sub_qty,
                                'qty'       => $qty,
                                'rate'      => $request->old_rate[$key],
                                'sub_total' => $request->old_sub_total[$key]
                            ]);

                            $stock_item->update([
                                'qty' => $existing_quantity + $new_quantity
                            ]);
                            break;
                        }
                    }

                    // dd("HELLO");
                    // chech if stock table updated properly

                    $stock_quantity = $pos_item->stock()->sum('qty');
                    if ($stock_quantity == $qty) {
                        // since updated nothing to do
                    } else {
                        // since Existing Records couldn't fullfill the required quantity
                        // get new purhase_ids and insert that data
                        $purchase_distribution = StockService::return_purchase_ids_and_qty_for_the_sell($pos_item->product_id, $new_quantity);
                        // dd($purchase_distribution);

                        // if There is stock -> then
                        if (isset($purchase_distribution['purchase_items'])) {
                            // dd($request->old_qty[$key]);
                            $pos_item->update([
                                'main_unit_qty' => $main_qty,
                                'sub_unit_qty' => $sub_qty,
                                'qty'       => $qty,
                                'rate'      => $request->old_rate[$key],
                                'sub_total' => $request->old_sub_total[$key]
                            ]);

                            // - Update - Stock
                            // Update PosItem
                            foreach ($purchase_distribution['purchase_items'] as $p_dist_key => $p_dist_value) {
                                // check database to see if --Existing purchased_id mathes
                                $stock = $pos_item->stock()->where('purchase_item_id', $p_dist_value['purchase_item_id'])->first();
                                if ($stock) {
                                    $stock->update([
                                        'qty' => $stock->qty + $p_dist_value['qty']
                                    ]);
                                } else {
                                    // insert stock
                                    $pos_item->stock()->create([
                                        'purchase_id' => $p_dist_value['purchase_id'],
                                        'purchase_item_id' => $p_dist_value['purchase_item_id'],
                                        'product_id' => $pos_item->product_id,
                                        'qty' => $p_dist_value['qty']
                                    ]);
                                }
                            }
                        } else {
                            // Error - Not enought Stock
                            // $product = Product::find($pos_item->product_id);
                            session()->flash('warning', $product->name . ' Doesn\'t have enough stock. So could not be updated.');
                        }
                    }
                }
                // Quantity Decreased
                else {
                    $extra_quantity = $pos_item->qty - $qty;

                    // if single stock > change_req_quantity
                    $stock = $pos_item->stock()->where('qty', '>=', $qty)->first();

                    if ($stock && $stock->qty > $extra_quantity) {
                        //Just update this one and update pos_item
                        $stock->update([
                            'qty' => $stock->qty - $extra_quantity
                        ]);

                        $pos_item->update([
                            'main_unit_qty' => $main_qty,
                            'sub_unit_qty' => $sub_qty,
                            'qty'       => $qty,
                            'rate'      => $request->old_rate[$key],
                            'sub_total' => $request->old_sub_total[$key]
                        ]);
                    } else if ($stock && $stock->qty == $extra_quantity) {
                        // delete stock and update
                        $stock->delete();
                        $pos_item->update([
                            'main_unit_qty' => $main_qty,
                            'sub_unit_qty' => $sub_qty,
                            'qty'       => $qty,
                            'rate'      => $request->old_rate[$key],
                            'sub_total' => $request->old_sub_total[$key]
                        ]);
                    } else {
                        // new quantity can not be Decreased from a single stock

                        // Update all the necessary items and update pos_item
                        $place_holder_quantity = $extra_quantity;
                        foreach ($pos_item->stock as $stock_key => $stock_value) {
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
                            // $stock->update([
                            //     'qty' => $stock->quantity - $extra_quantity
                            // ]);


                            if ($place_holder_quantity == 0) {
                                break;
                            }
                        }

                        // Update PosItem
                        $pos_item->update([
                            'main_unit_qty' => $main_qty,
                            'sub_unit_qty' => $sub_qty,
                            'qty'       => $qty,
                            'rate'      => $request->old_rate[$key],
                            'sub_total' => $request->old_sub_total[$key]
                        ]);
                    }
                }
            } elseif ($pos_item->rate != $request->old_rate[$key]) {
                $pos_item->update([
                    'rate'      => $request->old_rate[$key],
                    'sub_total' => $request->old_sub_total[$key]
                ]);
            } else {
                // Nothing Changed
            }


            // update purchase cost
            $pos_item->update_total_purchase_cost();
        }
    }

    // When product Returned
    public static function handle_return_stock($pos_item, $return_item, $qty)
    {
        // dd("HELLO");
        // 1.get sold item amount - data ->stock table consists the necessary information
        // purchase_id=1
        // sold 2
        // purchase_id=2
        // sold 3

        $temp_quanity = $qty;

        foreach ($pos_item->stock as $stock) {
            if ($temp_quanity == 0) {
                break;
            }

            // IF Existing purchase IDs - can fullfill the requested amount - then update
            // dd($item->purchase_item->remaining());

            // find stock- remaining quantity

            // dd($stock->remaining());

            if ($stock->remaining() == $temp_quanity) {
                $return_item->stock()->create([
                    'purchase_id' => $stock->purchase_id,
                    'purchase_item_id' => $stock->purchase_item_id,
                    'stock_id' => $stock->id,
                    'product_id' => $stock->product_id,
                    'qty' => $temp_quanity,
                    'out'   => 0,
                ]);
                break;
            } else if ($stock->remaining() > $temp_quanity) {
                // dd("Temp Quantity".$temp_quanity);
                $return_item->stock()->create([
                    'purchase_id' => $stock->purchase_id,
                    'purchase_item_id' => $stock->purchase_item_id,
                    'stock_id' => $stock->id,
                    'product_id' => $stock->product_id,
                    'qty' => $temp_quanity,
                    'out'   => 0,
                ]);
                break;
            } else if ($stock->remaining() != 0 && $stock->remaining() < $temp_quanity) {
                // dd($temp_quanity);
                // dd("SMALLER");
                // echo $temp_quanity."<br>";
                $remaining_stock = $stock->remaining();
                // dd($remaining_stock);
                $return_item->stock()->create([
                    'purchase_id' => $stock->purchase_id,
                    'purchase_item_id' => $stock->purchase_item_id,
                    'stock_id' => $stock->id,
                    'product_id' => $stock->product_id,
                    'qty' => $remaining_stock,
                    'out'   => 0,
                ]);
                $temp_quanity -= $remaining_stock;
                // dd($temp_quanity);
            }
        }
        // exit();

    }
    
}
