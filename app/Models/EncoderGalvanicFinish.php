<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EncoderGalvanicFinish extends Model
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
    protected $fillable = ['code', 'name', 'abbreviation', 'comments', 'state', 'created_by'];

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
}
