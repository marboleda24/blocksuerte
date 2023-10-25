<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class EncoderLine extends Model
{
    use HasFactory;

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
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'code', 'name', 'abbreviation', 'comments',  'state', 'created_by',
    ];

    /**
     * @var string[]
     */
    protected $hidden = ['created_by'];

    /**
     * @var string[]
     */
    protected $appends = ['types'];

    /**
     * @return HasOne
     */
    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * @return BelongsToMany
     */
    public function product_types(): BelongsToMany
    {
        return $this->belongsToMany(EncoderProductType::class, 'encoder_pvt_product_types_lines', 'line_code', 'product_type_code')
            ->withPivot('line_code');
    }

    /**
     * @return Collection
     */
    public function getTypesAttribute(): Collection
    {
        return $this->product_types()->pluck('code');
    }
}
