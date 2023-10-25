<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestInvoice extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'evpiu';

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'id_worksplace', 'invoice', 'id_reason', 'comments', 'state', 'process_status',
        'new_invoice', 'notes', 'file', 'file_approved', 'justify', 'justify_send_store', 'justify_refuse_store',
        'reopen_quality_comments', 'reopen_store_comments', 'observations', 'observations_quality', 'created_by',
    ];

    /**
     * @var string[]
     */
    protected $hidden = ['created_by'];

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(RequestInvoiceDetail::class, 'request_invoice_id', 'id');
    }

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function worksplace(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Workplace::class, 'id', 'id_worksplace');
    }

    public function reason_reprocessings(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ReasonReprocessing::class, 'id', 'id_reason');
    }
}
