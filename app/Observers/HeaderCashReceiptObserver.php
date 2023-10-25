<?php

namespace App\Observers;

use App\Models\HeaderCashReceipt;

class HeaderCashReceiptObserver
{
    /**
     * Handle the HeaderCashReceipt "created" event.
     *
     * @param  HeaderCashReceipt  $headerCashReceipt
     * @return void
     */
    public function creating(HeaderCashReceipt $headerCashReceipt): void
    {
        $headerCashReceipt->consecutive = getLastConsecutive(HeaderCashReceipt::class);
    }
}
