<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_id',
        'type',
        'localisation',
        'photo_preuve',
        'traiter'
    ];

    protected $casts = [
        'traiter' => 'boolean',
    ];

    public function plate()
    {
        return $this->belongsTo(Plate::class);
    }

    public static function types()
    {
        return [
            'feu_rouge' => 'Feu rouge',
            'exces_de_vitesse' => 'Excès de vitesse',
            'autre' => 'Autre'
        ];
    }

   
}