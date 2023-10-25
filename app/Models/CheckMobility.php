<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckMobility extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'date', 'city', 'driver', 'boss', 'mileage', 'plate', 'documents', 'inspection', 'road_kit'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'documents' => 'object',
        'inspection' => 'object',
        'road_kit' => 'object',
    ];
}
