<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class HeaderOrder extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'oc', 'customer_code', 'notes', 'bruto', 'subtotal', 'taxes', 'discount', 'currency', 'consecutive',
        'type', 'order_max', 'state', 'substate', 'created_by', 'seller_id', 'destiny', 'taxable', 'mark',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['state_name', 'trm'];

    /**
     * details
     *
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(DetailOrder::class, 'order_id', 'id');
    }

    /**
     * customer
     *
     * @return HasOne
     */
    public function customer(): HasOne
    {
        return $this->hasOne(CustomerMax::class, 'CODIGO_CLIENTE', 'customer_code');
    }

    /**
     * log
     *
     * @return HasMany
     */
    public function log(): HasMany
    {
        return $this->hasMany(LogOrder::class, 'order_id', 'id');
    }

    /**
     * seller
     *
     * @return HasOne
     */
    public function seller(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }

    /**
     * cliente_info
     *
     * @return HasOne
     */
    public function customer_master(): HasOne
    {
        return $this->hasOne(CustomerMaster::class, 'CUSTID_23', 'customer_code');
    }

    /**
     * @return float
     */
    public function getRetentionAttribute(): float
    {
        if ($this->taxes > 0 && $this->customer->TIPO_CLIENTE !== 'PN') {
            $crr = current_retention_rates(Carbon::now()->year);

            $type = array_search('servicio', array_column(array_column($this->details->toarray(), 'product_info'), 'description'))
                ? 'SERVICIOS'
                : 'VENTAS';

            $total = $type === 'SERVICIOS'
                ? ($this->subtotal * 4) / 100
                : ($this->subtotal * 2.5) / 100;

            if ($this->subtotal > $crr->where('Tipo', '=', $type)->first()->Base) {
                return $total;
            } else {
                return 0.00;
            }
        }

        return 0.00;
    }

    /**
     * @return string|void
     */
    public function getStateNameAttribute()
    {
        switch ($this->state) {
            case '0':
                return 'Anulado';
            case '1':
                return 'Borrador';
            case '2':
                return 'Cartera';
            case '3':
                return 'Costos';
            case '4':
                return 'Bodega';
            case '5':
                return 'Producción';
            case '6':
                return 'Troqueles';
            case '7':
                return 'Rechazados';
            case '10':
                return 'Finalizados';
        }
    }

    /**
     * @return int|mixed
     */
    public function getTrmAttribute(): mixed
    {
        if ($this->type === 'export') {
            return DB::connection('DMS')
                ->table('monedas_factores')
                ->where('moneda', '=', 'US')
                ->where('fecha', '=', Carbon::parse($this->created_at)->format('Y-m-d 00:00:00'))
                ->pluck('factor')
                ->first();
        }

        return 0;
    }
}
