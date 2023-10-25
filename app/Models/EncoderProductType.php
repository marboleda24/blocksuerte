<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EncoderProductType extends Model
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
        'code', 'name', 'comments', 'state', 'created_by',
    ];

    /**
     * @var string[]
     */
    protected $hidden = ['created_by'];

    /**
     * @return HasOne
     */
    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function lines(): BelongsToMany
    {
        return $this->belongsToMany(EncoderLine::class, 'encoder_pvt_product_types_lines', 'product_type_code', 'line_code')
            ->withPivot('product_type_code');
    }
}
