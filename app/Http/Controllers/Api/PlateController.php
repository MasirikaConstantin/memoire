<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plate;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlateController extends Controller
{
    public function checkPlate(Request $request)
    {
        // Valider les données OCR reçues
        $validator = Validator::make($request->all(), [
            'ocr_results' => 'required|array',
            'ocr_results.*' => 'string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $ocrTexts = $request->input('ocr_results');
        $bestMatch = null;
        $highestConfidence = 0;

        // Trouver la meilleure correspondance parmi les résultats OCR
        foreach ($ocrTexts as $text) {
            $cleanText = $this->normalizePlateNumber($text);
            
            $plate = Plate::where('number', $cleanText)->first();
            
            if ($plate) {
                // Ici vous pourriez utiliser la confiance de l'OCR si disponible
                $confidence = 1.0; // Valeur factice, à remplacer par la vraie confiance
                
                if ($confidence > $highestConfidence) {
                    $bestMatch = $plate;
                    $highestConfidence = $confidence;
                }
            }
        }

        if (!$bestMatch) {
            return response()->json([
                'success' => true,
                'plate_exists' => false,
                'message' => 'Aucune plaque correspondante trouvée'
            ]);
        }

        return response()->json([
            'success' => true,
            'plate_exists' => true,
            'plate' => [
                'number' => $bestMatch->number,
                'proprietaire' => $bestMatch->proprietaire,
                'is_stolen' => $bestMatch->is_stolen,
                'violations_count' => $bestMatch->violations->count()
            ],
            'confidence' => $highestConfidence
        ]);
    }

    public function getPlateViolations($plateNumber)
    {
        $cleanPlateNumber = $this->normalizePlateNumber($plateNumber);
        
        $plate = Plate::with(['violations' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->where('number', $cleanPlateNumber)->first();

        if (!$plate) {
            return response()->json([
                'success' => false,
                'message' => 'Plaque non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'plate' => [
                'number' => $plate->number,
                'proprietaire' => $plate->proprietaire,
                'is_stolen' => $plate->is_stolen
            ],
            'violations' => $plate->violations->map(function($violation) {
                return [
                    'id' => $violation->id,
                    'type' => $violation->type,
                    'localisation' => $violation->localisation,
                    'date' => $violation->created_at->format('d/m/Y H:i'),
                    'traiter' => $violation->traiter,
                    'photo_preuve' => $violation->photo_preuve ? url('storage/'.$violation->photo_preuve) : null
                ];
            })
        ]);
    }

    private function normalizePlateNumber($text)
    {
        // Nettoyage du texte pour correspondre au format des plaques
        $text = strtoupper(trim($text));
        $text = preg_replace('/[^A-Z0-9]/', '', $text);
        
        // Ici vous pouvez ajouter d'autres règles de normalisation
        // selon le format de vos plaques d'immatriculation
        
        return $text;
    }
}