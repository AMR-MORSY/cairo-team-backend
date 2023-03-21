<?php

namespace App\Services\EnergyAlarms;

use Carbon\Carbon;
use App\Models\NUR\NUR2G;
use App\Models\NUR\NUR3G;


class PowerAlarmsHelpers
{

    protected $powerAlarmsCollection, $downAlarmsCollection, $week,$month;

    public function __construct($powerAlarms=null, $downAlarms = null,$week = null,$month=null)
    {
        $this->powerAlarmsCollection = $powerAlarms;
        $this->downAlarmsCollection = $downAlarms;
        $this->week = $week;
        $this->month=$month;
      
    }

    public function zonesPowerAlarmsCount($zones)
    {
        $oz = [];
        foreach ($zones as $zone) {
            $oz[$zone] = $this->powerAlarmsCollection->where("operational_zone", $zone)->count();
        }

        return $oz;
    }
   

    public function zonesSitesReportedAlarms($zones)
    {
        $oz = [];
        foreach ($zones as $zone) {
            $siteCodes = $this->powerAlarmsCollection->where("operational_zone", $zone)->groupBy("site_code")->count();
            $oz[$zone] = $siteCodes;
        }
        return $oz;
    }
    public function zonesSitesPowerAlarmsMoreThan($zones, $times)
    {
        $oz = [];

        foreach ($zones as $zone) {
            $sites = [];
            $siteCodes = $this->powerAlarmsCollection->where("operational_zone", $zone)->groupBy("site_code");
            foreach ($siteCodes as $key => $codes) {
                $subs["siteName"] =  $codes->first()->site_name;
                $subs["siteCode"] =  $codes->first()->site_code;
                $subs["count"] =  $codes->count();
                array_push($sites, $subs);
            }
            $oz[$zone] = collect($sites)->where("count", ">", $times);
        }
        return $oz;
    }

    private function convertMinutesToHours($minutes)
    {
        $quotient = (int)($minutes / 60);
        $remainder = $minutes % 60;
        return " $quotient:$remainder";
    }

