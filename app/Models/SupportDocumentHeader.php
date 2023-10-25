<?php

namespace App\Models;

use App\Models\Dian\ApiDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupportDocumentHeader extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'consecutive', 'seller_document', 'notes', 'transaction_date',
        'created_id', 'payment_form', 'state', 'entity',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'seller_document', 'created_id',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'user', 'provider_name', 'state_dian', 'bruto', 'retention',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s A',
    ];

    /**
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(SupportDocumentDetail::class, 'support_document_header_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(SupportDocumentLog::class, 'support_document_header_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function created_by(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_id');
    }

    /**
     * @return HasOne
     */
    public function provider(): HasOne
    {
        if ($this->entity === 'CIEV') {
            return $this->hasOne(ThirdPartiesDMS::class, 'nit', 'seller_document');
        } else {
            return $this->hasOne(ThirdPartiesDMSGoja::class, 'nit', 'seller_document');
        }
    }

    /**
     * @return mixed
     */
    public function getUserAttribute(): mixed
    {
        return $this->created_by->name;
    }

    /**
     * @return mixed
     */
    public function getProviderNameAttribute(): mixed
    {
        return $this->provider->nombres;
    }

    /**
     * @return float
     */
    public function getBrutoAttribute(): float
    {
        return $this->details->reduce(function ($a, $c) {
            return $a + ($c->price * $c->quantity);
        });
    }

    /**
     * @return float
     */
    public function getRetentionAttribute(): float
    {
        return $this->details->reduce(function ($a, $c) {
            return $a + $c->retention;
        });
    }

    /**
     * @return string
     */
    public function getStateDianAttribute(): string
    {
        return $this->hasOne(ApiDocument::class, 'number', 'consecutive')
            ->where('prefix', '=', 'DSEV')
            ->where('type_document_id', '=', 11)
            ->where('state_document_id', '=', 1)->first()
            ? 'Autorizado' : 'Pendiente';
    }
}
