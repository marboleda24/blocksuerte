<?php

namespace App\Models;

use App\Models\Views\CIEV_V_FE_FacturasDetalladas;
use Awobaz\Compoships\Compoships;
use Awobaz\Compoships\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimItem extends Model
{
    use HasFactory, Compoships;

    /**
     * @var string[]
     */
    protected $fillable = [
        'header_id', 'item', 'product_code', 'new_product_code', 'new_price', 'new_quantity', 'reposition_quantity',
        'delivered_quantity', 'reprocessing_quantity', 'notes', 'credit_note_quantity',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['invoice'];

    /**
     * @var string[]
     */
    protected $hidden = [
        'header_id',
    ];

    public function getInvoiceAttribute()
    {
        return $this->header->document;
    }

    /**
     * @return HasOne
     */
    public function header(): HasOne
    {
        return $this->hasOne(ClaimHeader::class, 'id', 'header_id');
    }

    /**
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this->hasOne(CIEV_V_FE_FacturasDetalladas::class, ['CodigoProducto', 'Item', 'Factura'], ['product_code', 'item', 'invoice']);
    }

    /**
     * @return HasOne
     */
    public function new_product(): HasOne
    {
        return $this->hasOne(ProductMax::class, 'Pieza', 'new_product_code');
    }
}
