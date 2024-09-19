<?php

namespace Tests\Feature\Unit;

use App\Jobs\DeleteOldFilesJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteOldFilesJobTest extends TestCase
{
    private $testFilePath;

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        Storage::fake();
        Storage::disk('generated_files')->put('test.txt', 'test');
        $this->testFilePath = Storage::disk('generated_files')->path('test.txt');
    }

    public function test_job_is_dispatched()
    {
        dispatch(new DeleteOldFilesJob(0, 'test.txt'));

        Queue::assertPushed(DeleteOldFilesJob::class);
    }

    public function test_deletes_old_files(): void
    {
        touch($this->testFilePath, Carbon::now()->subDays(4)->timestamp);

        $this->assertFileExists($this->testFilePath);

        $job = (new DeleteOldFilesJob())->withFakeQueueInteractions();
        $job->handle();

        $job->assertNotFailed();

        $this->assertFileDoesNotExist($this->testFilePath);
    }

    public function test_not_deletes_files_that_are_not_old(): void
    {
        $this->testFilePath = Storage::disk('generated_files')->path('test.txt');

        $this->assertFileExists($this->testFilePath);

        $job = (new DeleteOldFilesJob())->withFakeQueueInteractions();
        $job->handle();

        $job->assertNotFailed();

        $this->assertFileExists($this->testFilePath);
    }
}
