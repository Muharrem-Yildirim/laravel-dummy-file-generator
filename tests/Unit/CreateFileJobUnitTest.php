<?php

namespace Tests\Unit;

use App\Jobs\CreateFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateFileJobUnitTest extends TestCase
{
    public function test_job_is_dispatched()
    {
        Queue::fake();
        Storage::fake('generated_files');

        dispatch(new CreateFile('test.txt'));

        Queue::assertPushed(CreateFile::class);
    }
}
