<?php

namespace App\Observers;

use App\Models\PackingOrderExportationList;

class PackingOrderExportationListObserver
{
    /**
     * @param PackingOrderExportationList $packingOrderExportationList
     * @return void
     */
    public function creating(PackingOrderExportationList $packingOrderExportationList): void
    {
        $packingOrderExportationList->consecutive = getLastConsecutive(PackingOrderExportationList::class);
    }
}
