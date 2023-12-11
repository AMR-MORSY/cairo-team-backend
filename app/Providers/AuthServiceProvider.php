<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\EnergySheet\DownAlarm;
use App\Models\EnergySheet\GenAlarm;
use App\Models\EnergySheet\HighTempAlarm;
use App\Models\EnergySheet\PowerAlarm;
use App\Models\Instruments\Instrument;
use App\Policies\WANPolicy;
use App\Policies\XPICPolicy;
use App\Models\Transmission\XPIC;
use App\Policies\IP_trafficPolicy;
use App\Policies\ModificationPolicy;
use Illuminate\Support\Facades\Gate;
use App\Models\Transmission\IP_traffic;
use App\Models\Modifications\Modification;
use App\Models\NUR\NUR2G;
use App\Models\NUR\NUR3G;
use App\Models\NUR\NUR4G;
use App\Models\Users\User;
use App\Policies\DownAlarmPolicy;
use App\Policies\GenAlarmPolicy;
use App\Policies\HighTempAlarmPolicy;
use App\Policies\InstrumentPolicy;
use App\Policies\NUR2GPolicy;
use App\Policies\NUR3GPolicy;
use App\Policies\NUR4GPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PowerAlarmPolicy;
use App\Policies\RolePolicy;
use App\Policies\SitePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        Modification::class=>ModificationPolicy::class,
        Site::class=>SitePolicy::class,
        Instrument::class=>InstrumentPolicy::class,
        PowerAlarm::class=>PowerAlarmPolicy::class,
        HighTempAlarm::class=>HighTempAlarmPolicy::class,
        DownAlarm::class=>DownAlarmPolicy::class,
        GenAlarm::class=>GenAlarmPolicy::class,
        NUR2G::class=>NUR2GPolicy::class,
        NUR3G::class=>NUR3GPolicy::class,
        NUR4G::class=>NUR4GPolicy::class,
        Permission::class=>PermissionPolicy::class,
        Role::class=>RolePolicy::class,

      
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
