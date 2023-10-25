<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Awobaz\Compoships\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntradaOC extends Model
{
    use HasFactory;
    use Compoships;

    /**
     * @var string
     */
    protected $primaryKey = 'OC';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * The database primary key type
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * @var string
     */
    protected $table = 'CIEV_V_EntradasOC';

    /**
     * @return HasOne
     */
    public function registro(): HasOne
    {
        return $this->hasOne(RawMaterial::class, ['entry', 'entry_id'], ['ENTRADA', 'IDEntrada']);
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(RawMaterial::class, 'oc', 'OC');
    }
}
