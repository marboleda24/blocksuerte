<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RemissionDetail extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'remission_header_id', 'product', 'quantity', 'price', 'unit_measurement',
        'art', 'art2', 'brand', 'notes', 'customer_product_code',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'remission_header_id',
    ];

    /**
     * @return HasOne
     */
    public function info(): HasOne
    {
        return $this->hasOne(ProductMaxEvpiu::class, 'code', 'product');
    }
}
