<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MAXInvoiceDetail extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * @var string
     */
    protected $table = 'CIEV_V_FE_FacturasDetalladas';

    /**
     * @var bool
     */
    public $incrementing = true;

    /**
     * @var string
     */
    protected $primaryKey = 'Factura';
}
