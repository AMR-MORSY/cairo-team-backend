<?php


namespace App\Services\EnergyAlarms;

class GenAlarmsHelpers{

    protected $GenAlarmsCollection,$week;

    public function __construct($GenAlarms,$week = null)
    {
        $this->GenAlarmsCollection=$GenAlarms;
        $this->week = $week;
        
    }

    public function zonesGenAlarmsCount($zones)
    {
        $oz = [];
        foreach ($zones as $zone) {
            $oz[$zone] = $this->GenAlarmsCollection->where("operational_zone", $zone)->count();
        }

        return $oz;
    }
    public function zonesSitesReportedAlarms($zones)
    {
        $oz = [];
        foreach ($zones as $zone) {
            $siteCodes = $this->GenAlarmsCollection->where("operational_zone", $zone)->groupBy("site_code")->count();
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

    public function zonesSitesReportedGenAlarmsDetails($zones)
    {
        $oz = [];

        foreach ($zones as $zone) {
            $sites = [];
            $siteCodes = $this->GenAlarmsCollection->where("operational_zone", $zone)->groupBy("site_code");

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