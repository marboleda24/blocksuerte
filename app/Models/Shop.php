<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Shop extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['code', 'name', 'created_id'];

    /**
     * @var string[]
     */
    protected $hidden = ['created_id'];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_id');
    }
}
