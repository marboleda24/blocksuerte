<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DesignRequirementHeader extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'seller_id', 'customer_code', 'brand_id', 'parameter', 'product_type_code', 'line_code', 'subline_code',
        'feature_code', 'render', 'material_id', 'measurement_id', 'base', 'details', 'created_by', 'assigned_designer_id',
        'state', 'consecutive'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'seller_id', 'customer_code', 'brand_id', 'product_type_code', 'line_code', 'subline_code', 'feature_code',
        'material_id', 'measurement_id', 'created_by', 'assigned_designer_id',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['product', 'measure', 'count_proposals'];

    /**
     * @return HasOne
     */
    public function customer(): HasOne
    {
        return $this->hasOne(CustomerMax::class, 'CODIGO_CLIENTE', 'customer_code');
    }

    /**
     * @return HasOne
     */
    public function seller(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }

    /**
     * @return HasOne
     */
    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

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
    public function created_user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * @return HasOne
     */
    public function assigned_designer(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'assigned_designer_id');
    }

    /**
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(DesignRequirementLog::class, 'design_requirement_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(DesignRequirementProposal::class, 'design_requirement_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(DesignRequirementFile::class, 'design_requirement_id', 'id');
    }

    /**
     * @return string
     */
    public function getProductAttribute(): string
    {
        return "{$this->product_type->code} {$this->line->abbreviation} {$this->subline->abbreviation} {$this->feature->abbreviation} {$this->material->material->abbreviation} ".denominationCreator($this->measurement->detail);
    }

    /**
     * @return string
     */
    public function getMeasureAttribute(): string
    {
        return denominationCreator($this->measurement->detail);
    }

    /**
     * @return mixed
     */
    public function getCountProposalsAttribute(): mixed
    {
        return $this->proposals->count();
    }
}
