<?php

namespace App\Providers;

use App\Models\Advance;
use App\Models\ClaimHeader;
use App\Models\DesignRequirementHeader;
use App\Models\DesignRequirementProposal;
use App\Models\HeaderCashReceipt;
use App\Models\HeaderOrder;
use App\Models\LocalCustomer;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceWorkOrder;
use App\Models\PackingOrderExportationList;
use App\Models\RemissionHeader;
use App\Models\SupplierPurchase;
use App\Observers\AdvanceObserver;
use App\Observers\ClaimHeaderObserver;
use App\Observers\DesignRequirementHeaderObserver;
use App\Observers\DesignRequirementProposalObserver;
use App\Observers\HeaderCashReceiptObserver;
use App\Observers\HeaderOrderObserver;
use App\Observers\LocalCustomerObserver;
use App\Observers\MaintenanceRequestObserver;
use App\Observers\MaintenanceWorkOrderObserver;
use App\Observers\PackingOrderExportationListObserver;
use App\Observers\RemissionHeaderObserver;
use App\Observers\SupplierPurchaseObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        HeaderOrder::observe(HeaderOrderObserver::class);
        ClaimHeader::observe(ClaimHeaderObserver::class);
        SupplierPurchase::observe(SupplierPurchaseObserver::class);
        LocalCustomer::observe(LocalCustomerObserver::class);
        MaintenanceRequest::observe(MaintenanceRequestObserver::class);
        MaintenanceWorkOrder::observe(MaintenanceWorkOrderObserver::class);
        Advance::observe(AdvanceObserver::class);
        HeaderCashReceipt::observe(HeaderCashReceiptObserver::class);
        RemissionHeader::observe(RemissionHeaderObserver::class);
        DesignRequirementHeader::observe(DesignRequirementHeaderObserver::class);
        DesignRequirementProposal::observe(DesignRequirementProposalObserver::class);
        PackingOrderExportationList::observe(PackingOrderExportationListObserver::class);
    }
}
