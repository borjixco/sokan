<?php

namespace App\Services;

use App\Jobs\SendSmsForBillJob;
use App\Models\Bill;

class BillService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createNewPayment($bill): void
    {
        $transactionResult = (new TransactionService)->onlinePayment($bill->user->id,Bill::class,$bill->id,$bill->amount,'GATEWAY', 'افزایش موجودی از طریق درگاه');
        SendSmsForBillJob::dispatch($bill,$transactionResult['path']);

    }
}
