<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CIE10 extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'cie10';

    /**
     * @var string[]
     */
    protected $fillable = [
        'code', 'symbol', 'description', 'group', 'segment'
    ];
}
