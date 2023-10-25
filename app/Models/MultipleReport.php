<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultipleReport extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'code', 'table', 'title',
    ];
}
