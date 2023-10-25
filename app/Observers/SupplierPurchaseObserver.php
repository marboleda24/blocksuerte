<?php

namespace App\Observers;

use App\Mail\SystemNotificationMail;
use App\Models\SupplierPurchase;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SupplierPurchaseObserver
{
    /**
     * Handle the SupplierPurchase "updated" event.
     *
     * @param  SupplierPurchase  $supplierPurchase
     * @return void
     */
    public function updated(SupplierPurchase $supplierPurchase): void
    {
        if ($supplierPurchase->dian_state === '030') {
            $users = User::where('work_centers', 'like', "%{$supplierPurchase->work_center}%")
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();

            if (count($users) === 0 && empty($users)) {
                $users = ['dcorrea@estradavelasquez.com'];
            }

            Mail::to($users)
                ->send(new SystemNotificationMail("Nueva factura de proveedor",
                    "Nueva factura de proveedor",
                    "EVPIU le informa que el proveedor {$supplierPurchase->supplier['Name']} envío el documento con CUFE: {$supplierPurchase->document_information['ID']} y esta pendiente por aceptación de la mercancía"));
        }
    }
}
