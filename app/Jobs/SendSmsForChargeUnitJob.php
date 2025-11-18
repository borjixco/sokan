<?php

namespace App\Jobs;

use App\Services\Sms\MashhadSmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendSmsForChargeUnitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, \Illuminate\Bus\Queueable, SerializesModels;

    public function __construct(protected $charge, protected $path)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->charge->update(['status' => 'UNPAID']);
            $sms = new MashhadSmsService;
            $sms->sendUnitCharge($this->charge->user->mobile,$this->charge->user->name,$this->path);
        }
        catch (\Exception $e){
            Log::error($e->getMessage());
        }
    }

    public function failed(?Throwable $exception): void
    {
        $this->charge->update(['status' => 'NOT_SENT']);
    }
}
