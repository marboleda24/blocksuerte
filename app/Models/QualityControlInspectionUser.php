<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityControlInspectionUser extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['name'];
}
