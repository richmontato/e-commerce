<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);

        Gate::define('manage-products', function (?User $user): bool {
            return $user?->isSeller() ?? false;
        });

        Gate::define('access-cart', function (?User $user): bool {
            return $user !== null && !$user->isAdmin();
        });

        Gate::define('view-orders', function (?User $user): bool {
            return $user !== null;
        });

        Gate::define('manage-vouchers', function (?User $user): bool {
            return $user?->isAdmin() ?? false;
        });

        Gate::define('view-sales-report', function (?User $user): bool {
            return $user?->isAdmin() ?? false;
        });
    }
}
