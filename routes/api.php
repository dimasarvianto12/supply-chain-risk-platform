<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\RiskController;
use App\Http\Controllers\Api\PortController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\VisualizationController as ApiVisualizationController;
use App\Http\Controllers\Api\CompareController as ApiCompareController;



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

Route::get('/visualization/gdp/{country}', [ApiVisualizationController::class, 'gdp']);
Route::get('/visualization/inflation/{country}', [ApiVisualizationController::class, 'inflation']);
Route::get('/visualization/currency/{country}', [ApiVisualizationController::class, 'currency']);
Route::get('/visualization/risk/{country}', [ApiVisualizationController::class, 'risk']);

Route::get('/compare/{countryA}/{countryB}', [ApiCompareController::class, 'compare']);
