<?php

namespace App\Models\Goja;

use App\Models\Dian\ApiDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'MAXPG';

    /**
     * @var string
     */
    protected $table = 'PG_V_FE_FacturasTotalizadas';

    /**
     * @var string
     */
    protected $primaryKey = 'NUMERO';

    /**
     * @var string[]
     */
    protected $fillable = [
        'NUMERO', 'OC', 'FECHA', 'VENCIMIENTO', 'MOTIVO', 'DESCMOTIVO', 'CODIVA', 'BRUTO', 'DESCUENTO', 'RTEFTE',
        'RTEIVA', 'RTEICA', 'SUBTOTAL', 'IVA', 'FLETES', 'SEGUROS', 'MONEDA', 'TASA', 'TIPOCLIENTE', 'OV', 'CLIENTE',
        'IDENTIFICACION', 'RAZONSOCIAL', 'TIPODOC', 'PLAZO', 'DIAS', 'DESCPLAZO', 'CODVENDEDOR', 'NOMVENDEDOR',
        'COMENTARIOS', 'CORREOSCOPIA', 'BRUTO_USD', 'DESC_USD', 'IVA_USD', 'FLETES_USD', 'SEGUROS_USD', 'CORREOFE',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['api_document'];

    /**
     * @return mixed
     */
    public function getApiDocumentAttribute(): mixed
    {
        if ($this->MOTIVO === '39'){
            $type = 1;
        }else if ($this->TIPODOC === 'CR'){
            $type = 4;
        }else if ($this->TIPODOC === 'CU'){
            $type = 1;
        }else {
            $type = 2;
        }

        return ApiDocument::where('type_document_id', '=', $type)
            ->where('number', '=', $this->NUMERO)
            ->where('identification_number', '=', '900349726')
            ->where('state_document_id', '=', 1)
            ->first();
    }
}
