<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RemissionLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['remission_header_id', 'description', 'user_id'];

    /**
     * @var string[]
     */
    protected $hidden = ['remission_header_id', 'user_id'];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}