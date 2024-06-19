<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Audit;
use App\Models\Product;
use App\Models\Stock;
use App\Policies\AuditPolicy;
use App\Policies\ProductPolicy;
use App\Policies\StockPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
        Stock::class => StockPolicy::class,
        Audit::class => AuditPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
