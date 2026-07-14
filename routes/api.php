<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\RiskController;
use App\Http\Controllers\Api\PortController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\CurrencyController;

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{code}', [CountryController::class, 'show']);

Route::get('/risk', [RiskController::class, 'index']);
Route::get('/risk/{country}', [RiskController::class, 'show']);

Route::get('/ports', [PortController::class, 'index']);
Route::get('/ports/{id}', [PortController::class, 'show']);

Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{country}', [NewsController::class, 'show']);

Route::get('/currency/{base}/{target}', [CurrencyController::class, 'show']);
Route::get('/currency/history/{base}/{target}', [CurrencyController::class, 'history']);