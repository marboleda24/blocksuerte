<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartiesDMS extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'DMS';

    /**
     * @var string
     */
    protected $table = 'V_CIEV_Terceros';
}
