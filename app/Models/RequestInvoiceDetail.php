<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RequestInvoiceDetail extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['item', 'product_code', 'request_invoice_id', 'quantity', 'notes'];

    /**
     * @var string[]
     */
    protected $hidden = ['request_invoice_id'];

    /**
     * @return HasOne
     */
    public function detail(): HasOne
    {
        return $this->hasOne(ProductMax::class, 'Pieza', 'product_code');
    }
}
