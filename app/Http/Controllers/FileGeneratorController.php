<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileGenerateRequest;
use App\Jobs\CreateFile;

class FileGeneratorController extends Controller
{

    public function generate(FileGenerateRequest $request)
    {
        $job = new CreateFile($request->file_size, $request->file_name);
        $id = dispatch($job);
        return response()->json(['success' => true, 'job' => $id->getJobId()], 200);
    }

    public function show($jobId)
    {
        return response()->download(storage_path('app/public/' . $fileName), $fileName);
    }
}
