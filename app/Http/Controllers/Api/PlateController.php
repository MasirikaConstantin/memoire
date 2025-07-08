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
        
        // Méthode 1: Recherche exacte d'abord
        $plate = Plate::with(['violations'])
                ->where('number', $cleanPlateNumber)
                ->first();

        // Méthode 2: Si non trouvé, recherche plus flexible
        if (!$plate) {
            $alternateFormat = $this->convertSimilarChars($cleanPlateNumber);
            $plate = Plate::with(['violations'])
                    ->where('number', $alternateFormat)
                    ->first();
        }

        return response()->json([
            'plate' => $plate,
            'cleaned_input' => $cleanPlateNumber,
            'alternate_search' => $alternateFormat ?? null,
            'match_found' => $plate !== null
        ]);
    }

    private function normalizePlateNumber($text)
    {
        // 1. Tout en majuscules
        $text = strtoupper(trim($text));
        
        // 2. Supprimer tous les caractères non alphanumériques
        $text = preg_replace('/[^A-Z0-9]/', '', $text);
        
        return $text;
    }

    private function convertSimilarChars($plateNumber)
    {
        // Convertit les caractères ambigus
        $conversions = [
            'O' => '0',
            'I' => '1',
            'Z' => '2',
            //'A' => '4',
            'S' => '5',
            'G' => '6',
            'T' => '7',
            'B' => '8'
        ];
        
        return str_replace(
            array_keys($conversions),
            array_values($conversions),
            $plateNumber
        );
    }
}