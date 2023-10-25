<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineBinnacle extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'reference', 'brand', 'btu_tc', 'kcal_tc', 'type',
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'machines_binnacle';
}
