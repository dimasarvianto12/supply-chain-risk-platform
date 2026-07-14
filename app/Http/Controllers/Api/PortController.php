<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortResource;
use App\Models\Port;
use Illuminate\Http\Request;

class PortController extends Controller
{
    /**
     * GET /api/ports
     * Daftar pelabuhan dengan filter (country, name, status)
     */
    public function index(Request $request)
    {
        $query = Port::query();

        // Filter berdasarkan negara
        if ($request->has('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        // Filter berdasarkan nama pelabuhan
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $ports = $query->get();

        return PortResource::collection($ports);
    }

    /**
     * GET /api/ports/{id}
     * Detail satu pelabuhan
     */
    public function show($id)
    {
        $port = Port::find($id);

        if (!$port) {
            return response()->json(['message' => 'Port not found'], 404);
        }

        return new PortResource($port);
    }
}