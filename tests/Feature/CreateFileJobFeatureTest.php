<?php

namespace Tests\Feature;

use App\Enums\FileGenerationStatus;
use App\Jobs\CreateFile;
use App\Services\FileDataGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
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

        $this->filePath = Storage::disk('generated_files')->path($this->fileName);
    }

    public function test_can_job_create_file(): void
    {
        $job = (new CreateFile($this->fileName))->withFakeQueueInteractions();
        $job->handle(app(FileDataGenerator::class));

        $this->assertFileExists($this->filePath);

        $job->assertNotFailed();
    }

    #[DataProvider('createFileDataProvider')]
    public function test_can_job_create_file_with_correct_size(int $size): void
    {
        $job = (new CreateFile($this->fileName, $size))->withFakeQueueInteractions();
        $job->handle(app(FileDataGenerator::class));

        $this->assertEquals(File::size($this->filePath) === $size, true, "File size should be $size bytes");
    }

    public function test_should_not_generate_same_file(): void
    {
        $job = (new CreateFile($this->fileName, 1024))->withFakeQueueInteractions();
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
