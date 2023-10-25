<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerMax extends Model
{
    use HasFactory;

    /**
     * The database primary key
     *
     * @var string
     */
    public $primaryKey = 'CODIGO_CLIENTE';

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'CIEV_V_Clientes';

    /**
     * The database primary key type
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * balance_max
     *
     * @return HasOne
     */
    public function balance_max(): HasOne
    {
        return $this->hasOne(CustomerBalanceMAX::class, 'CODIGO_CLIENTE', 'CODIGO_CLIENTE');
    }

    /**
     * balance_dms
     *
     * @return HasOne
     */
    public function balance_dms(): HasOne
    {
        return $this->hasOne(CustomerBalanceDMS::class, 'CodigoAlterno', 'CODIGO_CLIENTE');
    }

    /**
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(CustomerFile::class, 'customer_code', 'CODIGO_CLIENTE');
    }
}
