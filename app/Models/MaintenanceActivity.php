<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaintenanceActivity extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['work_order_id', 'work_type_id', 'assigned_to', 'cost', 'state', 'description', 'created_by', 'conclusion', 'finish_date'];

    /**
     * @var string[]
     */
    protected $hidden = ['work_order_id', 'work_type_id', 'assigned_to', 'created_by'];

    /**
     * @return HasOne
     */
    public function work_order(): HasOne
    {
        return $this->hasOne(MaintenanceWorkOrder::class, 'id', 'work_order_id');
    }

    /**
     * @return HasOne
     */
    public function work_type(): HasOne
    {
        return $this->hasOne(MaintenanceWorkType::class, 'id', 'work_type_id');
    }

    /**
     * @return HasOne
     */
    public function assignedto(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'assigned_to');
    }

    /**
     * @return HasOne
     */
    public function createdby(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
