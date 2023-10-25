<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMaxEvpiu extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'PRODUCTS_MAX_EVPIU';

    /**
     * @var string[]
     */
    protected $fillable = [
        'code', 'description', 'origin', 'stock', 'art', 'arts', 'type'
    ];
}
