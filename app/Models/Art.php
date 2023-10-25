<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Art extends Model
{
    use HasFactory;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'EVPIUM';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'CIEV_V_ARTES';

    /**
     * @return HasOne
     */
    public function brand(): HasOne
    {
        return $this->hasOne(\App\Models\Evpium\Brand::class, 'IdMarca', 'Marca');
    }
}
