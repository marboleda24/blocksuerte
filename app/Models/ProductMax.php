<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductMax extends Model
{
    use HasFactory;

    /**
     * The database primary key
     *
     * @var string
     */
    public $primaryKey = 'Pieza';

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'Pieza', 'Descripcion', 'Cant', 'TRK_LOTE',
    ];

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
    protected $table = 'CIEV_V_ProductosVentas';

    /**
     * The database primary key type
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @return HasOne
     */
    public function price(): HasOne
    {
        return $this->hasOne(PriceItemOrder::class, 'product', 'Pieza');
    }

    /**
     * @return HasOne
     */
    public function customer_product(): HasOne
    {
        return $this->hasOne(PriceItemOrder::class, 'product', 'Pieza');
    }
}
