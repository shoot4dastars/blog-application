<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/lifecycle-test', fn () =>
    response()->json([
    'php_version' => phpversion(),
    'timestamp' => now()->toDateTimeString(),
    ])
);
