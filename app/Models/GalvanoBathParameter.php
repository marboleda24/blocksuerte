<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GalvanoBathParameter extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'production_order', 'product_code', 'bath', 'ph', 'density', 'temperature', 'entry_time', 'exit_time', 'user_id', 'notes',
    ];

    /**
     * @var string[]
     */
    protected $hidden = ['user_id'];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this->hasOne(ProductMax::class, 'Pieza', 'product_code');
    }
}
