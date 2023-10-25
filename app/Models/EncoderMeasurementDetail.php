<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EncoderMeasurementDetail extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'measurement_id', 'unit_code', 'characteristic_code', 'value'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'measurement_id', 'unit_code', 'characteristic_code'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'value' => 'float'
    ];

    /**
     * @return HasOne
     */
    public function unit(): HasOne
    {
        return $this->hasOne(EncoderUnitMeasurement::class, 'code', 'unit_code');
    }

    /**
     * @return HasOne
     */
    public function characteristic(): HasOne
    {
        return $this->hasOne(EncoderMeasurementCharacteristic::class, 'code', 'characteristic_code');
    }
}
