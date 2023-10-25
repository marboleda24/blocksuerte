<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PackingOrderExportationList extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'consecutive',
        'user_id',
        'data',
        'box_list',
        'state'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'data' => 'array',
        'box_list' => 'array',
        'created_at' => 'date:Y-m-d h:m:s A',
        'updated_at' => 'date:Y-m-d h:m:s A'
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'state_name'
    ];

    /**
     * @return HasOne
     */
    public function userCreated(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getStateNameAttribute(): string
    {
        return match ($this->state){
            'pending' => 'Pendiente',
            'cancel' => 'Cancelado',
            'close' => 'Cerrado'
        };
    }
}
