<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

class CompareController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')->get();
        return view('compare.index', compact('countries'));
    }
}