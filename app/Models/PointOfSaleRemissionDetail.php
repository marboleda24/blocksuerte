<?php

namespace App\Models;

use Closure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PointOfSaleRemissionDetail extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'header_id', 'product', 'quantity', 'price', 'warehouse',
        'unit_measurement', 'art', 'art2', 'brand', 'notes', 'type',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'description', 'required_lot', 'available_lots',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'order_id', 'header_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'quantity' => 'int',
    ];

    /**
     * @return HasMany
     */
    public function lots(): HasMany
    {
        return $this->hasMany(PointOfSaleRemissionDetailLot::class, 'detail_id', 'id');
    }

    /**
     * @return Closure|mixed|null
     */
    public function getDescriptionAttribute(): mixed
    {
        return $this->hasOne(ProductMax::class, 'Pieza', 'product')
            ->pluck('Descripcion')
            ->first();
    }

    /**
     * @return bool
     */
    public function getRequiredLotAttribute(): bool
    {
        return $this->hasOne(ProductMax::class, 'Pieza', 'product')
                ->pluck('TRK_LOTE')
                ->first() === 'Y';
    }

    /**
     * @return Collection
     */
    public function getAvailableLotsAttribute(): Collection
    {
        return DB::connection('MAX')
            ->table('Part_Lot')
            ->where('PRTNUM_68', '=', $this->product)
            ->get();
    }
}
