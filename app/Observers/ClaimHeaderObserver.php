<?php

namespace App\Observers;

use App\Mail\SystemNotificationMail;
use App\Models\ClaimHeader;
use Illuminate\Support\Facades\Mail;

class ClaimHeaderObserver
{
    /**
     * Handle the ClaimHeader "created" event.
     *
     * @param  ClaimHeader  $claimHeader
     * @return void
     */
    public function creating(ClaimHeader $claimHeader): void
    {
        $claimHeader->consecutive = getLastConsecutive(ClaimHeader::class);
    }

    /**
     * @param  ClaimHeader  $claimHeader
     * @return void
     */
    public function updated(ClaimHeader $claimHeader): void
    {
        if ($claimHeader->state === 'finish' && $claimHeader->user->email) {
            Mail::to($claimHeader->user->email)
                ->send(new SystemNotificationMail("Reclamo finalizado", "Reclamo finalizado", "EVPIU le informa que el reclamo con consecutivo {$claimHeader->consecutive} ha sido gestionado y se encuentra en estado finalizado"));

        } elseif ($claimHeader->state === 'refuse' && $claimHeader->user->email) {
            Mail::to($claimHeader->user->email)
                ->send(new SystemNotificationMail("Reclamo rechazado",
                    "Reclamo rechazado",
                    "EVPIU le informa que el reclamo con consecutivo {$claimHeader->consecutive} ha sido rechazado, por favor ingrese a la plataforma para obtener mas información"));

        }
    }
}
