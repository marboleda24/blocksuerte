<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['consecutive', 'status', 'statusCode', 'ErrorMessage', 'cune', 'send_by', 'entity'];
}
