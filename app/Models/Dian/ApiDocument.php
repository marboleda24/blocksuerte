<?php

namespace App\Models\Dian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiDocument extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'API_DIAN';

    /**
     * @var string
     */
    protected $table = 'documents';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'client' => 'object',
        'taxes' => 'object',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identification_number', 'state_document_id', 'type_document_id', 'prefix', 'number', 'xml', 'cufe',
        'acknowledgment_received', 'type_invoice_id', 'client_id', 'client', 'currency_id', 'date_issue',
        'date_expiration', 'observation', 'reference_id', 'note_concept_id', 'sale', 'total_discount', 'taxes',
        'total_tax', 'subtotal', 'total', 'version_ubl_id', 'ambient_id', 'response_api', 'payment_form_id',
        'payment_method_id', 'time_days_credit', 'response_api_status', 'correlative_api', 'request_api', 'pdf', 'customer'
    ];
}
