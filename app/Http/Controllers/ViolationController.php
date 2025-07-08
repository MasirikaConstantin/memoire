<?php

namespace App\Http\Controllers;

use App\Models\Plate;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $query = Violation::with('plate')
            ->orderBy('occurred_at', 'desc');
            
        // Filtres
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('plate_number')) {
            $query->whereHas('plate', function($q) use ($request) {
                $q->where('number', 'like', '%'.$request->plate_number.'%');
            });
        }
        
        $violations = $query->paginate(20);
        
        return response()->json($violations);
    }

    public function show($id)
    {
        $violation = Violation::with('plate')->findOrFail($id);
        
        return response()->json([
            'data' => $violation,
            'evidence_url' => $violation->evidence_photo_path 
                ? Storage::url($violation->evidence_photo_path)
                : null
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:10',
            'violation_type' => 'required|in:red_light,speeding,parking',
            'occurred_at' => 'sometimes|date',
            'location' => 'nullable|string|max:255',
            'evidence_photo' => 'sometimes|file|image|max:2048'
        ]);
        
        // Trouver ou créer la plaque
        $plate = Plate::firstOrCreate(['number' => $validated['plate_number']]);
        
        // Créer la violation
        $violation = $plate->violations()->create([
            'type' => $validated['violation_type'],
            'occurred_at' => $validated['occurred_at'] ?? now(),
            'location' => $validated['location'] ?? null,
            'status' => 'pending'
        ]);
        
        // Gérer l'upload de la preuve photo
        if ($request->hasFile('evidence_photo')) {
            $path = $request->file('evidence_photo')->store(
                "violations/{$violation->id}", 
                'public'
            );
            $violation->update(['evidence_photo_path' => $path]);
        }
        
        return response()->json($violation, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $violation = Violation::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processed,dismissed'
        ]);
        
        $violation->update(['status' => $validated['status']]);
        
        return response()->json($violation);
    }
}