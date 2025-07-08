<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plate extends Model
{
    protected $fillable = [
        'number',
        'proprietaire',
        'type_vehicle',
        'est_volee',
        'image',
    ];
    
    protected $casts = [
        'est_volee' => 'boolean',
    ];
    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }
    public static function boot()
    {
        parent::boot();
        static::created(function ($plate) {
            $plate->normalized_number = strtoupper(str_replace([' ', '-'], '', $plate->number));
            $plate->save();
        });
    }

    // Dans app/Models/Plate.php
    public function scopeByAnyFormat($query, $number)
    {
        $clean = strtoupper(preg_replace('/[^A-Z0-9]/', '', $number));
        $alternate = str_replace(['O','I'], ['0','1'], $clean);
        
        return $query->where('normalized_number', $clean)
                    ->orWhere('normalized_number', $alternate);
    }
}
