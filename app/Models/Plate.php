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
}
