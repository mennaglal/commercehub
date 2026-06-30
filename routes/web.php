<?php

use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/change-language/{locale}', [LanguageController::class,'change'])
    ->name('change-language');
