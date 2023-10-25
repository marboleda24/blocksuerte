<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CIEV_V_FE_FacturasTotalizadas_Dian extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * @var string
     */
    protected $table = 'CIEV_V_FE_FacturasTotalizadas_Dian';

    /**
     * @var string
     */
    protected $primaryKey = 'NUMERO';
}
