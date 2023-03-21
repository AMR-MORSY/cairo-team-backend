<?php


namespace App\Services\EnergyAlarms;


class HTAlarmsHelpers{


    protected $HTAlarmsCollection,$downAlarmsCollection,$week;

    public function __construct($HTAlarms,$downAlarms = null,$week = null)
    {
        $this->HTAlarmsCollection=$HTAlarms;
        $this->downAlarmsCollection=$downAlarms;
        $this->week = $week;
        
    }

    public function zonesHTAlarmsCount($zones)
    {
        $oz = [];
        foreach ($zones as $zone) {
            $oz[$zone] = $this->HTAlarmsCollection->where("operational_zone", $zone)->count();
        }

        return $oz;
    }

    public function zonesSitesReportedAlarms($zones)
    {
        $oz = [];
        foreach ($zones as $zone) {
            $siteCodes = $this->HTAlarmsCollection->where("operational_zone", $zone)->groupBy("site_code")->count();
            $oz[$zone] = $siteCodes;
        }
        return $oz;
    }


    private function convertMinutesToHours($minutes)
    {
        $quotient = (int)($minutes / 60);
        $remainder = $minutes % 60;
        return " $quotient:$remainder";
    }
    public function zonesSitesReportedHTAlarmsDetails($zones)
    {
        $oz = [];

        foreach ($zones as $zone) {
            $sites = [];
            $siteCodes = $this->HTAlarmsCollection->where("operational_zone", $zone)->groupBy("site_code");

            foreach ($siteCodes as $key => $codes) {

                $siteCode = $codes->sortByDesc("duration");
                $siteCode = $siteCode->first();
                $subs["siteName"] =  $siteCode->site_name;
                $subs["siteCode"] =  $siteCode->site_code;
                $subs["count"]=$codes->count();
                $subs["highest_duration"] = $this->convertMinutesToHours($siteCode->duration);
                array_push($sites, $subs);
            }
            $oz[$zone] =$sites;
        }


        return $oz;
    }

}