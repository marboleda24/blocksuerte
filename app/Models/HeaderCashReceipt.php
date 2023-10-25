<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HeaderCashReceipt extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'customer_code', 'total_paid', 'comments', 'payment_date', 'payment_account', 'consecutive', 'observations',
        'dms_cash_receipt', 'state', 'created_by', 'approved_by', 'payment_method', 'type'
    ];

    /**
     * details
     *
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(DetailCashReceipt::class, 'cash_receipt_id', 'id');
    }

    /**
     * log
     *
     * @return HasMany
     */
    public function log(): HasMany
    {
        return $this->hasMany(LogCashReceipt::class, 'cash_receipt_id', 'id');
    }

    /**
     * createdby
     *
     * @return HasOne
     */
    public function createdby(): HasOne
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
}
