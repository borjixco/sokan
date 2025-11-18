<?php

namespace App\Console\Commands;

use App\Jobs\SendSmsForChargeUnitJob;
use App\Models\Charge;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class sendReminderChargeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '17shahrivar:send-reminder-charge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'یادآوری شارژ واحدها';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $charges = Charge::where('created_at','<', now()->subDays(2))->where('status','UNPAID')->where('reminder',0)->limit(300)->get();
        foreach ($charges as $charge) {
            try {
                $transaction = $charge->transactions()->where('status', 'PENDING')->where('method','GATEWAY')->latest()->first();
                $path = paymentCrypt('charge',$transaction->id);
                SendSmsForChargeUnitJob::dispatch($charge, $path);
                $charge->update(['reminder' => 1]);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
