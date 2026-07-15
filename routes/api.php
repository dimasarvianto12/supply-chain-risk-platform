<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\RiskController;
use App\Http\Controllers\Api\PortController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\WeatherController;

// Countries
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{code}', [CountryController::class, 'show']);

// Risk
Route::get('/risk', [RiskController::class, 'index']);
Route::get('/risk/{country}', [RiskController::class, 'show']);

// Ports
Route::get('/ports', [PortController::class, 'index']);
Route::get('/ports/{id}', [PortController::class, 'show']);

// News
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{country}', [NewsController::class, 'show']);

// Currency (urutan PENTING)
Route::get('/currency/latest/{base?}', [CurrencyController::class, 'latestRates']);
Route::get('/currency/history/{base}/{target}', [CurrencyController::class, 'history']);
Route::get('/currency/{base}/{target}', [CurrencyController::class, 'show']);

// Weather
Route::get('/weather', [WeatherController::class, 'index']);