    public function zonesHighiestAlarmDuration($zones)
    {
        $oz = [];

        foreach ($zones as $zone) {
            $sites = [];
            $siteCodes = $this->powerAlarmsCollection->where("operational_zone", $zone)->groupBy("site_code");

            foreach ($siteCodes as $key => $codes) {

                $siteCode = $codes->sortByDesc("duration");
                $siteCode = $siteCode->first();
                $subs["siteName"] =  $siteCode->site_name;
                $subs["siteCode"] =  $siteCode->site_code;
                $subs["duration"] = $this->convertMinutesToHours($siteCode->duration);
                array_push($sites, $subs);
            }
            $oz[$zone] = collect($sites)->sortByDesc('duration')->take(10);
        }


        return $oz;
    }
    public function zonesPowerDurationLessThanHour($zones)
    {
        $oz = [];

        foreach ($zones as $zone) {

            $durations = [];
            $countSites = $this->powerAlarmsCollection->where("operational_zone", $zone)->where("duration", "<", 60)->count();
            $durations['count'] = $countSites;

            // foreach ($sites as  $site) {


            //     $subs["siteName"] =  $site->site_name;
            //     $subs["siteCode"] =  $site->site_code;
            //     $subs["duration"] =  $site->duration;
            //     array_push($durations, $subs);
            // }
            $oz[$zone] = $durations;
        }


        return $oz;
    }
    public function zonesDownSitesAfterPowerAlarm($zones)
    {
        // $oz = [];

        // foreach ($zones as $zone) {
        $sites = [];
        $siteCodes = $this->powerAlarmsCollection->where("operational_zone", "Cairo South")->groupBy("site_code");
        $downAlarms = $this->downAlarmsCollection->where("operational_zone", "Cairo South");
        foreach ($siteCodes as $key => $codes) {
            foreach ($codes as $code) {
                $downAlarm = $downAlarms->where("site_code", $code->site_code)->where("alarm_name", "NodeB Unavailable")->whereStrict("start_date", $code->start_date)->where("start_time", ">=", $code->start_time)->where("end_time", "<=", $code->end_time)->first();
                if ($downAlarm) {
                    $alarm["site_name"] = $downAlarm->site_name;
                    $alarm["site_code"] = $downAlarm->site_code;
                    $alarm["downAlarm_start_date"] = $downAlarm->start_date;
                    $alarm["powerAlarm_start_date"] = $code->start_date;
                    $alarm["downAlarm_id"] = $downAlarm->id;
                    $alarm['durationBeforDown'] = intdiv((strtotime($downAlarm->start_time) - strtotime($code->start_time)), 60);
                    $alarm["downAlarmDuration"] = intdiv((strtotime($downAlarm->end_time) - strtotime($downAlarm->start_time)), 60);
                    array_push($sites, $alarm);
                } else {
                    $downAlarm = $downAlarms->where("site_code", $code->site_code)->where("alarm_name", "OML Fault")->whereStrict("start_date", $code->start_date)->where("start_time", ">=", $code->start_time)->where("end_time", "<=", $code->end_time)->first();
                    if ($downAlarm) {
                        $alarm["site_name"] = $downAlarm->site_name;
                        $alarm["site_code"] = $downAlarm->site_code;
                        $alarm["downAlarm_start_date"] = $downAlarm->start_date;
                        $alarm["powerAlarm_start_date"] = $code->start_date;
                        $alarm["downAlarm_id"] = $downAlarm->id;
                        $alarm['durationBeforDown'] = intdiv((strtotime($downAlarm->start_time) - strtotime($code->start_time)), 60);
                        $alarm["downAlarmDuration"] = intdiv((strtotime($downAlarm->end_time) - strtotime($downAlarm->start_time)), 60);
                        array_push($sites, $alarm);
                    }
                }
            }
            // $downSite = $this->downAlarmsCollection->where("site_code", $code->site_code)->where("start_date", $code->start_date)->where("start_time", "<=", $code->start_time);
            // $downSites=$this->downAlarmsCollection->where("operational_zone", $zone)->where("site_code", $code);

        }
        $oz["Cairo South"] = $sites;

        return $oz;
    }

