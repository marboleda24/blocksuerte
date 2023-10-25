<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AdvanceLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['advance_id', 'user_id', 'description'];

    /**
     * @var string[]
     */
    protected $hidden = ['advance_id', 'user_id'];

    /**
     * @var string[]
     */
    protected $with = [
        'user',
    ];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
