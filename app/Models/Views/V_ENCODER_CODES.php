<?php

namespace App\Models\Views;

use App\Models\EncoderMeasurement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class V_ENCODER_CODES extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'V_ENCODER_CODES';

    /**
     * @var string[]
     */
    protected $fillable = [
        'code', 'description', 'product_type', 'line', 'subline', 'feature', 'material', 'galvanic_finish',
        'decorative_option', 'measurement_id', 'comments'
    ];

    /**
     * @return HasOne
     */
    public function measurement(): HasOne
    {
        return $this->hasOne(EncoderMeasurement::class, 'id', 'measurement_id');
    }
}
