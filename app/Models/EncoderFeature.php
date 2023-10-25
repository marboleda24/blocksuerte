<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EncoderFeature extends Model
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
        'line_code', 'subline_code', 'code', 'name', 'abbreviation', 'comments',  'state', 'created_by',
    ];

    /**
     * @var string[]
     */
    protected $hidden = ['line_code', 'subline_code', 'created_by'];

    /**
     * @return HasOne
     */
    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
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
}
