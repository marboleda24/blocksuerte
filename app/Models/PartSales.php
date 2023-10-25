<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartSales extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $primaryKey = 'PRTNUM_29';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * The database primary key type
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * @var string
     */
    protected $table = 'Part_Sales';
}
