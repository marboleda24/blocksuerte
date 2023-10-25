<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectronicBillingLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['document', 'status', 'status_code', 'error_message', 'send_id'];
}
