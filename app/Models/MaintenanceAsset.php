<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaintenanceAsset extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'code', 'name', 'classification_id', 'state', 'work_center_id', 'priority', 'last_revision',
        'comments', 'data_sheet', 'functionality', 'created_by', 'maintenance_frequency'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'last_revision' => 'datetime'
    ];

    /**
     * @return HasOne
     */
    public function classification(): HasOne
    {
        return $this->hasOne(MaintenanceAssetClassification::class, 'id', 'classification_id');
    }

    /**
     * @return HasOne
     */
    public function work_center(): HasOne
    {
        return $this->hasOne(MaintenanceWorkCenter::class, 'id', 'work_center_id');
    }

    /**
     * @return HasOne
     */
    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * @return HasMany
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'asset_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(MaintenanceAssetFile::class, 'asset_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function resume(): HasOne
    {
        return $this->hasOne(MaintenanceAssetResume::class, 'asset_id', 'id');
    }
}
