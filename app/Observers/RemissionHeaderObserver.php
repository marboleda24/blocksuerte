<?php

namespace App\Observers;

use App\Models\RemissionHeader;

class RemissionHeaderObserver
{
    /**
     * Handle the Advance "created" event.
     *
     * @param  RemissionHeader  $remissionHeader
     * @return void
     */
    public function creating(RemissionHeader $remissionHeader): void
    {
        $remissionHeader->consecutive = getLastConsecutive(RemissionHeader::class);
    }
}
