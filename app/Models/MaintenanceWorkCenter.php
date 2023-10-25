<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaintenanceWorkCenter extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'ct', 'comments', 'created_by'];

    /**
     * @return HasOne
     */
    public function created_by(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
