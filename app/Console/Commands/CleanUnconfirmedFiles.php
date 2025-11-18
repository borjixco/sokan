<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanUnconfirmedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '17shahrivar:clean-unconfirmed-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'حذف رسانه های بلا استفاده';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = Media::where('confirmed', false)
            ->where('created_at', '<', now()->subDay()) // فایل‌های قدیمی‌تر از یک روز
            ->get();

        foreach ($files as $file) {
            Storage::delete('public/' . $file->file_path);
            $file->delete();
        }

        $this->info("فایل‌های بی‌صاحب حذف شدند!");
    }
}
