<?php

namespace App\Models\Dian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * @var string
     */
    protected $table = 'CIEV_V_FE_FacturasTotalizadas';

    /**
     * @var string
     */
    protected $primaryKey = 'NUMERO';


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
        }else if ($this->TIPODOC === 'CU' && $this->MONEDA === 'COP'){
            $type = 1;
        }else {
            $type = 2;
        }

        return ApiDocument::where('type_document_id', '=', $type)
            ->where('number', '=', $this->NUMERO)
            ->where('identification_number', '=', '890926617')
            ->where('state_document_id', '=', 1)
            ->first();
    }
}
