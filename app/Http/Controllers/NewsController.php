<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Article; // <-- tambahkan ini
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')->get();
        // Ambil 5 artikel terbaru dari database
        $internalArticles = Article::orderBy('created_at', 'desc')->limit(5)->get();
        return view('news.index', compact('countries', 'internalArticles'));
    }
}