<?php

use App\Http\Controllers\FileGeneratorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('file')->group(function () {
    Route::resource('generator', FileGeneratorController::class)->only(['store', 'show']);
    Route::get('/download/{fileName}', [FileGeneratorController::class, 'download'])->name('file.download');
})
    ->name('file.');
