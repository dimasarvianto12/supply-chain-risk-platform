<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

class VisualizationController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')->get();
        return view('visualization.index', compact('countries'));
    }
}