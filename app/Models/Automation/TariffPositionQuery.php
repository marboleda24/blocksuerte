<?php

namespace App\Models\Automation;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TariffPositionQuery extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['query', 'description', 'created_id'];

    /**
     * @return HasOne
     */
    public function createdby(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_id');
    }
}
