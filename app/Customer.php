<?php

namespace App;

use App\Payment;
use App\Pos;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    /****************************
     * RELATIONS
     ***************************/
    public function actual_payments()
    {
        return $this->hasMany(ActualPayment::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class,'customer_id');
    }

    public function sales()
    {
        return $this->hasMany(Pos::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


    public function opening_transactions()
    {
        return $this->morphMany(Transaction::class, 'transactable');
    }
    /****************************
     * HELPER METHODS
     ***************************/
    public function due_invoice_count()
    {
        return $this->sales()->where('due','>',0)->count();
    }

    public function sell_due()
    {
        return $this->sales()->sum('due');
    }


    public function direct_received()
    {
        return $this->payments()->where('payment_type', 'receive')->sum('pay_amount');
            // ->get()->sum(function ($payment) {
            //     return $payment->pay_amount + $payment->discount;
            // });
    }

    public function direct_paid()
    {
        return $this->payments()->where('payment_type', 'pay')->sum('pay_amount');
            // ->get()->sum(function ($payment) {
            //     return $payment->pay_amount + $payment->discount;
            // });
    }

    public function received_from_wallet()
    {
        // return 0;
        return $this->actual_payments()
            ->where('payment_type', 'receive')
            ->where('wallet_payment', 1)
            ->sum('amount');
    }

    public function wallet_balance()
    {
        $amount = 0;
        // return  4000;
        // return $this->received_from_wallet();
        if ($this->opening_receivable != null && $this->opening_receivable != 0) {
            $amount -= abs($this->opening_receivable);
        }
        if ($this->opening_payable != null && $this->opening_payable != 0) {
            $amount += abs($this->opening_payable);
        }

        $amount += $this->direct_received() - $this->direct_paid() - $this->received_from_wallet();
        return $amount;
        return $amount > 0 ? $amount : 0;
    }

    public function previous_due($invoice_id) {
        return bcsub($this->receivable(null,null,$invoice_id) , $this->paid($invoice_id));
    }

    public function receivable($start_date = null, $end_date = null, $invoice_id=null)
    {
        $amount = 0;
        //if ($this->opening_balance > 0) {
        //  $amount = $this->opening_balance;
        //}
        $sales=$this->sales();
        if ($start_date != null && $end_date != null) {
            $sales= $sales->where('sale_date', '>=', $start_date)->where('sale_date', '<=', $end_date);
        } 

        if($invoice_id){
            $sales=$sales->where('id','<',$invoice_id);
        }

        return $sales->sum('final_receivable');

    }

    public function paid_as_return()
    {
        return $this->returns()->sum('return_product_value');
    }

    public function paid($invoice_id=null)
    {
        $totalPaid = 0;

        $sales=$this->sales();

        if($invoice_id){
            $sales=$sales->where('id','<',$invoice_id);
        }

        return $sales->sum('paid');
    }

    public function due()//sale_due
    {
        return $this->receivable() - $this->paid();
    }

	public function total_due()
    {
        // $this->due()+$this-
        // Invoice Due + direct_receivable
        $direct_receivable = 0;
        if ($this->wallet_balance() < 0) {
            $direct_receivable = abs($this->wallet_balance());
        }

        $invoice_due = $this->due();

        return $invoice_due + $direct_receivable;
    }



    public function update_calculated_data()
    {
        // update wallet balance
        // $wallet_balance = $this->wallet_balance();
        $wallet_balance=$this->wallet_balance();
        // Update Customer Receivable
        // 1. Consider wallet balance + total due
        $sale_due = $this->sales()->sum('due');

        if ($wallet_balance < 0) {
            $this->update([
                'wallet_balance'=>$wallet_balance,
                'total_receivable'=>$sale_due+abs($wallet_balance),
                'total_payable'=>0
            ]);
        }else{
            $this->update([
                'wallet_balance'=>$wallet_balance,
                'total_receivable'=>$sale_due,
                'total_payable'=>abs($wallet_balance)
            ]);
        }
    }

    // Don't delete if any relation is existing
    protected static function boot()
    {
        parent::boot();

        static::created(function($customer){
            $customer->update_calculated_data();
            TransactionService::add_customer_opening_balance($customer);
        });

        static::deleting(function ($rel) {
            $relationMethods = ['actual_payments', 'sales'];

            foreach ($relationMethods as $relationMethod) {
                if ($rel->$relationMethod()->count() > 0) {
                    return false;
                }
            }
        });

        static::deleted(function($customer){
            foreach($customer->opening_transactions as $transaction){
                $transaction->delete();
            }
        });
    }
}
