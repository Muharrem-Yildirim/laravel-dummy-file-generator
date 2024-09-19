<?php

namespace Tests\Unit;

use App\Jobs\CreateFileJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateFileJobUnitTest extends TestCase
{
    public function test_job_is_dispatched()
    {
        Queue::fake();

        dispatch(new CreateFileJob(0, 'test.txt'));

        Queue::assertPushed(CreateFileJob::class);
    }
}
