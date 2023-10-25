<?php

namespace App\Models\Evpium;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'EVPIUM';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'Marcas';
}
