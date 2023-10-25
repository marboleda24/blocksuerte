<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EncoderProduct extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['old_product', 'new_product', 'user_id'];

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
}
