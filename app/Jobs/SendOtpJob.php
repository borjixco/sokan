<?php

namespace App\Jobs;

use App\Services\Sms\MashhadSmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, \Illuminate\Bus\Queueable, SerializesModels;

    protected $mobile;
    protected $code;

    public function __construct($mobile, $code)
    {
        $this->mobile = $mobile;
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $sms = new MashhadSmsService;
            $sms->sendOtp($this->mobile,$this->code);
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
