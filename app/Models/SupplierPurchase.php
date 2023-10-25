<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupplierPurchase extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'application_response', 'document_information', 'customer', 'supplier', 'payment_means', 'payment_terms',
        'allowance_charge', 'legal_monetary_total', 'tax_total', 'items', 'pdf_path', 'xml_path', 'upload_by',
        'received_by', 'accepted_by', 'state', 'work_center', 'classification', 'dian_state', 'entity',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'upload_by',
        'received_by',
        'accepted_by',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'application_response' => 'array',
        'document_information' => 'array',
        'customer' => 'array',
        'supplier' => 'array',
        'payment_means' => 'array',
        'payment_terms' => 'array',
        'allowance_charge' => 'array',
        'legal_monetary_total' => 'array',
        'tax_total' => 'array',
        'items' => 'array',
    ];

    /**
     * @return HasOne
     */
    public function upload_user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'upload_by');
    }

    /**
     * @return HasOne
     */
    public function received_user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'received_by');
    }

    /**
     * @return HasOne
     */
    public function accepted_user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'accepted_by');
    }
}
