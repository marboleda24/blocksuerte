<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DesignRequirementProposalVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id', 'line_code', 'subline_code', 'feature_code', 'material_id', 'measurement_id',
        'path2D', 'path3D', 'blueprint_id', 'features_detail', 'details', 'created_by', 'version', 'weight',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'proposal_id', 'line_code', 'subline_code', 'feature_code', 'material_id', 'measurement_id',
        'path2D', 'path3D', 'blueprint_id', 'features_detail', 'created_by',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'url2D', 'url3D', 'product', 'measure',
    ];

    /**
     * @return HasOne
     */
    public function blueprint(): HasOne
    {
        return $this->hasOne(Blueprint::class, 'id', 'blueprint_id');
    }

    /**
     * @return string
     */
    public function getUrl2DAttribute(): string
    {
        return $this->path2D
            ? storage_path("app/$this->path2D")
            : '';
    }

    /**
     * @return string
     */
    public function getUrl3DAttribute(): string
    {
        return $this->path3D
            ? storage_path("app/$this->path3D")
            : '';
    }

    /**
     * @return string
     */
    public function getProductAttribute(): string
    {
        return "{$this->line->abbreviation} {$this->subline->abbreviation} {$this->feature->abbreviation} {$this->material->material->abbreviation} ".denominationCreator($this->measurement->detail);
    }

    /**
     * @return string
     */
    public function getMeasureAttribute(): string
    {
        return denominationCreator($this->measurement->detail);
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
    public function proposal(): HasOne
    {
        return $this->hasOne(DesignRequirementProposal::class, 'id', 'proposal_id');
    }
}
