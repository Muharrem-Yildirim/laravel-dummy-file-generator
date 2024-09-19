<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileGenerateRequest;
use App\Http\Resources\FileGenerator\GenerateResource as FileGeneratorGenerateResource;
use App\Http\Resources\FileGenerator\ShowResource;
use App\Http\Resources\GenerateResource;
use App\Jobs\CreateFile;
use Imtigger\LaravelJobStatus\JobStatus;

class FileGeneratorController extends Controller
{

    public function store(FileGenerateRequest $request)
    {
        $job = new CreateFile($request->file_size, $request->file_name);
        $jobId = custom_dispatch($job);

        return FileGeneratorGenerateResource::make(['jobId' => $jobId]);
    }

    public function show($jobId)
    {
        $jobStatus = JobStatus::whereJobId($jobId)->firstOrFail();

        return ShowResource::make($jobStatus);
    }
}
