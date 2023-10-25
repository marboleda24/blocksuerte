<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartiesDMSGoja extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'GOJA';

    /**
     * @var string
     */
    protected $table = 'V_PG_Terceros';
}
