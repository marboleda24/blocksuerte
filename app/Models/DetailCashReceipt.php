<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailCashReceipt extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'cash_receipt_id', 'invoice', 'bruto', 'discount', 'retention', 'reteiva', 'reteica',
        'other_deductions', 'other_income', 'total', 'financial_expenses', 'positive_balance'
    ];
}
