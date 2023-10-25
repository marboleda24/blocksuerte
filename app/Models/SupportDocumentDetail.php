<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupportDocumentDetail extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'support_document_header_id', 'product_id', 'price', 'quantity', 'retention', 'measurement', 'type',
        'type_transmition_id', 'transmition_date',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'support_document_header_id', 'product_code',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['total'];

    /**
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this->hasOne(SupportDocumentProduct::class, 'id', 'product_id');
    }

    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
