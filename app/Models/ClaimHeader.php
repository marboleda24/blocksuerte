<?php

namespace App\Models;

use App\Models\Views\CIEV_V_FE_FacturasTotalizadas_Dian;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use phpseclib3\Crypt\Hash;
use Vinkla\Hashids\Facades\Hashids;

class ClaimHeader extends Model
{
    use HasFactory, Compoships;

    /**
     * @var string[]
     */
    protected $fillable = [
        'consecutive', 'destiny', 'action', 'reason', 'document', 'notes', 'created_id', 'workplace_id', 'state',
        'production_order', 'quality_observation', 'cellar_observation', 'credit_memo', 'sale_order', 'remission_id',
        'discount', 'major_value', 'new_customer_code', 'accounted', 'credit_note',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'created_id', 'workplace_id', 'id',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'state_esp', 'destiny_esp', 'action_esp', 'reason_esp', 'hash', 'remissions_list'
    ];

    /**
     * @return BelongsToMany
     */
    public function causes(): BelongsToMany
    {
        return $this->belongsToMany(ClaimCause::class, 'claim_pvt_causes_header', 'header_id', 'cause_id')
            ->withPivot('header_id');
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {

        return $this->hasMany(ClaimItem::class, 'header_id', 'id')
            ->orderBy('Item', 'asc');
    }

    /**
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ClaimLog::class, 'header_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_id');
    }

    /**
     * @return HasOne
     */
    public function workplace(): HasOne
    {
        return $this->hasOne(ClaimWorkplace::class, 'id', 'workplace_id');
    }

    /**
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(ClaimFile::class, 'header_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(CIEV_V_FE_FacturasTotalizadas_Dian::class, 'NUMERO', 'document');
    }

    /**
     * @return HasOne
     */
    public function new_customer(): HasOne
    {
        return $this->hasOne(CustomerMax::class, 'CODIGO_CLIENTE', 'new_customer_code');
    }

    /**
     * @return string|void
     */
    public function getDestinyEspAttribute()
    {
        switch ($this->destiny) {
            case 'cellar':
                return 'Bodega';
            case 'quality':
                return 'Calidad';
            case 'cost':
                return 'Costos';
        }
    }

    /**
     * @return string|void
     */
    public function getActionEspAttribute()
    {
        switch ($this->action) {
            case 'credit-note':
                return 'Nota Crédito';
            case 'change-reposition':
                return 'Cambios - Reposiciones';
            case 'manufacturing':
                return 'Fabricación';
            case 'reprocess':
                return 'Reproceso';
            case 'return':
                return 'Devolución';
        }
    }

    /**
     * @return string|void
     */
    public function getReasonEspAttribute()
    {
        switch ($this->reason) {
            case 'change':
                return 'Cambios';
            case 'reposition':
                return 'Reposición';
            case 'quantity':
                return 'Cantidad';
            case 'quantity-new-invoice':
                return 'Cantidad - Repetir factura';
            case 'date':
                return 'Fecha';
            case 'price':
                return 'Precio';
            case 'discount':
                return 'Descuento';
            case 'NA':
                return 'N/A';
            case 'major-value':
                return 'Mayor Valor Cobrado';
            case 'customer-change':
                return 'Cambio razón social';
            case 'quality-r1':
                return 'Pedido mal programado desde ventas';
            case 'quality-r2':
                return 'Pedido mal programado de parte del cliente.';
            case 'quality-r3':
                return 'Pedido mal despachado desde el área de bodega.';
            case 'quality-r4':
                return 'Factura mal generada desde el área de sistemas.';
            case 'quality-r5':
                return 'Mal revisado por calidad.';
            case 'quality-r6':
                return 'Pedido mal programado por producción.';
            case 'product-change':
                return 'Cambio de producto';

        }
    }

    /**
     * @return string|void
     */
    public function getStateEspAttribute()
    {
        switch ($this->state) {
            case 'erase':
                return 'Borrador';
            case 'refuse':
                return 'Rechazado';
            case 'quality':
                return 'Calidad';
            case 'cellar':
                return 'Bodega';
            case 'cost':
                return 'Costos';
            case 'wallet':
                return 'Cartera';
            case 'finish':
                return 'Finalizado';
            case 'canceled':
                return 'Anulado';
        }
    }

    /**
     * @return string
     */
    public function getHashAttribute(): string
    {
        return Hashids::encode($this->id);
    }

    /**
     * @return \Awobaz\Compoships\Database\Eloquent\Relations\HasMany
     */
    public function remissions(): HasMany
    {
        return $this->hasMany(RemissionHeader::class, 'claim_id' ,'id' );
    }

    /**
     * @return array
     */
    public function getRemissionsListAttribute(): array
    {
        return $this->remissions()->pluck('consecutive')->toArray();
    }

}
