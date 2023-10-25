<?php

namespace App\Observers;

use App\Mail\SystemNotificationMail;
use App\Models\LocalCustomer;
use Illuminate\Support\Facades\Mail;

class LocalCustomerObserver
{
    /**
     * Handle the LocalCustomer "created" event.
     *
     * @param  LocalCustomer  $localCustomer
     * @return void
     */
    public function created(LocalCustomer $localCustomer): void
    {
        Mail::to(['sistemas@estradavelasquez.com', 'auxsistemas@estradavelasquez.com', 'cartera@estradavelasquez.com'])
            ->send(new SystemNotificationMail("Nuevo cliente creado",
                "Nuevo cliente creado",
                "EVPIU le informa que el usuario {$localCustomer->createdby->name} creo el cliente {$localCustomer->company_name} con NIT {$localCustomer->document} y esta pendiente por asignación de cupo"));

    }
}
