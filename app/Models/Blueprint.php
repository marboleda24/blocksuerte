<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Blueprint extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['line_code', 'subline_code', 'feature_code', 'material_id', 'measurement_id', 'description', 'created_by'];

    /**
     * @var string[]
     */
    protected $hidden = ['created_by'];

    /**
     * @var string[]
     */
    protected $appends = ['file_path', 'measure', 'product', 'version', 'miniature'];

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
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(BlueprintFile::class, 'blueprint_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function created_user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * @return string
     */
    public function getFilePathAttribute(): string
    {
        return $this->files->count() > 0
            ? $this->files->where('state', '=', 1)->first()->path
            : '';
    }

    /**
     * @return string
     */
    public function getVersionAttribute(): string
    {
        return $this->files->count() > 0
            ? $this->files->where('state', '=', 1)->first()->version
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
     * @return string
     */
    public function getMiniatureAttribute(): string
    {
        return $this->files->count() > 0
            ? $this->files->where('state', '=', 1)->first()->miniature
            : '';
    }
}
