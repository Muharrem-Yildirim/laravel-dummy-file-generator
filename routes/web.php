<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home', [
        'sessionId' => session()->getId(),
    ]);
});

Route::post('/broadcasting/custom-auth', function () {
    return response([], 200);
});
