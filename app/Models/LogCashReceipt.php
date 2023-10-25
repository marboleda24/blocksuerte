<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LogCashReceipt extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['cash_receipt_id', 'description', 'created_by'];

    /**
     * @var string[]
     */
    protected $hidden = ['cash_receipt_id', 'created_by'];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
