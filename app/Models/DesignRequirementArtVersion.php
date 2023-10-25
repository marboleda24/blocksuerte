<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class DesignRequirementArtVersion extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'art_id', 'line_code', 'subline_code', 'feature_code', 'material_id', 'measurement_id',
        'path2D', 'path3D', 'blueprint_id', 'brand_id', 'weight', 'designer_id', 'seller_id', 'comments',
        'features_detail', 'version', 'state', 'enabled',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'art_id', 'line_code', 'subline_code', 'feature_code', 'material_id', 'measurement_id',
        'path2D', 'path3D', 'blueprint_id', 'brand_id', 'designer_id', 'seller_id',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'product', 'measure', 'url2D', 'url3D',
    ];

    /**
     * @return HasOne
     */
    public function art(): HasOne
    {
        return $this->hasOne(DesignRequirementArt::class, 'id', 'art_id');
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
    public function blueprint(): HasOne
    {
        return $this->hasOne(Blueprint::class, 'id', 'blueprint_id');
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
    public function designer(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'designer_id');
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
     * @return string
     */
    public function getUrl2DAttribute(): string
    {
        if ($this->path2D) {
            $file = Storage::disk('qnap')->get($this->path2D);
            $mime = Storage::disk('qnap')->mimeType($this->path2D);

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
     * @return string
     */
    public function getRevisionAttribute(): string
    {
        return "R{$this->art->design_requirement->id}P{$this->art->proposal->id}V{$this->version}";
    }
}
