<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Port;
use App\Models\Article;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalPorts = Port::count();
        $totalArticles = Article::count();
        
        $recentUsers = User::latest()->take(5)->get();
        $recentArticles = Article::latest()->take(5)->get();
        $congestedPorts = Port::orderBy('delay_days', 'desc')->take(5)->get();
        
        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalPorts', 
            'totalArticles',
            'recentUsers',
            'recentArticles',
            'congestedPorts'
        ));
    }
}