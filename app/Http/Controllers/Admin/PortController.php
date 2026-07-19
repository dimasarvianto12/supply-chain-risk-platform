<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Port;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index()
    {
        $ports = Port::orderBy('name')->paginate(10);
        return view('admin.ports.index', compact('ports'));
    }

    public function create()
    {
        return view('admin.ports.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'congestion_level' => 'nullable|in:low,medium,high',
            'delay_days' => 'nullable|integer|min:0',
        ]);

        Port::create($request->all());

        return redirect()->route('admin.ports.index')->with('success', 'Pelabuhan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $port = Port::findOrFail($id);
        return view('admin.ports.form', compact('port'));
    }

    public function update(Request $request, $id)
    {
        $port = Port::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'congestion_level' => 'nullable|in:low,medium,high',
            'delay_days' => 'nullable|integer|min:0',
        ]);

        $port->update($request->all());

        return redirect()->route('admin.ports.index')->with('success', 'Pelabuhan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $port = Port::findOrFail($id);
        $port->delete();
        return redirect()->route('admin.ports.index')->with('success', 'Pelabuhan berhasil dihapus.');
    }
}