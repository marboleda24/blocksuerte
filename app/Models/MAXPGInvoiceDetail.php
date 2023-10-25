<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MAXPGInvoiceDetail extends Model
{
    use HasFactory;
    /**
     * @var string
     */
    protected $connection = 'MAXPG';

    /**
     * @var string
     */
    protected $table = 'PG_V_FE_FacturasDetalladas';

    /**
     * @var bool
     */
    public $incrementing = true;

    /**
     * @var string
     */
    protected $primaryKey = 'Factura';


}
