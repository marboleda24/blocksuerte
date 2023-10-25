<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaintenanceRequestLog extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['request_id', 'description', 'created_by', 'type'];

    /**
     * @var string[]
     */
    protected $hidden = ['request_id', 'created_by'];

    /**
     * @return HasOne
     */
    public function request(): HasOne
    {
        return $this->hasOne(MaintenanceRequest::class, 'request_id');
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
