<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Watchlist;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Menampilkan daftar semua negara favorit dari seluruh user.
     */
    public function index()
    {
        $favorites = Watchlist::with(['user', 'country'])->latest()->paginate(10);
        return view('admin.favorites.index', compact('favorites'));
    }

    /**
     * Menghapus salah satu data favorit.
     */
    public function destroy($id)
    {
        $favorite = Watchlist::findOrFail($id);
        $favorite->delete();

        return back()->with('success', 'Negara favorit pengguna berhasil dihapus dari pemantauan!');
    }
}
