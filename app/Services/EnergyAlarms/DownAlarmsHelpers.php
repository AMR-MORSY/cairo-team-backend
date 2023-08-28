<?php

namespace App\Services\EnergyAlarms;

use App\Services\EnergyAlarms\HTAlarmsHelpers;

class DownAlarmsHelpers
{
    protected $downAlarmsCollection;

    public function __construct($downAlarms)
    {
        $this->downAlarmsCollection = $downAlarms;
    }
    public function zonesDownAlarmsCount($zones)
    {
        $oz = [];
        foreach ($zones as $zone) {
            $oz[$zone] = $this->downAlarmsCollection->where("operational_zone", $zone)->count();
        }
        $totalAlarms = 0;
        foreach ($oz as $key => $zone) {
            $totalAlarms = $totalAlarms + $zone;
        }

        $oz['Cairo'] = $totalAlarms;

        return $oz;
    }
    private function extractSites($groupedAlarms)
    {
        $sites = [];
        foreach ($groupedAlarms as $key => $alarm) {
            $site["site_code"] = $key;
            $site["site_name"] = $alarm->first()->site_name;
            $site["repeatation"] = $alarm->count();
            $site["avg_duration"] = HTAlarmsHelpers::convertMinutesToHours($alarm->avg('duration'));
            array_push($sites, $site);
        }
        return $sites;
    }
    public function zoneSitesReportedDownAlarms()
    {
        $alarmsGroupedByCodes2G = $this->downAlarmsCollection->where("alarm_name", "OML Fault")->groupBy("site_code");
        $alarmsGroupedByCodes3G = $this->downAlarmsCollection->where("alarm_name", "NodeB Unavailable")->groupBy("site_code");
        $sites2G = $this->extractSites($alarmsGroupedByCodes2G);
        $sites3G = $this->extractSites($alarmsGroupedByCodes3G);

        $allSites["down2G"] = $sites2G;
        $allSites["down3G"] = $sites3G;
        return $allSites;
    }

    private function calculateDuration($start, $end)

    {
        $result = intdiv((strtotime($start) - strtotime($end)), 60);
        if ($result < 0) {
            return $result+(24*60);
        }
        else{
            return $result;
        }
    }

    public function zoneDownSitesAfterPowerAlarm($powerAlarmsCollection)
    {
        $sites = [];
        $siteCodes = $powerAlarmsCollection->groupBy("site_code");
      
        foreach ($siteCodes as $key => $codes) {
            foreach ($codes as $code) {
                $downAlarm = $this->downAlarmsCollection->where("site_code", $code->site_code)->where("alarm_name", "NodeB Unavailable")->whereStrict("start_date", $code->start_date)->where("end_date", $code->end_date)->where("start_time", ">=", $code->start_time)->where("end_time", "<=", $code->end_time)->first();
                if ($downAlarm) {
                    $alarm["site_name"] = $downAlarm->site_name;
                    $alarm["site_code"] = $downAlarm->site_code;
                    $alarm["downAlarm_start_date"] = $downAlarm->start_date;
                    $alarm["downAlarm_start_time"] = $downAlarm->start_time;
                    $alarm["downAlarm_end_date"] = $downAlarm->end_date;
                    $alarm["downAlarm_end_time"] = $downAlarm->end_time;

                    $alarm["powerAlarm_start_date"] = $code->start_date;
                    $alarm["powerAlarm_start_time"] = $code->start_time;
                    $alarm["powerAlarm_end_date"] = $code->end_date;
                    $alarm["powerAlarm_end_time"] = $code->end_time;
                    $alarm["downAlarm_id"] = $downAlarm->id;
                    $alarm['durationBeforDown'] =$this->calculateDuration($downAlarm->start_time,$code->start_time);
                 
                    $alarm["downAlarmDuration"] =$this->calculateDuration($downAlarm->end_time,$downAlarm->start_time);
              
                    array_push($sites, $alarm);
                }
              
            }
           
        }
       
        return $sites;
    }
    public function zoneSitesDownWithoutPowerAlarms($powerAlarmsCollection)
    {
        $downAlarmsGroupedCodes = $this->downAlarmsCollection->groupBy("site_code");
       
        $sites = [];
        foreach ($downAlarmsGroupedCodes as $key => $codes) {
            $powerAlarm=$powerAlarmsCollection->where("site_code", $key)->first();
            if(!$powerAlarm)
            {
                $alarm["site_name"] = $codes->first()["site_name"];
                $alarm["site_code"] = $key;
                $alarm["repeatation"]=$codes->count();
                $alarm["avg_duration"]=$codes->avg("duration");
                array_push($sites,$alarm);

            }
          
           
        }

        return $sites;

    }
    
}
