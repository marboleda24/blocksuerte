<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Advance extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'customer_code', 'total_paid', 'payment_date', 'bank_account', 'consecutive',
        'details', 'dms_cash_receipt', 'state', 'created_by', 'approved_by', 'cancel_justify',
    ];

    /**
     * createdby
     *
     * @return HasOne
     */
    public function createdby()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * createdby
     *
     * @return HasOne
     */
    public function approvedby(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'approved_by');
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
     * @return HasMany
     */
    public function log(): HasMany
    {
        return $this->hasMany(AdvanceLog::class, 'advance_id', 'id');
    }
}
