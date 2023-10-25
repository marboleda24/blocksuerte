<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBalanceMAX extends Model
{
    use HasFactory;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'CIEV_V_SaldoOVXCliente';

    /**
     * The database primary key
     *
     * @var string
     */
    public $primaryKey = 'CODIGO_CLIENTE';

    /**
     * The database primary key type
     *
     * @var string
     */
    protected $keyType = 'string';
}
