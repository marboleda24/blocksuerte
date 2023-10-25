<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDocument extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'employee_document', 'consecutive', 'entity', 'payload', 'year', 'month',
        'start_period', 'end_period', 'status', 'type_operation', 'document_reference',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'payload' => 'object',
    ];
}
