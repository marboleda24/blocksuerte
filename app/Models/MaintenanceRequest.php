<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaintenanceRequest extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'consecutive', 'applicant_id', 'asset_id', 'planning_date', 'description', 'type', 'state', 'closing_date'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'applicant_id', 'asset_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'planning_date' => 'datetime:Y-m-d h:i:s A',
        'closing_date' => 'datetime:Y-m-d h:i:s A',
        'created_at' => 'datetime:Y-m-d h:i:s A'
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'type_name', 'state_name'
    ];

    /**
     * @return HasOne
     */
    public function applicant(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'applicant_id');
    }

    /**
     * @return HasOne
     */
    public function asset(): HasOne
    {
        return $this->hasOne(MaintenanceAsset::class, 'id', 'asset_id');
    }

    /**
     * @return HasMany
     */
    public function log(): HasMany
    {
        return $this->hasMany(MaintenanceRequestLog::class, 'request_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function work_orders(): HasMany
    {
        return $this->hasMany(MaintenanceWorkOrder::class, 'request_id', 'id');
    }

    /**
     * @return string
     */
    public function getTypeNameAttribute(): string
    {
        return match ($this->type){
            "preventive" => "Preventivo",
            "corrective" => "Correctivo",
            "locative" => "Locativo",
            default => "Mejorativo"
        };
    }

    /**
     * @return string
     */
    public function getStateNameAttribute(): string
    {
        return match ($this->state){
            "0" => "Anulado",
            "1" => "En revision",
            "2" => "Aprobada",
            "3" => "En proceso",
            "4" => "Finalizada",
            default => "Rechazado"
        };
    }
}
