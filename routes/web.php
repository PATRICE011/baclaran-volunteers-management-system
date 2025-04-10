<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', [AuthController::class,  'getLogin']);

Route::get('/information-sheet', function () {
    return view('information_sheet');
});