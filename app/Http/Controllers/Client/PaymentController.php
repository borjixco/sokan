<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Charge;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function charge(Charge $charge)
    {
        $transaction = $charge->transactions()->where('status','PENDING')->where('method','GATEWAY')->first();
        if($transaction){
            $url = route('payment',paymentCrypt('charge',$transaction->id));
            return redirectMessage('success','درحال اتصال به درگاه', ['url' => $url]);
        }
        else{
            return redirectMessage('error','خطایی پیش آمد لطفا با پشتیبانی تماس بگیرید');
        }
    }

    public function bill(Bill $bill)
    {
        $transaction = $bill->transactions()->where('status','PENDING')->where('method','GATEWAY')->first();
        if($transaction){
            $url = route('payment',paymentCrypt('bill',$transaction->id));
            return redirectMessage('success','درحال اتصال به درگاه', ['url' => $url]);
        }
        else{
            return redirectMessage('error','خطایی پیش آمد لطفا با پشتیبانی تماس بگیرید');
        }
    }
}
