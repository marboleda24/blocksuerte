<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaintenanceWorkOrder extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'consecutive', 'request_id', 'description', 'state', 'type', 'cost',
        'created_by', 'updated_by', 'closing_date', 'assigned_to',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'request_id', 'updated_by', 'created_by', 'assigned_to',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s A',
        'closing_date' => 'datetime:Y-m-d h:i:s A',
    ];

    /**
     * @return HasOne
     */
    public function request(): HasOne
    {
        return $this->hasOne(MaintenanceRequest::class, 'id', 'request_id');
    }

    /**
     * @return HasOne
     */
    public function updatedby(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    /**
     * @return HasOne
     */
    public function createdby(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * @return HasOne
     */
    public function assignedto(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'assigned_to');
    }

    /**
     * @return HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(MaintenanceActivity::class, 'work_order_id', 'id');
    }

    /**
     * @return void
     */
    public function cancel_activities(): void
    {
        $this->activities()->update([
            'state' => '0',
            'finish_date' => Carbon::now(),
            'conclusion' => 'actividad anulada, Justificación: Orden de trabajo anulada',
        ]);
    }
}
