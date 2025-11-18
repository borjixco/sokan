<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Charge;
use App\Models\Parking;
use App\Models\Setting;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function PHPUnit\Framework\throwException;

class TransactionService
{
    public function creditPayment($userId, $payableType, $payableId,$amount,$method){
        try {
            DB::transaction(function () use($userId,$payableType,$payableId,$amount,$method){
                $instance = new $payableType;
                Transaction::create([
                    'user_id'        => $userId,
                    'payable_type'   => $payableType,
                    'payable_id'     => $payableId,
                    'amount'         => abs($amount)*-1,
                    'method'         => $method,
                    'status'         => 'SUCCESSFUL',
                    'transaction_id' => generateTransactionId(),
                    'reference_id'   => now()->getTimestampMs(),
                    'description'    => 'کسر از موجودی بابت '.$instance->title,
                ]);
                $payable = $instance::find($payableId);
                $payable->update([
                   'status' => 'PAID',
                ]);
                $payable->user->decrement('balance',$amount);
            });
            return true;
        }
        catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function onlinePayment($userId,$payableType,$payableId,$amount,$method,$description = '')
    {
        try {
            $items = [
                'user_id'      => $userId,
                'payable_type' => $payableType,
                'payable_id'   => $payableId,
                'amount'       => $amount,
                'method'       => $method,
                'status'       => 'PENDING',
                'description'  => $description,
            ];
            $transaction = Transaction::create($items);

            $path = paymentCrypt('charge',$transaction->id);
            $url = route('payment',$path);
            return [
                'url'  => $url,
                'path' => $path,
            ];
        }
        catch (Exception $e){
            return false;
        }
    }

    public function createPayment($userId,$payableType,$payableId,$amount,$method,$status = 'PENDING',$referenceId = '', $description = '')
    {
        try {
            $items = [
                'user_id'       => $userId,
                'payable_type'  => $payableType,
                'payable_id'    => $payableId,
                'amount'        => $amount,
                'method'        => $method,
                'status'        => $status,
                'reference_id'  => $referenceId,
                'description'   => $description,
            ];
            return Transaction::create($items);
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function updatePayment($transaction,$data)
    {

         $decreaseBalance = $data['decreaseBalance'] ?? false;
         $status          = $data['status'] ?? false;
         $referenceId     = $data['referenceId'] ?? false;
         $cardNumber      = $data['cardNumber'] ?? false;
         $paidAt          = $data['paidAt'] ?? false;
         $traceNumber     = $data['traceNumber'] ?? false;


        try {
            $items = [
                'status'       => $status,
                'paid_at'      => $paidAt,
                'trace_number' => $traceNumber,
                'reference_id' => $referenceId,
                'card_number'  => $cardNumber,
            ];
            $transaction->update($items);

            $payableStatus = '';
            if($transaction->payable_type == Charge::class){
                if($status == 'SUCCESSFUL'){
                    $payableStatus = 'PAID';
                }
                elseif ($status == 'CANCELED'){
                    $payableStatus = 'UNPAID';
                }
                elseif ($status == 'EXPIRED'){
                    $payableStatus = 'UNPAID';
                }
                elseif ($status == 'FAILED'){
                    $payableStatus = 'UNPAID';
                }
            }
            elseif ($transaction->payable_type == Bill::class){
                if($status == 'SUCCESSFUL'){
                    $payableStatus = 'PAID';
                }
                elseif ($status == 'CANCELED'){
                    $payableStatus = 'CANCELED';
                }
                elseif ($status == 'EXPIRED'){
                    $payableStatus = 'OVERDUE';
                }
                elseif ($status == 'FAILED'){
                    $payableStatus = 'UNPAID';
                }
            }
            elseif ($transaction->payable_type == Parking::class){

                if($status == 'SUCCESSFUL'){
                    $payableStatus = 'PAID';
                }
                elseif ($status == 'CANCELED'){
                    $payableStatus = 'CANCELED';
                }
                elseif ($status == 'EXPIRED'){
                    $payableStatus = 'UNPAID';
                }
                elseif ($status == 'FAILED'){
                    $payableStatus = 'UNPAID';
                }
            }
            $transaction->payable->update([
                'payment_method' => 'GATEWAY',
                'status'         => $payableStatus
            ]);

            if($decreaseBalance !== false){
                $transaction->user->increment('balance',$decreaseBalance);
                $this->creditPayment($transaction->user->id,$transaction->payable_type,$transaction->payable_id,$decreaseBalance,'WALLET');
            }
        }
        catch (Exception $e){
            return false;
        }
    }
}
