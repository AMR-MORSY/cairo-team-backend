<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Policies\WANPolicy;
use App\Policies\XPICPolicy;
use App\Models\Transmission\XPIC;
use App\Policies\IP_trafficPolicy;
use App\Policies\ModificationPolicy;
use Illuminate\Support\Facades\Gate;
use App\Models\Transmission\IP_traffic;
use App\Models\Modifications\Modification;
use App\Models\Users\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //  'App\Models\Model' => 'App\Policies\ModelPolicy',
        WAN::class=>WANPolicy::class,
        XPIC::class=>XPICPolicy::class,
        IP_traffic::class=>IP_trafficPolicy::class,
        User::class=>UserPolicy::class,
        "App\Models\Model\Modifications\Modification"=>'App\Policies\ModificationPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
