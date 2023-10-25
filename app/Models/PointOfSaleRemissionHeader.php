<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PointOfSaleRemissionHeader extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'consecutive', 'order_id', 'location', 'created_id', 'state',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'state_name',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'order_id', 'created_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s A',
    ];

    /**
     * @return HasOne
     */
    public function order(): HasOne
    {
        return $this->hasOne(HeaderOrder::class, 'id', 'order_id');
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_id');
    }

    /**
     * @return HasMany
     */
    public function detail(): HasMany
    {
        return $this->hasMany(PointOfSaleRemissionDetail::class, 'header_id', 'id');
    }

    /**
     * @return string
     */
    public function getStateNameAttribute(): string
    {
        return match ($this->state) {
            'pending' => 'Pendiente',
            'transit' => 'En transito',
            'finish' => 'Finalizado'
        };
    }
}
