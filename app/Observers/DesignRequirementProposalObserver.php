<?php

namespace App\Observers;

use App\Models\DesignRequirementHeader;
use App\Models\DesignRequirementProposal;

class DesignRequirementProposalObserver
{
    /**
     * Handle the DesignRequirementProposal "created" event.
     */
    public function creating(DesignRequirementProposal $designRequirementProposal): void
    {
        $designRequirementProposal->consecutive = DesignRequirementHeader::find($designRequirementProposal->design_requirement_id)->proposals->max('consecutive') + 1;
    }

}
