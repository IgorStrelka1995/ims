<?php

namespace App\Providers;

use App\Events\ProductCreated;
use App\Events\ProductDelete;
use App\Events\ProductUpdated;
use App\Events\StockIn;
use App\Events\StockOut;
use App\Listeners\HandleCreatedProductToAudit;
use App\Listeners\HandleCreatedProductToStock;
use App\Listeners\HandleStockInToAudit;
use App\Listeners\HandleStockOutToAudit;
use App\Listeners\HandleUpdatedProductToAudit;
use App\Listeners\HandleUpdatedProductToStock;
use App\Listeners\HandleUpdateProductQuantity;
use App\Listeners\SaveAuditLogs;
use App\Listeners\SaveInventoryProductCreation;
use App\Listeners\SaveInventoryProductDelete;
use App\Listeners\SaveInventoryProductUpdate;
use App\Listeners\SaveProductQuantity;
use App\Listeners\SaveStockAuditLogs;
use App\Listeners\SaveStockChanges;
use App\Listeners\SaveStockUpdate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ProductCreated::class => [
            HandleCreatedProductToStock::class,
            HandleCreatedProductToAudit::class
        ],
        ProductUpdated::class => [
            HandleUpdatedProductToStock::class,
            HandleUpdatedProductToAudit::class
        ],
        StockIn::class => [
            HandleUpdateProductQuantity::class,
            HandleStockInToAudit::class
        ],
        StockOut::class => [
            HandleUpdateProductQuantity::class,
            HandleStockOutToAudit::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
