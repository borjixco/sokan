<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Charge;
use App\Models\Transaction;
use App\Services\BillService;
use App\Services\ChargeService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class PaymentController extends Controller
{
    public function payment($crypt,Request $request)
    {
        $decrypt = base62Decode($crypt);
        $exp = explode('-', $decrypt);
        if(isset($exp[1])) {

            [$type, $randString, $id] = $exp;
            $transaction = Transaction::find($id);
            if ($transaction && $transaction->status == 'PENDING') {

                if (
                    in_array($transaction->payable_type, [Bill::class, Charge::class])
                    && (
                        $transaction->payable->due_date === null
                        || strtotime(now()->toDateTimeString()) < Carbon::parse($transaction->payable->due_date)->timestamp
                    )
                ) {
                    $invoice = (new Invoice)->amount($transaction->amount);
                    $uuid = $invoice->getUuid();

                    return Payment::via('sepehr')->purchase($invoice, function ($driver, $transactionId) use ($transaction,$uuid) {
                        $transaction->update([
                            'uuid'           => $uuid,
                            'transaction_id' => generateTransactionId(),
                            'gateway'        => 'SADERAT',
                        ]);
                    })->pay()->render();
                } else {
                    try {
                        DB::transaction(function () use ($transaction) {
                            $transaction->payable->update(['status' => 'OVERDUE']);
                            $transaction->update(['status' => 'EXPIRED']);
                        });
                        $status = 'danger';
                        $message = 'این تراکنش منقضی شده است.';
                    } catch (\Exception $e) {
                        $status = 'danger';
                        $message = $e->getMessage();
                    }
                }
            } else {
                $status = 'danger';
                $message = 'تراکنش نامعتبر (۱۰۰۱)';
            }
        }
        else{
            $status = 'danger';
            $message = 'تراکنش نامعتبر (۱۰۰۲)';
        }
        return inertia('Web/Payment/Index', compact('status', 'message'));

    }

    public function callback(Request $request)
    {
        $transactionService = new TransactionService;
        $transaction = $request->invoiceid ? Transaction::where('uuid', $request->invoiceid)->first() : null;
        //dump($request->all(),$transaction);
        $refId = '';
        $amount = '';
        $transactionId = '';
        $date = '';
        if(!$transaction){
            $message = 'تراکنش پیدا نشد (در صورت کسر مبلغ از حساب شما حداکثر پس از 72 ساعت مبلغ به حسابتان برمیگردد)';
            $status = 'error';
        }
        else {
            $date = verta()->instance($transaction->created_at)->format('d F Y');
            if ($transaction && $transaction->status == 'PENDING') {
                try {
                    $receipt = Payment::via('sepehr')->amount($transaction->amount)->verify();

                    $refId = $receipt->getReferenceId(); // or $request->rrn
                    $cardNumber = $request->cardnumber;
                    $traceNumber = $request->tracenumber;
                    $paidAt = $request->datepaid ? Verta::parseFormat('Ymd H:i:s',$request->datepaid)->toCarbon()->format('Y-m-d H:i:s') : null;
                    $transactionService->updatePayment($transaction, [
                        'status'          => 'SUCCESSFUL',
                        'referenceId'     => $refId,
                        'decreaseBalance' => $transaction->amount,
                        'cardNumber'      => $cardNumber,
                        'paidAt'          => $paidAt,
                        'traceNumber'     => $traceNumber,
                    ]);
                    $amount = $transaction->amount;
                    $status = 'success';
                    $message = 'تراکنش با موفقیت انجام شد';
                    $transactionId = $transaction->transaction_id;
                } catch (InvalidPaymentException $exception) {
                    $transactionService->updatePayment($transaction, ['status' => 'CANCELED']);
                    $status = 'error';
                    $message = $exception->getMessage();
                    $transactionId = $transaction->transaction_id;
                    if($transaction->payable_type == Charge::class){
                        (new ChargeService)->createNewPayment($transaction->payable);
                    }
                    elseif($transaction->payable_type == Bill::class){
                        (new BillService)->createNewPayment($transaction->payable);
                    }
                }

            } elseif ($transaction->status == 'SUCCESSFUL') {
                $status = 'info';
                $refId = $transaction->ref_id;
                $message = 'این تراکنش قبلا انجام شده است';
                $transactionId = $transaction->transaction_id;
            } elseif ($transaction->status == 'CANCELED') {
                $status = 'error';
                $message = 'این تراکنش توسط شما لغو شده است';
                $transactionId = $transaction->transaction_id;
            }
        }
        return inertia('Web/Payment/Callback', compact('message','transactionId', 'refId','status', 'amount', 'date'));
    }

    public function callback21(Request $request)
    {
        // این متد بعدا حذف شود
        //$receipt = Payment::via('sepehr')->amount(10000)->verify();
        dd($request->all());
        try {


            // دریافت اطلاعات از رسید و ریکوئست
            $transactionId = $receipt->getTransactionId(); // transaction_id سمت شما
            $refId = $receipt->getReferenceId();           // digitalreceipt
            $amount = $receipt->getAmount();               // مبلغ
            $details = $receipt->getDetails();             // آرایه شامل همه اطلاعات اضافی

            // مثال اطلاعات موجود در $details:
            // [
            //     "digitalreceipt" => "...",
            //     "rrn" => "...", شماره مرجع تراکنش
            //     "tracenumber" => "...",
            //     "datepaid" => "...",
            //     "cardnumber" => "6037********1234",
            //     ...
            // ]

            // ثبت در دیتابیس
            $transaction = Transaction::where('transaction_id', $transactionId)->first();
            if ($transaction) {
                $transaction->update([
                    'reference_id' => $refId,
                    'status' => 'SUCCESSFUL',
                    'paid_at' => Carbon::parse($details['datepaid']),
                    'rrn' => $details['rrn'] ?? null,
                    'trace_number' => $details['tracenumber'] ?? null,
                    'card_number' => $details['cardnumber'] ?? null,
                ]);
            }

            return response()->json([
                'message' => 'پرداخت با موفقیت انجام شد',
                'ref_id' => $refId,
                'amount' => $amount,
            ]);
        } catch (InvalidPaymentException $e) {
            // در صورت خطا
            $transactionId = request()->input('invoiceid'); // یا هر چیزی که به عنوان transaction_id فرستادی

            $transaction = Transaction::where('transaction_id', $transactionId)->first();
            if ($transaction) {
                $transaction->update([
                    'status' => 'FAILED',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'message' => 'پرداخت ناموفق بود',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
