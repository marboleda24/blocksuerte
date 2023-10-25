<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimFile extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'header_id', 'name', 'path', 'type',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'header_id',
    ];
}
