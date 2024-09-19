<?php

namespace Tests\Feature;

use App\Enums\FileGenerationStatus;
use App\Events\FileGeneratedEvent;
use App\Events\FileNotGeneratedEvent;
use App\Jobs\CreateFileJob;
use App\Services\FileDataGenerator;
use Exception;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Imtigger\LaravelJobStatus\JobStatus;
use PHPUnit\Framework\Attributes\DataProvider;

class CreateFileJobFeatureTest extends TestCase
{
    private $fileName = 'test.txt';
    private $filePath = '';

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('generated_files');
        Event::fake();


        $this->filePath = Storage::disk('generated_files')->path($this->fileName);
    }

    public function test_can_job_create_file(): void
    {
        $job = (new CreateFileJob(0, $this->fileName))->withFakeQueueInteractions();
        $job->handle(app(FileDataGenerator::class));

        $this->assertFileExists($this->filePath);

        $job->assertNotFailed();
    }

    public function test_job_dispatched_success_event(): void
    {
        $job = (new CreateFileJob(0, $this->fileName, session()->getId()))->withFakeQueueInteractions();
        $job->handle(app(FileDataGenerator::class));

        Event::assertDispatched(FileGeneratedEvent::class);
    }

    public function test_job_dispatched_failed_event(): void
    {
        $job = (new CreateFileJob(1, $this->fileName, session()->getId()))->withFakeQueueInteractions();

        try {
            $job->fail();
            $job->failed(new Exception('File could not generated.'));
        } catch (Exception $e) {
        }

        $job->assertFailed();

        Event::assertDispatched(FileNotGeneratedEvent::class);
    }

    #[DataProvider('createFileDataProvider')]
    public function test_can_job_create_file_with_correct_size(int $size): void
    {
        $job = (new CreateFileJob($size, $this->fileName))->withFakeQueueInteractions();
        $job->handle(app(FileDataGenerator::class));

        $this->assertEquals(File::size($this->filePath) === $size, true, "File size should be $size bytes");
    }

    public function test_should_not_generate_same_file(): void
    {
        $job = (new CreateFileJob(1024, $this->fileName))->withFakeQueueInteractions();
        $job->handle(app(FileDataGenerator::class));

        $jobStatus = JobStatus::whereKey($job->getJobStatusId())->firstOrFail();

        $this->assertContains(FileGenerationStatus::SUCCESS->value, $jobStatus->output);

        $job->handle(app(FileDataGenerator::class));
        $jobStatus->refresh();

        $this->assertContains(FileGenerationStatus::EXISTS->value, $jobStatus->output);
    }

    public static function createFileDataProvider(): array
    {
        return [
            'txt file with 1 mb'  => [1024],
            'txt file with 5 mb' => [5242880],
            'txt file with 512 mb' => [536870912]
        ];
    }
}
