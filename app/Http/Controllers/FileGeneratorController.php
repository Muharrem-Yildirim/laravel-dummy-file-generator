<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileGenerateRequest;
use App\Http\Resources\FileGenerator\GenerateResource as FileGeneratorGenerateResource;
use App\Http\Resources\FileGenerator\ShowResource;
use App\Jobs\CreateFileJob;
use Imtigger\LaravelJobStatus\JobStatus;

class FileGeneratorController extends Controller
{

    public function store(FileGenerateRequest $request)
    {
        $job = new CreateFileJob($request->file_size, $request->file_name, session()->getId());
        $jobId = custom_dispatch($job);

        return FileGeneratorGenerateResource::make(['jobId' => $jobId, 'sessionId' => $job->sessionId]);
    }

    public function show($jobId)
    {
        $jobStatus = JobStatus::whereJobId($jobId)->firstOrFail();

        return ShowResource::make($jobStatus);
    }
}
