<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DeleteOldFilesJob implements ShouldQueue
{
    use Queueable;

    protected $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $files = Storage::disk('generated_files')->files('/');

        foreach ($files as $file) {
            $lastModified = Storage::disk('generated_files')->lastModified($file);
            $threeDaysAgo = Carbon::now()->subDays(3)->timestamp;

            if ($lastModified < $threeDaysAgo) {
                Storage::disk('generated_files')->delete($file);
            }
        }

        return 0;
    }
}
