<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBalanceDMS extends Model
{
    use HasFactory;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'DMS';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'V_CIEV_SaldoXCliente';

    /**
     * The database primary key
     *
     * @var string
     */
    public $primaryKey = 'CodigoAlterno';
}
