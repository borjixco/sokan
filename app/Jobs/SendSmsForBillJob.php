<?php

namespace App\Jobs;

use App\Services\Sms\MashhadSmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendSmsForBillJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, \Illuminate\Bus\Queueable, SerializesModels;

    public function __construct(protected $bill, protected $path)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->bill->update(['status' => 'UNPAID']);
            $sms = new MashhadSmsService;
            $sms->sendBill($this->bill->user->mobile,$this->bill->user->name,$this->path);
        }
        catch (\Exception $e){
            Log::error($e->getMessage());
        }
    }

    public function failed(?Throwable $exception): void
    {
        $this->bill->update(['status' => 'NOT_SENT']);
    }
}
