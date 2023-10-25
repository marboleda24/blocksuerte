<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DesignRequirementProduct extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'father_id', 'code', 'description', 'product_type_code', 'line_code', 'subline_code', 'feature_code', 'material_id',
        'measurement_id', 'galvanic_finish_code', 'decorative_option_code', 'art_code', 'type', 'state', 'brand_id'
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'order_notes'
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
    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    /**
     * @return string|null
     */
    public function getOrderNotesAttribute(): string|null
    {
        $item = DetailOrder::where('product', '=', $this->code)->first();

        if ($item)
            return $item->toArray()['notes'] === null ? 'N/A' : $item->toArray()['notes'];

        return 'N/A';
    }
}
