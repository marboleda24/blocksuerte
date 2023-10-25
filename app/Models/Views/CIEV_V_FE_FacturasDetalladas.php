<?php

namespace App\Models\Views;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CIEV_V_FE_FacturasDetalladas extends Model
{
    use HasFactory, Compoships;

    /**
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * @var string
     */
    protected $table = 'CIEV_V_FE_FacturasDetalladas';
}
