<?php

namespace App\Observers;

use App\Models\DesignRequirementHeader;

class DesignRequirementHeaderObserver
{
    /**
     * Handle the DesignRequirementHeader "created" event.
     */
    public function creating(DesignRequirementHeader $designRequirementHeader): void
    {
        $designRequirementHeader->consecutive = DesignRequirementHeader::max('consecutive') + 1;
    }

}
