<?php

namespace App\Jobs;

use App\Services\FileDataGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class CreateFile implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private $fileName, private $fileSize = 0) {}

    /**
     * Execute the job.
     */
    public function handle(FileDataGenerator $fileDataGenerator)
    {
        Storage::disk('generated_files')->put(
            $this->fileName,
            $fileDataGenerator->createFileContentWithSize($this->fileSize)
        );

        return 0;
    }
}
