<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DetailOrder extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'product', 'quantity', 'price', 'unit_measurement', 'art', 'art2', 'brand',
        'notes', 'customer_product_code', 'type', 'destiny',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'description'
    ];

    /**
     * info product by max database
     *
     * @return HasOne
     */
    public function product_info(): HasOne
    {
        return $this->hasOne(ProductMaxEvpiu::class, 'code', 'product');
    }

    /**
     * @return HasOne
     */
    public function header(): HasOne
    {
        return $this->hasOne(HeaderOrder::class, 'id', 'order_id');
    }

    /**
     * @return mixed
     */
    public function getDescriptionAttribute(): mixed
    {
        return $this->product_info?->description;
    }
}
