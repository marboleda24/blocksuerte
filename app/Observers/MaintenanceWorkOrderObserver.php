<?php

namespace App\Observers;

use App\Models\MaintenanceWorkOrder;

class MaintenanceWorkOrderObserver
{
    /**
     * Handle the MaintenanceRequest "created" event.
     *
     * @param  MaintenanceWorkOrder  $maintenanceWorkOrder
     * @return void
     */
    public function creating(MaintenanceWorkOrder $maintenanceWorkOrder): void
    {
        $maintenanceWorkOrder->consecutive = getLastConsecutive(MaintenanceWorkOrder::class);
    }
}
