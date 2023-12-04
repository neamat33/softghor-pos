<?php

namespace App\Http\Controllers;

use App\ActualPayment;
use App\Customer;
use App\Payment;
use App\Services\PaymentService;
use App\Supplier;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:create-payment',  ['only' => ['create', 'store']]);
        // $this->middleware('can:edit-expense',  ['only' => ['edit', 'update']]);
        $this->middleware('can:delete-payment', ['only' => ['destroy']]);
        $this->middleware('can:list-payment', ['only' => ['index']]);
        // $this->middleware('can:show-customer', ['only' => ['show']]);

        $this->middleware('can:payment_receipt', ['only' => ['payment_receipt']]);

    }

    public function index(Request $request)
    {
        $payments       = ActualPayment::query();
        $customers      = Customer::all();
        $suppliers      = Supplier::all();

        if($request->customer) {
            $payments = $payments->where('customer_id', $request->customer);
        }

        if($request->supplier) {
            $payments = $payments->where('supplier_id', $request->supplier);
        }

        if($request->start_date){
            $payments = $payments->where('date','>=',$request->start_date);
        }

        if($request->end_date){
            $payments = $payments->where('date','<=',$request->end_date);
        }



        return view('pages.payments.index')->with(['customers'=> $customers, 'suppliers' => $suppliers, 'payments'=>$payments->latest()->paginate(20)]);
    }


    public function create()
    {
        return view('pages.payments.create');
    }

    // public function supplier_create()
    // {
    //     return view('pages.payments.supplier_create');
    // }

    // public function customer_create()
    // {
    //     return view('pages.payments.customer_create');
    // }



    public function send_customer_sms($request, $customer, $requestAmount)
    {
        if ($request->sms != null) {
            // $customer = Customer::find($request->customer);

            $name = $customer->name;
            // $order_id = $pos->id;
            // $payable = $pos->payable;
            // $paid = $pos->paid();
            $due = $customer->sell_due();

            $sms_body = "Dear " . $name . ", Payment Tk." . $requestAmount . " has been successfully done. Current Due: Tk. " . $due . " Dated " . date("d/m/Y", strtotime($request->payment_date)) . " \n";



            $sms_body .= "Note : " . $request->note . "\n";

            $sms_body .= "--Oriental Metal and Engineering Works";

            // dd($sms_body);
            $mobile_number = "88" . $customer->phone;
            // $mobile_number = "8801741045212";
            // $mobile_number = "8801779724380";
            // SmsHelper::sendSms($mobile_number, $sms_body);
            // $mobile_number="8801741045212";

        }
    }






    public function send_supplier_sms($request, $supplier, $requestAmount)
    {
        if ($request->sms != null) {
            // $customer = Customer::find($request->customer);

            $name = $supplier->name;
            // $order_id = $pos->id;
            // $payable = $pos->payable;
            // $paid = $pos->paid();
            $due = $supplier->purchase_due();
            $sms_body = "Dear " . $name . ", Payment Tk." . $requestAmount . " has been successfully done. Current Due: Tk. " . $due . " Dated " . date("d/m/Y", strtotime($request->payment_date)) . " \n";

            $sms_body .= "Note : " . $request->note . "\n";
            $sms_body .= "--Oriental Metal and Engineering Works";
            // dd($sms_body);
            $mobile_number = "88" . $supplier->phone;
            // $mobile_number = "8801741045212";
            // $mobile_number = "8801779724380";
            SmsHelper::sendSms($mobile_number, $sms_body);
            // $mobile_number="8801741045212";

        }
    }


    public function store(Request $request)
    {

        $this->validate($request, [
            'payment_date'     => 'required',
            'payment_type' => 'required',
            'account_type'     => 'required',
            'account_id'       => 'required',
            'amount'           => 'required',
            // 'method'           => 'required',
            // 'transaction_id' => 'required|unique:payments',
        ]);

        // dd($request->all());

        if ($request->account_type == 'customer') {
            $actual_payment = PaymentService::add_customer_payment($request);
        }


        if ($request->account_type == 'supplier') {
            $actual_payment = PaymentService::add_supplier_payment($request);
        }


        return redirect()->route('payment_receipt', $actual_payment->id);
    }

    public function destroy(ActualPayment $payment)
    {
        // $actual_payment = ActualPayment::where('id', $payment->actual_payment_id)->first();
        // if ($actual_payment != null) {
        //     $actual_payment->amount = $actual_payment->amount - $payment->pay_amount;
        //     $actual_payment->save();
        //     if ($actual_payment->amount == 0) {
        //         $actual_payment->delete();
        //     }
        // }

        if ($payment->delete()) {
            session()->flash('success', 'Payment Delete Success');
        } else {
            session()->flash('warning', 'Deletion Failed!');
        }
        return back();
    }

    public function payment_receipt(ActualPayment $actual_payment)
    {
        // $payment       = ActualPayment::find($payment_id);
        // $payment_items = Payment::where('actual_payment_id', $payment->id)->get();
        // dd($actual_payment);
        // dd($actual_payment->payments);
        return view('pages.payments.receipt')->with(['payment' => $actual_payment, 'payment_items' => $actual_payment->payments]);
    }

    public function partial_delete(Payment $payment)
    {
        if ($payment->delete()) {
            session()->flash('success', 'Payment Delete Success');
        } else {
            session()->flash('warning', 'Deletion Failed!');
        }
        return back();
    }
}
