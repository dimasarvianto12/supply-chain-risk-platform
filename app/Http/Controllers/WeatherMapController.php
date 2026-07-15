<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeatherMapController extends Controller
{
    public function index()
    {
        return view('weather.map');
    }
}