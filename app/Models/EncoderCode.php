<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EncoderCode extends Model
{
    use HasFactory;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * primaryKey
     *
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * keyType
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var string[]
     */
    protected $fillable = [
        'code', 'description', 'comments', 'generic', 'state', 'created_by', 'product_type_code', 'line_code', 'subline_code', 'feature_code',
        'material_id', 'measurement_id', 'galvanic_finish_code', 'decorative_option_code', 'art_code',
    ];

    /**
     * @return HasOne
     */
    public function product_type(): HasOne
    {
        return $this->hasOne(EncoderProductType::class, 'code', 'product_type_code');
    }

    /**
     * @return HasOne
     */
    public function line(): HasOne
    {
        return $this->hasOne(EncoderLine::class, 'code', 'line_code');
    }

    /**
     * @return HasOne
     */
    public function subline(): HasOne
    {
        return $this->hasOne(EncoderSubline::class, 'code', 'subline_code');
    }

    /**
     * @return HasOne
     */
    public function feature(): HasOne
    {
        return $this->hasOne(EncoderFeature::class, 'code', 'feature_code');
    }

    /**
     * @return HasOne
     */
    public function material(): HasOne
    {
        return $this->hasOne(EncoderMaterial::class, 'id', 'material_id');
    }

    /**
     * @return HasOne
     */
    public function measurement(): HasOne
    {
        return $this->hasOne(EncoderMeasurement::class, 'id', 'measurement_id');
    }

    /**
     * @return HasOne
     */
    public function galvanic_finish(): HasOne
    {
        return $this->hasOne(EncoderGalvanicFinish::class, 'code', 'galvanic_finish_code');
    }

    /**
     * @return HasOne
     */
    public function decorative_option(): HasOne
    {
        return $this->hasOne(EncoderDecorativeOption::class, 'code', 'decorative_option_code');
    }

    /**
     * @return HasOne
     */
    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
