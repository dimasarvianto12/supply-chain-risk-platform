<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')->get();
        return view('news.index', compact('countries'));
    }
}