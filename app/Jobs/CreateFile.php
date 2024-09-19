<?php

namespace App\Jobs;

use App\Enums\FileGenerationStatus;
use App\Services\FileDataGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Imtigger\LaravelJobStatus\Trackable;
use Throwable;

class CreateFile implements ShouldQueue
{
    use Queueable, Trackable;

    /**
     * Create a new job instance.
     */
    public function __construct(private $fileName = '', private $fileSize = 0)
    {
        $this->prepareStatus();

        if (empty($this->fileName)) {
            $this->fileName = format_bytes($this->fileSize) . '.txt';
        }
    }

    /**
     * Execute the job.
     */
    public function handle(FileDataGenerator $fileDataGenerator)
    {
        if (Storage::disk('generated_files')->exists($this->fileName)) {
            $this->setOutput([FileGenerationStatus::EXISTS, FileGenerationStatus::SUCCESS]);
            return 0;
        }

        Storage::disk('generated_files')->put(
            $this->fileName,
            $fileDataGenerator->createFileContentWithSize($this->fileSize)
        );

        $this->setOutput([FileGenerationStatus::SUCCESS]);

        return 0;
    }

    public function failed(Throwable $e): void
    {
        $this->setOutput([FileGenerationStatus::FAIL]);

        throw $e;
    }
}
