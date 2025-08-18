<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
{
    $this->registerPolicies();

    Gate::define('create-user-with-role', function ($user, $requestedRole) {
        // Aturan untuk Kepala Apoteker
        if ($user->hasRole('kepala apotek')) {
            return in_array($requestedRole, ['admin', 'apoteker']);
        }

        // Aturan untuk Admin
        if ($user->hasRole('admin')) {
            return $requestedRole === 'apoteker';
        }

        return false;
    });
}
}
