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
    

    // Dans app/Models/Plate.php
public function getNormalizedNumberAttribute()
{
    return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $this->number));
}

    public function setNormalizedNumberAttribute($value)
    {
        $this->attributes['normalized_number'] = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $value));
    }
}
