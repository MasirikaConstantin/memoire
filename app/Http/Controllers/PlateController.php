<?php

namespace App\Http\Controllers;

use App\Models\Plate;
use Illuminate\Http\Request;

class PlateController extends Controller
{
    public function index(Request $request)
    {
        $query = Plate::query();
        
        // Filtrage
        if ($request->has('is_stolen')) {
            $query->where('is_stolen', $request->boolean('is_stolen'));
        }
        
        // Pagination
        $plates = $query->paginate(20);
        
        return response()->json($plates);
    }

    public function show($plateNumber)
    {
        $plate = Plate::with(['violations' => function($query) {
            $query->orderBy('occurred_at', 'desc');
        }])->where('number', $plateNumber)->firstOrFail();
        
        return response()->json([
            'data' => $plate,
            'meta' => [
                'violation_count' => $plate->violations->count(),
                'pending_violations' => $plate->violations->where('status', 'pending')->count()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:10|unique:plates',
            'owner_name' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|string|max:50',
            'is_stolen' => 'sometimes|boolean'
        ]);
        
        $plate = Plate::create($validated);
        
        return response()->json($plate, 201);
    }

    public function update(Request $request, $plateNumber)
    {
        $plate = Plate::where('number', $plateNumber)->firstOrFail();
        
        $validated = $request->validate([
            'owner_name' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|string|max:50',
            'is_stolen' => 'sometimes|boolean'
        ]);
        
        $plate->update($validated);
        
        return response()->json($plate);
    }
}