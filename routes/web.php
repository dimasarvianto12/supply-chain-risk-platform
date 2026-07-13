<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

// Route sementara untuk semua menu (akan diganti dengan controller di tahap selanjutnya)
Route::get('/countries', function () {
    return view('home');
})->name('countries.index');

Route::get('/weather-map', function () {
    return view('home');
})->name('weather.map');

Route::get('/currency', function () {
    return view('home');
})->name('currency.dashboard');

Route::get('/news', function () {
    return view('home');
})->name('news.index');

Route::get('/ports', function () {
    return view('home');
})->name('ports.index');

Route::get('/visualization', function () {
    return view('home');
})->name('visualization.index');

Route::get('/compare', function () {
    return view('home');
})->name('compare.index');

Route::get('/favorites', function () {
    return view('home');
})->name('favorites.index');

// Route untuk auth (sementara belum dipakai, bisa dikomentari dulu)
// Route::get('/login', function () { return view('home'); })->name('login');
// Route::get('/logout', function () { return redirect('/'); })->name('logout');