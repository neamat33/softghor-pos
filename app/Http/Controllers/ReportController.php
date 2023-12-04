<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Expense;
use App\Payment;
use App\Pos;
use App\PosItem;
use App\PosSetting;
use App\Product;
use App\PurchaseItem;
use App\Services\ProductService;
use App\Supplier;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function paginate($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, ['path' => Paginator::resolveCurrentPath()]);
    }

    public function today_report()
    {
        Gate::authorize('today_report');

        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');


        $sells = Pos::where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date)->get();
        $expenses = Expense::whereBetween('expense_date', [$start_date, $end_date])->get();
        $pos_items = ProductService::topSaleProductsDateToDate($start_date, $end_date);
        $payments_paid = Payment::where('payment_type', 'pay')->whereBetween('payment_date', [$start_date, $end_date])->get();
        $payments_received = Payment::where('payment_type', 'receive')->whereBetween('payment_date', [$start_date, $end_date])->get();
        // dd($payments_paid);

        return view('pages.reports.today', compact('sells', 'expenses', 'pos_items', 'payments_paid', 'payments_received'));
    }

    public function current_month_report()
    {
        Gate::authorize('current_month_report');
        $start_date = date('Y-m-1');
        $end_date = date('Y-m-t');


        $sells = Pos::where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date)->get();
        $expenses = Expense::whereBetween('expense_date', [$start_date, $end_date])->get();
        $pos_items = ProductService::topSaleProductsDateToDate($start_date, $end_date);
        $payments_paid = Payment::where('payment_type', 'pay')->whereBetween('payment_date', [$start_date, $end_date])->get();
        $payments_received = Payment::where('payment_type', 'receive')->whereBetween('payment_date', [$start_date, $end_date])->get();


        return view('pages.reports.current_month', compact('sells', 'expenses', 'pos_items', 'payments_paid', 'payments_received'));
    }

    public function summary_report(Request $request)
    {
        Gate::authorize('summary_report');
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }

        $sells = Pos::where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date)->get();
        $expenses = Expense::whereBetween('expense_date', [$start_date, $end_date])->get();
        $top_sold_products = ProductService::topSaleProductsDateToDate($start_date, $end_date);
        $payments_paid = Payment::where('payment_type', 'pay')->whereBetween('payment_date', [$start_date, $end_date])->get();
        $payments_received = Payment::where('payment_type', 'receive')->whereBetween('payment_date', [$start_date, $end_date])->get();


        return view('pages.reports.summary_report', compact('sells', 'expenses', 'top_sold_products', 'payments_paid', 'payments_received','start_date','end_date'));
    }

	public function daily_report(Request $request)
    {
        Gate::authorize('daily_report');

        $start_date = date('Y-m-1');
        $end_date = date('Y-m-t');

        if ($request->start_date) {
            $start_date = $request->start_date;
        }
        if ($request->end_date) {
            $end_date = $request->end_date;
        }

		return view('pages.reports.daily_report',compact('start_date','end_date'));
    }

    public function customer_due(Request $request)
    {
        Gate::authorize('customer_due_report');
        $customers = Customer::query();

        if($request->customer_id){
            $csutomers = $customers->where('id',$request->customer_id);
        }

        $customers = $customers->where('total_receivable','>',0)->get();
        // $customers = $customers->sortByDesc(function ($customer, $key) {
        //     return $customer->total_due();
        // })->filter(function ($customer) {
        //     if ($customer->total_due() > 0) {
        //         return $customer;
        //     }
        // });

        $customers = $this->paginate($customers);

        $filter_customers=Customer::select('id','name')->get();

        return view('pages.reports.customer_due', compact('customers','filter_customers'));
    }

    public function supplier_due(Request $request)
    {
        Gate::authorize('supplier_due_report');
        $suppliers = Supplier::query();

        if($request->supplier_id){
            $suppliers = $suppliers->where('id',$request->customer_id);
        }

        $suppliers = $suppliers->where('total_payable','>',0)->get();

        $suppliers = $this->paginate($suppliers);

        $filter_suppliers=Supplier::select('id','name')->get();

        return view('pages.reports.supplier_due', compact('suppliers','filter_suppliers'));
    }

    public function low_stock(Request $request)
    {
        Gate::authorize('low_stock_report');
        $products = new Product();

        if ($request->product_id != null) {
            $products = $products->where('id', $request->product_id);
            $data['product_id'] = $request->product_id;
        }

        if ($request->code != null) {
            $products = $products->where('code', $request->code);
            $data['code'] = $request->code;
        }

        if ($request->name != null) {
            $products = $products->where('name', 'like', '%' . $request->name . '%');
            $data['name'] = $request->name;
        }

        if ($request->category != null) {
            $products = $products->where('category_id', $request->category);
            $data['category'] = $request->category;
        }

        $low_stock_quantity = PosSetting::first()->low_stock;

        // $products = $products->get()->filter(function ($item) use ($low_stock_quantity) {
        //     if ($item->stock() <= $low_stock_quantity) {
        //         return $item;
        //     }
        // });

        $products=Product::where('main_unit_stock','<',$low_stock_quantity)->get();

        $data['products'] = $this->paginate($products);
        return view('pages.reports.low_stock', $data);
    }

    public function top_customer(Request $request)
    {
        Gate::authorize('top_customer_report');
        $customers = Customer::all();

        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        if ($request->start_date) {
            $start_date = $request->start_date;
        }

        if ($request->end_date) {
            $end_date = $request->end_date;
        }

        $customers = $customers->sortByDesc(function ($customer, $key) use ($start_date, $end_date) {
            return $customer->receivable($start_date, $end_date);
        });

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $data['customers'] = $this->paginate($customers);
        return view('pages.reports.top_customer', $data);
    }

    public function top_product(Request $request)
    {
        Gate::authorize('top_product_report');
        $products = Product::all();

        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        if ($request->start_date) {
            $start_date = $request->start_date;
        }

        if ($request->end_date) {
            $end_date = $request->end_date;
        }

        $products = $products->sortByDesc(function ($product, $key) use ($start_date, $end_date) {
            return $product->sell_count($start_date, $end_date);
        });

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $data['products'] = $products;
        // $this->paginate($products);
        return view('pages.reports.top_products', $data);
    }

    public function top_product_all_time(Request $request)
    {
        Gate::authorize('top_product_all_time_report');
        $products=Product::orderBy('total_sold','desc')->select('id','name','code','main_unit_id','sub_unit_id')->get();
        // $this->paginate($products);
        return view('pages.reports.top_products_all_time', compact('products'));
    }
    public function purchase_report(Request $request)
    {
        Gate::authorize('purchase_report');
        // $data['items']=collect();

        $purchase_items = new PurchaseItem();
        if ($request->product_id) {
            $purchase_items = $purchase_items->where('product_id', $request->product_id);
        }

        if($request->start_date){
            $purchase_items = $purchase_items->whereHas('purchase',function($purchase)use($request){
                $purchase->where('purchase_date','>=',$request->start_date);
            });
        }

        if($request->end_date){
            $purchase_items = $purchase_items->whereHas('purchase',function($purchase)use($request){
                $purchase->where('purchase_date','<=',$request->end_date);
            });
        }

        $purchases = $purchase_items->paginate(20);

        return view('pages.reports.purchase_report', compact('purchases'));
    }

    public function customer_ledger(Request $request)
    {
        Gate::authorize('customer_ledger');
        $transactions = new Transaction();

        if($request->customer_id){
            $transactions = $transactions->where('customer_id',$request->customer_id);
        }

        if($request->start_date){
            $transactions = $transactions->where('date','>=',$request->start_date);
        }

        if($request->end_date){
            $transactions = $transactions->where('date','<=',$request->end_date);
        }

        $transactions = $transactions->get();

        return view('pages.reports.customer_ledger',compact('transactions'));
    }

    public function supplier_ledger(Request $request)
    {
        Gate::authorize('supplier_ledger');
        $transactions = new Transaction();
        if($request->supplier_id){
            $transactions = $transactions->where('supplier_id',$request->supplier_id);
        }

        if($request->start_date){
            $transactions = $transactions->where('date','>=',$request->start_date);
        }

        if($request->end_date){
            $transactions = $transactions->where('date','<=',$request->end_date);
        }

        $transactions = $transactions->get();

        return view('pages.reports.supplier_ledger',compact('transactions'));
    }

    public function profit_loss_report(Request $request)
    {
        Gate::authorize('profit_loss_report');
        $start_date=null;
        $end_date=null;


        if($request->start_date){
            $start_date = date('Y-m-d',strtotime($request->start_date.'-01'));
        }

        if($request->end_date){
            $end_date = date('Y-m-t',strtotime($request->end_date.'-01'));
        }

        return view('pages.reports.profit_loss_report',compact('start_date','end_date'));
    }
}
