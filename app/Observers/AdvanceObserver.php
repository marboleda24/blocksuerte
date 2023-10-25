<?php

namespace App\Observers;

use App\Models\Advance;

class AdvanceObserver
{
    /**
     * Handle the Advance "created" event.
     *
     * @param  Advance  $advance
     * @return void
     */
    public function creating(Advance $advance): void
    {
        $advance->consecutive = getLastConsecutive(Advance::class);
    }
}
