<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class DesignRequirementProposal extends Model
{
    use HasFactory, Compoships;

    protected $fillable = [
        'design_requirement_id', 'product_type_code', 'line_code', 'subline_code', 'feature_code', 'material_id',
        'measurement_id', 'path2D', 'path3D', 'blueprint_id', 'features_detail', 'details', 'state', 'created_by',
        'approved_by', 'weight', 'consecutive', 'area'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'design_requirement_id', 'product_type_code', 'line_code', 'subline_code', 'feature_code', 'material_id',
        'measurement_id', 'path3D', 'blueprint_id', 'created_by', 'approved_by',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'url2D', 'url3D', 'version', 'product', 'measure',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'art'
    ];

    /**
     * @return HasOne
     */
    public function header(): HasOne
    {
        return $this->hasOne(DesignRequirementHeader::class, 'id', 'design_requirement_id');
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
    public function approved_user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'approved_by');
    }

    /**
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(DesignRequirementProposalLog::class, 'proposal_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function versions(): HasMany
    {
        return $this->hasMany(DesignRequirementProposalVersion::class, 'proposal_id', 'id');
    }

    /**
     * @return \Awobaz\Compoships\Database\Eloquent\Relations\HasOne
     */
    public function art(): \Awobaz\Compoships\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DesignRequirementArt::class, ['design_requirement_id', 'proposal_id'], ['design_requirement_id', 'id']);
    }

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
        if ($this->path2D) {
            $file = Storage::get($this->path2D);
            $mime = Storage::mimeType($this->path2D);

            return "data:$mime;base64,".base64_encode($file);
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getUrl3DAttribute(): string
    {
        if ($this->path3D) {
            $file = Storage::disk('qnap')->get($this->path3D);
            $mime = Storage::disk('qnap')->mimeType($this->path3D);

            return "data:$mime;base64,".base64_encode($file);
        } else {
            return '';
        }
    }

    /**
     * @return HasOne|int
     */
    public function getVersionAttribute(): HasOne|int
    {
        return $this->versions->count() === 0
            ? 1
            : $this->hasOne(DesignRequirementProposalVersion::class, 'proposal_id', 'id')->latest('version')->first()->version + 1;
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
}
