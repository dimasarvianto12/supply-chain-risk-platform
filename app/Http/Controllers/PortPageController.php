<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

class PortPageController extends Controller
{
    public function index()
    {
        // Ambil semua negara untuk dropdown
        $countries = Country::orderBy('name')->get();
        return view('ports.index', compact('countries'));
    }
}