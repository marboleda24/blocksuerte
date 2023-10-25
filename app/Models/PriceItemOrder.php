<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PriceItemOrder extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prices_items_orders';

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'customer_code', 'product', 'customer_product_code', 'price', 'approved_by', 'notes', 'state',
    ];

    /**
     * product_info
     *
     * @return HasOne
     */
    public function product_info(): HasOne
    {
        return $this->hasOne(ProductMax::class, 'Pieza', 'product');
    }

    /**
     * customer
     *
     * @return HasOne
     */
    public function customer(): HasOne
    {
        return $this->hasOne(CustomerMax::class, 'CODIGO_CLIENTE', 'customer_code');
    }

    /**
     * approved
     *
     * @return HasOne
     */
    public function approved(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'approved_by');
    }
}
