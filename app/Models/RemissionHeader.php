<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RemissionHeader extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'consecutive', 'customer_code', 'notes', 'bruto', 'subtotal', 'taxes', 'discount', 'oc', 'document_support',
        'state', 'currency', 'type_id', 'order_number', 'order_max', 'created_by', 'seller_id', 'type_sale', 'claim_id'
    ];

    /**
     * @var string[]
     */
    protected $hidden = ['created_by', 'seller_id', 'type_id'];

    /**
     * @return HasMany
     */
    public function detail(): HasMany
    {
        return $this->hasMany(RemissionDetail::class, 'remission_header_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function customer(): HasOne
    {
        return $this->hasOne(CustomerMax::class, 'CODIGO_CLIENTE', 'customer_code');
    }

    /**
     * @return HasOne
     */
    public function seller(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }

    /**
     * @return HasOne
     */
    public function createdby(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * @return HasOne
     */
    public function type(): HasOne
    {
        return $this->hasOne(RemissionType::class, 'id', 'type_id');
    }

    /**
     * @return HasMany
     */
    public function log(): HasMany
    {
        return $this->hasMany(RemissionLog::class, 'remission_header_id', 'id');
    }

    public function claim(): hasOne
    {
        return $this->hasOne(ClaimHeader::class, 'id', 'claim_id');
    }
}