    public function sitesDownWithoutPowerAlarms()
    {
        $siteCodes = $this->downAlarmsCollection->where("operational_zone", "Cairo South")->groupBy("site_code");
        $powerAlarms = $this->powerAlarmsCollection->where("operational_zone", "Cairo South");
        $sites = [];
        foreach ($siteCodes as $key => $codes) {
            foreach ($codes as $code) {
                $powerAlarm = $powerAlarms->where("site_code", $code->site_code)->whereStrict("start_date", $code->start_date)->where("start_time", "<=", $code->start_time)->where("end_time", ">=", $code->end_time)->first();
                if (!isset($powerAlarm)) {
                    $alarm["site_name"] = $code->site_name;
                    $alarm["site_code"] = $code->site_code;
                    $alarm["downAlarm_start_date"] = $code->start_date;
                    $alarm["downAlarm_id"] = $code->id;
                    $alarm["downAlarmName"] = $code->alarm_name;
                    $alarm["downAlarmDuration"] = intdiv((strtotime($code->end_time) - strtotime($code->start_time)), 60);
                    array_push($sites, $alarm);
                }
            }
        }

        if (count($sites) > 0) {
            $sitoz = collect($sites);
            $newSiteCodes = $sitoz->groupBy("site_code");
            $newSites = [];

            $withNUR = [];
            $witoutNUR = [];
            foreach ($newSiteCodes as $key => $new_codes) {
               
                $NUR2G = [];
                $NUR3G = [];

                foreach ($new_codes as $newCode) {
                  
                    if ($newCode["downAlarmName"] == "OML Fault") {
                        if($this->week!=null)
                        {
                            $nurs = NUR2G::where("problem_site_code", $newCode["site_code"])->where("system","environmental")->where("sub_system","MAIN POWER")->where("week", $this->week)->get();
                            if (count($nurs) > 0) {
                                foreach ($nurs as $nur) {
                                    $begin = Carbon::parse($nur["begin"]);
                                    $newBegin = "$begin->year-$begin->month-$begin->day";
                                    if ($newBegin == $newCode["downAlarm_start_date"])
                                    {
                                        array_push($NUR2G, $nur);
    
                                    }
                                
                                }
                            }

                        }
                        else{
                            $nurs = NUR2G::where("problem_site_code", $newCode["site_code"])->where("system","environmental")->where("sub_system","MAIN POWER")->where("month", $this->month)->get();
                            if (count($nurs) > 0) {
                                foreach ($nurs as $nur) {
                                    $begin = Carbon::parse($nur["begin"]);
                                    $newBegin = "$begin->year-$begin->month-$begin->day";
                                    if ($newBegin == $newCode["downAlarm_start_date"])
                                    {
                                        array_push($NUR2G, $nur);
    
                                    }
                                
                                }
                            }

                        }
                       
                    }
                    if ($newCode["downAlarmName"] == "NodeB Unavailable") {
                        if($this->week!=null)
                        {
                            $nurs = NUR3G::where("problem_site_code", $newCode["site_code"])->where("system","environmental")->where("sub_system","MAIN POWER")->where("week", $this->week)->get();
                            if (count($nurs) > 0) {
                                foreach ($nurs as $nur) {
                                    $begin = Carbon::parse($nur["begin"]);
                                    $newBegin = "$begin->year-$begin->month-$begin->day";
                                    if ($newBegin == $newCode["downAlarm_start_date"])
                                    {
                                        array_push($NUR3G, $nur);
    
                                    }
                                   
                                }
                            }

                        }
                        else{
                            $nurs = NUR3G::where("problem_site_code", $newCode["site_code"])->where("system","environmental")->where("sub_system","MAIN POWER")->where("month", $this->month)->get();
                            if (count($nurs) > 0) {
                                foreach ($nurs as $nur) {
                                    $begin = Carbon::parse($nur["begin"]);
                                    $newBegin = "$begin->year-$begin->month-$begin->day";
                                    if ($newBegin == $newCode["downAlarm_start_date"])
                                    {
                                        array_push($NUR3G, $nur);
    
                                    }
                                   
                                }
                            }


                        }
                      
                    }
                  
                }
                if (count($NUR2G) > 0 || count($NUR3G) > 0) {
                    $NUR2G=collect($NUR2G);
                    $NUR2G=$NUR2G->unique();
                    $NUR3G=collect($NUR3G);
                    $NUR3G=$NUR3G->unique();
                    $newalarm["site_name"] = $new_codes->first()["site_name"];
                    $newalarm["site_code"] = $key;
                    $newalarm["count_down_alarms"] = $new_codes->count();
                    $newalarm["max_down_duration"] = $new_codes->max('downAlarmDuration');
                    $newalarm["min_down_duration"] = $new_codes->min('downAlarmDuration');
              
                    $newalarm["NUR2G"] = $NUR2G;
                    $newalarm["NUR3G"] = $NUR3G;

                    array_push($withNUR, $newalarm);
                 
                } else {
                    $newalarmo["site_name"] = $new_codes->first()["site_name"];
                   $newalarmo["site_code"] = $key;
                   $newalarmo["count_down_alarms"] = $new_codes->count();
                   $newalarmo["max_down_duration"] = $new_codes->max('downAlarmDuration');
                   $newalarmo["min_down_duration"] = $new_codes->min('downAlarmDuration');
                 
                    array_push($witoutNUR,$newalarmo);
                   

                }
            }
            $newSites["down_with_NUR"] = $withNUR;
            $newSites["down_without_NUR"] = $witoutNUR;

            $oz["Cairo South"] = $newSites;

            return $oz;
        } else {
            $oz["Cairo South"] = $sites;

            return $oz;
        }
    }
}
