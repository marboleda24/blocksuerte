<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class V_CIEV_Personal extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    public $connection = 'DMS';

    /**
     * @var string
     */
    public $primaryKey = 'nit';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    public $table = 'V_CIEV_Personal';
}
