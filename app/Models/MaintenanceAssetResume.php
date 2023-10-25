<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaintenanceAssetResume extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'asset_id', 'model', 'brand', 'power', 'amperage', 'voltage', 'frequency', 'watts', 'rpm', 'created_id',
        'updated_id', 'maintenance_frequency', 'dimension', 'preventive_maintenance_description', 'precautions'
    ];

    /**
     * @return HasOne
     */
    public function created_user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_id');
    }

    /**
     * @return HasOne
     */
    public function updated_user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_id');
    }
}
