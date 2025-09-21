<?php

namespace App\Providers;

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
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */


     
 public function boot()
{
    $this->registerPolicies();

    // Admin-only menu access - ONLY for Admin role
    Gate::define('admin-only', function ($user) {
        return $user && $user->role === 'Admin';
    });

    // Staff-only menu access - ONLY for Staff role, NOT for Admin
    Gate::define('staff-only', function ($user) {
        return $user && $user->role === 'Staff';
    });

    // Member-only menu access - ONLY for Member role
    Gate::define('member-only', function ($user) {
        return $user && $user->role === 'Member';
    });
}



}
