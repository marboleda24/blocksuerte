<?php

namespace App\Models\Dian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DebitNote extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'DMS';

    /**
     * @var string
     */
    protected $table = 'V_CIEV_FENotasDebito';

    /**
     * @var string
     */
    protected $primaryKey = 'NUMERO';

    /**
     * @return HasOne
     */
    public function apiDocument(): HasOne
    {
        return $this->hasOne(ApiDocument::class, 'number', 'NUMERO')
            ->where('state_document_id', '=', 1)
            ->where('type_document_id', '=', 5);
    }
}
