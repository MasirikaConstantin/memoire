<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ViolationResource;
use App\Models\Plate;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
    $bestMatchConfidence = 0;

    foreach ($ocrTexts as $text) {
        $cleanText = $this->normalizePlateNumber($text);
        
        // 1. Recherche exacte (confidence 1.0)
        $plate = Plate::withCount('violations')
                  ->where('number', $cleanText)
                  ->first();
        
        if ($plate && (1.0 > $bestMatchConfidence)) {
            $bestMatch = $plate;
            $bestMatchConfidence = 1.0;
            continue; // Priorité à la correspondance exacte
        }

        // 2. Recherche avec alternatives (confidence 0.9)
        if (!$plate) {
            $alternates = $this->generateAlternates($cleanText);
            
            foreach ($alternates as $alternate) {
                $plate = Plate::withCount('violations')
                          ->where('number', $alternate)
                          ->first();
                
                if ($plate && (0.9 > $bestMatchConfidence)) {
                    $bestMatch = $plate;
                    $bestMatchConfidence = 0.9;
                    break;
                }
            }
        }
    }

    if (!$bestMatch) {
        return response()->json([
            'success' => true,
            'plate_exists' => false,
            'message' => 'Aucune plaque correspondante trouvée',
            'debug' => [
                'top_candidate' => $ocrTexts[0] ?? null,
                'normalized' => !empty($ocrTexts[0]) ? $this->normalizePlateNumber($ocrTexts[0]) : null,
                'alternates_tried' => !empty($ocrTexts[0]) ? $this->generateAlternates($this->normalizePlateNumber($ocrTexts[0])) : []
            ]
        ]);
    }

    return response()->json([
        'success' => true,
        'plate_exists' => true,
        'plate' => [
            'number' => $bestMatch->number,
            'proprietaire' => $bestMatch->proprietaire,
            'type_vehicle' => $bestMatch->type_vehicle,
            'is_stolen' => $bestMatch->est_volee,
            'violations_count' => $bestMatch->violations_count,
            'image_url' => $bestMatch->image ? Storage::url($bestMatch->image) : null
        ],
        'match_quality' => [
            'confidence' => $bestMatchConfidence,
            'match_type' => $bestMatchConfidence === 1.0 ? 'exact' : 'alternative'
        ]
    ]);
}



public function getPlateViolations($plateNumber)
{
    $cleanPlateNumber = $this->normalizePlateNumber($plateNumber);

    $plate = Plate::with(['violations' => fn($q) => $q->orderByDesc('created_at')])
        ->where('number', $cleanPlateNumber)
        ->first();

    if (!$plate) {
        $alternateFormat = $this->convertSimilarChars($cleanPlateNumber);
        $plate = Plate::with(['violations' => fn($q) => $q->orderByDesc('created_at')])
            ->where('number', $alternateFormat)
            ->first();
    }

    return response()->json([
        'plate' => $plate ? [
            'id' => $plate->id,
            'number' => $plate->number,
            'proprietaire' => $plate->proprietaire,
            'type_vehicle' => $plate->type_vehicle,
            'est_volee' => $plate->est_volee,
            'image' => $plate->image,
            'created_at' => $plate->created_at,
            'updated_at' => $plate->updated_at,
            'violations' => ViolationResource::collection($plate->violations),
        ] : null,
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

    private function generateAlternates($plateNumber)
{
    $alternates = [];
    $similarChars = [
        'O' => '0',
        '0' => 'O',
        '1' => 'I',
        'I' => '1',
        'Z' => '2',
        '2' => 'Z',
        //'4' => 'A',
        'A' => '4',
        '5' => 'S',
        'S' => '5',
        '8' => 'B',
        'B' => '8'
    ];

    // Génère toutes les variantes avec un seul caractère changé
    for ($i = 0; $i < strlen($plateNumber); $i++) {
        $char = $plateNumber[$i];
        if (isset($similarChars[$char])) {
            $variant = substr($plateNumber, 0, $i) 
                      . $similarChars[$char] 
                      . substr($plateNumber, $i + 1);
            $alternates[] = $variant;
        }
    }

    return array_unique($alternates);
}
}