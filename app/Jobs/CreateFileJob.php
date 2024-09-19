<?php

namespace App\Jobs;

use App\Enums\FileGenerationStatus;
use App\Events\FileGeneratedEvent;
use App\Events\FileNotGeneratedEvent;
use App\Services\FileDataGenerator;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Imtigger\LaravelJobStatus\Trackable;
use Throwable;
use Illuminate\Support\Str;

class CreateFileJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Queueable, Trackable;

    protected $timeout = 300;

    protected $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(private $fileSize = 0, private $fileName = '', private $sessionId = null)
    {
        $this->prepareStatus();

        if (empty($this->fileName)) {
            $this->fileName = Str::replace(' ', '_', format_bytes($this->fileSize) . '.txt');
        }

        $this->setOutput([FileGenerationStatus::PENDING]);
    }

    /**
     * Execute the job.
     */
    public function handle(FileDataGenerator $fileDataGenerator)
    {
        if (
            Storage::disk('generated_files')->exists($this->fileName) &&
            Storage::disk('generated_files')->size($this->fileName) === $this->fileSize
        ) {
            $this->setOutput([FileGenerationStatus::EXISTS, FileGenerationStatus::SUCCESS]);
            return 0;
        }

        Storage::disk('generated_files')->put(
            $this->fileName,
            $fileDataGenerator->createFileContentWithSize($this->fileSize)
        );

        $this->setOutput([FileGenerationStatus::SUCCESS]);

        if ($this->sessionId != null)
            event(new FileGeneratedEvent($this->sessionId));

        return 0;
    }

    public function failed(Throwable $e): void
    {
        $this->setOutput([FileGenerationStatus::FAIL]);

        if ($this->sessionId != null)
            event(new FileNotGeneratedEvent($this->sessionId));

        throw $e;
    }

    public function uniqueId(): string
    {
        return $this->fileName;
    }
}
