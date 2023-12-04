<?php

namespace App\Http\Controllers;

use App\ActualPayment;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-customer',  ['only' => ['create', 'store']]);
        $this->middleware('can:edit-customer',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-customer', ['only' => ['destroy']]);
        $this->middleware('can:list-customer', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);

        $this->middleware('can:customer-wallet_payment', ['only' => ['wallet_payment','store_wallet_payment']]);
        $this->middleware('can:customer-report', ['only' => ['report']]);
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->all());
        // dd(Customer::first());
        $customers = Customer::query();

        if ($request->name != null) {
            $customers = $customers->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->mobile != null) {
            // $customers=$customers->where('phone','like','%'.$request->phone.'%');
            $customers = $customers->where('phone', $request->mobile);
        }

        $customers = $customers->orderBy('id', 'DESC')->paginate(20);
        // dd($customers);

        return view('pages.customer.index')->withCustomers($customers);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.customer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'phone' => 'required|unique:customers',
            'email' => 'nullable|email'
            // 'address' => 'required',
        ]);
        $data = $request->all();
        $data['opening_receivable'] = $request->opening_receivable != null ? $request->opening_receivable : 0;
        $data['opening_payable'] = $request->opening_payable != null ? $request->opening_payable : 0;


        Customer::create($data);
        session()->flash('success', 'Customer Created...');

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('pages.customer.edit')->withCustomer($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'phone' => 'required',
            'email' => 'nullable|email'
        ]);

        $data = $request->all();


        $customer->update($data);

        session()->flash('success', 'Customer Information Update');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        if ($customer->delete()) {
            session()->flash('success', 'Customer Deleted...');
        } else {
            session()->flash('warning', 'Customer Don\'t Delete. Please Check the Customer Actions.');
        }


        return back();
    }

    public function customers()
    {
        $customers = Customer::latest()->get(['id', 'name', 'phone']);
        return response()->json($customers);
    }

    public function customer_due($id)
    {
        $customer = Customer::findOrFail($id);
        // $dues = $customer->due_pos();
        $data = [
            'customer_name' => $customer->name,
            'due_invoice' => $customer->due_invoice_count(),
            'sell_due' => $customer->sell_due(),
            'walletBalance' => $customer->wallet_balance(),
            'total_due' =>$customer->total_due()
        ];
        return response()->json($data);
    }

    public function wallet_payment(Customer $customer)
    {
        return view('pages.customer.forms.wallet_payment', compact('customer'));
    }

    public function store_wallet_payment(Request $request, Customer $customer)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "payment_date" => "required",
            "pay_amount" => [
                'required', function ($attribute, $value, $fail) use ($customer, $request) {
                    // dd($value);
                    if ($request->pay_amount > $customer->wallet_balance()) {
                        $wallet_balance = $customer->wallet_balance()>0?$customer->wallet_balance():0;
                        return $fail('Customer wallet has ' . $wallet_balance . ' Tk');
                    }

                    if ($customer->due() < $request->pay_amount) {
                        return $fail('Over Payment not Alowed! Due is ' . $customer->due() . ' Tk');
                    }
                }
            ],
            'bank_account_id'=>'integer|required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        // return $request->all();
        $actual_payment              = new ActualPayment();
        $actual_payment->customer_id = $customer->id;
        $actual_payment->wallet_payment = 1;
        $actual_payment->amount      = $request->pay_amount;
        $actual_payment->date        = $request->payment_date;
        $actual_payment->payment_type = 'receive';
        $actual_payment->note         = $request->note;
        $actual_payment->save();


        $due_pos_list  = $customer->sales()->where('due', '>', 0)->get();
        $tempAmount = $request->pay_amount;;
        if ($due_pos_list->count() > 0) {
            foreach ($due_pos_list as $due_pos) {
                $due_amount = $due_pos->due;


                if (
                    $tempAmount >= 0 && $due_amount <= $tempAmount
                ) {
                    $tempAmount = $tempAmount - $due_amount;
                    // Due Amount full paid
                    $due_pos->payments()->create([
                        // 'transaction_id' => $request->transaction_id,
                        'actual_payment_id' => $actual_payment->id,
                        'bank_account_id'   => $request->bank_account_id,
                        'wallet_payment'    => 1,
                        'payment_date'      => $request->payment_date,
                        'payment_type'      => 'receive',
                        'pay_amount'        => $due_amount,
                        'method'            => $request->method,

                    ]);
                } else {
                    // Due amount in pay extra amount
                    $due_pos->payments()->create([
                        // 'transaction_id' => $request->transaction_id,
                        'actual_payment_id' => $actual_payment->id,
                        'bank_account_id'   => $request->bank_account_id,
                        'wallet_payment'    => 1,
                        'payment_date'      => $request->payment_date,
                        'payment_type'      => 'receive',
                        'pay_amount'        => $tempAmount,
                        'method'            => $request->method,
                        // 'note'              => $request->note,
                    ]);
                    $tempAmount = 0;
                    break;
                }
            }
        }
    }

    public function report(Customer $customer)
    {
        return view('pages.customer.report',compact('customer'));
    }
}
