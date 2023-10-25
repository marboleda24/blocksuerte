<?php

namespace App\Observers;

use App\Models\MaintenanceRequest;

class MaintenanceRequestObserver
{
    /**
     * Handle the MaintenanceRequest "created" event.
     *
     * @param  MaintenanceRequest  $maintenanceRequest
     * @return void
     */
    public function creating(MaintenanceRequest $maintenanceRequest): void
    {
        $maintenanceRequest->consecutive = getLastConsecutive(MaintenanceRequest::class);
    }
}
