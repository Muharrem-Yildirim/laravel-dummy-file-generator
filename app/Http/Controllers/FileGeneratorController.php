<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileGenerateRequest;
use App\Http\Resources\FileGenerator\GenerateResource as FileGeneratorGenerateResource;
use App\Http\Resources\FileGenerator\ShowResource;
use App\Jobs\CreateFileJob;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Imtigger\LaravelJobStatus\JobStatus;

class FileGeneratorController extends Controller
{
    public function store(FileGenerateRequest $request)
    {
        $job = new CreateFileJob($request->file_size, $request->file_name, $request->session_id ?? session()->getId());
        $jobId = custom_dispatch($job);

        return FileGeneratorGenerateResource::make(['jobId' => $jobId, 'sessionId' => $job->sessionId]);
    }

    public function show($jobId)
    {
        $jobStatus = JobStatus::whereJobId($jobId)->firstOrFail();

        return ShowResource::make($jobStatus);
    }

    public function download($fileName)
    {
        $file = Storage::disk('generated_files')->path($fileName);

        if (!Storage::disk('generated_files')->exists($fileName)) {
            return response()->json([
                'message' => 'File not found',
            ], 404);
        }

        return response()->download($file, $fileName);
    }
}
