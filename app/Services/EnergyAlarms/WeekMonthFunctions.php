<?php

namespace App\Services\EnergyAlarms;

class WeekMonthFunctions{

    protected $powerAlarms, $genAlarms, $HTAlarms, $downAlarms,$week,$month;
    public function __construct($powerAlarms, $genAlarms, $HTAlarms, $downAlarms,$week=null,$month=null)
    {
        $this->powerAlarms = $powerAlarms;
        $this->genAlarms = $genAlarms;
        $this->HTAlarms = $HTAlarms;
        $this->downAlarms = $downAlarms;
        $this->week=$week;
        $this->month=$month;
    }

    public function zonesPowerAlarmsCount()
    {

        $powerAlarmsStatestics = new PowerAlarmsHelpers($this->powerAlarms);

        $zonesPowerAlarmsCount = $powerAlarmsStatestics->zonesPowerAlarmsCount($this->powerAlarms->groupBy('operational_zone')->keys());

        return  $zonesPowerAlarmsCount;
    }
    public function zonesGenAlarmsCount()
    {

        $genAlarmsStatestics = new GenAlarmsHelpers($this->genAlarms);

        $zonesGenAlarmsCount = $genAlarmsStatestics->zonesGenAlarmsCount($this->genAlarms->groupBy('operational_zone')->keys());

        return  $zonesGenAlarmsCount;
    }

    public function zonesHTAlarmsCount()
    {
        $HTAlarmsStatestics=new HTAlarmsHelpers($this->HTAlarms);
         $zonesHTAlarmsCount=$HTAlarmsStatestics->zonesHTAlarmsCount($this->HTAlarms->groupBy('operational_zone')->keys());
         return $zonesHTAlarmsCount;
    }
    public function zonesSitesReportedPowerAlarms()
    {
        $powerAlarmsStatestics = new PowerAlarmsHelpers($this->powerAlarms);

        $zonesSitesReportedPowerAlarms = $powerAlarmsStatestics->zonesSitesReportedAlarms($this->powerAlarms->groupBy("operational_zone")->keys());

        return  $zonesSitesReportedPowerAlarms;
    }

    public function zonesSitesReportedGenAlarms()
    {
        $genAlarmsStatestics = new GenAlarmsHelpers($this->genAlarms);

        $zonesSitesReportedGenAlarms = $genAlarmsStatestics->zonesSitesReportedAlarms($this->genAlarms->groupBy("operational_zone")->keys());

        return  $zonesSitesReportedGenAlarms;
    }

    public function zonesSitesReportedHTAlarms()
    {
          $HTAlarmsStatestics = new HTAlarmsHelpers($this->HTAlarms);

        $zonesSitesReportedHTAlarms =$HTAlarmsStatestics->zonesSitesReportedAlarms($this->HTAlarms->groupBy("operational_zone")->keys());

        return   $zonesSitesReportedHTAlarms;
    }

    public function zonesSitesReportedHTAlarmsDetails()
    {   $HTAlarmsStatestics = new HTAlarmsHelpers($this->HTAlarms);
        $zonesSitesReportedHTAlarmsDetails =$HTAlarmsStatestics->zonesSitesReportedHTAlarmsDetails($this->HTAlarms->groupBy("operational_zone")->keys());
        return $zonesSitesReportedHTAlarmsDetails;

    }

    public function zonesSitesReportedGenAlarmsDetails()
    {     $genAlarmsStatestics = new GenAlarmsHelpers($this->genAlarms);
        $zonesSitesReportedGenAlarmsDetails = $genAlarmsStatestics->zonesSitesReportedGenAlarmsDetails($this->genAlarms->groupBy("operational_zone")->keys());
        return  $zonesSitesReportedGenAlarmsDetails ;

    }
    public function zonesSitesPowerAlarmsMoreThan()
    {
        $powerAlarmsStatestics = new PowerAlarmsHelpers($this->powerAlarms);
        $zonesSitesPowerAlarmsMoreThan = $powerAlarmsStatestics->zonesSitesPowerAlarmsMoreThan($this->powerAlarms->groupBy("operational_zone")->keys(),2);
        return $zonesSitesPowerAlarmsMoreThan;
    }
    public function zonesHighiestPowerAlarmDuration()
    {
        $powerAlarmsStatestics = new PowerAlarmsHelpers($this->powerAlarms);
        $zonesHighiestPowerAlarmDuration = $powerAlarmsStatestics->zonesHighiestAlarmDuration($this->powerAlarms->groupBy("operational_zone")->keys());
        return   $zonesHighiestPowerAlarmDuration;
    }
    public function zonesPowerDurationLessThanHour()
    {
        $powerAlarmsStatestics = new PowerAlarmsHelpers($this->powerAlarms);
        $zonesPowerDurationLessThanHour = $powerAlarmsStatestics->zonesPowerDurationLessThanHour($this->powerAlarms->groupBy("operational_zone")->keys());
        return  $zonesPowerDurationLessThanHour;
    }
    public function zonesDownSitesAfterPowerAlarm()
    {
        $powerAlarmsStatestics = new PowerAlarmsHelpers($this->powerAlarms, $this->downAlarms);
        $zonesDownSitesAfterPowerAlarm = $powerAlarmsStatestics->zonesDownSitesAfterPowerAlarm($this->powerAlarms->groupBy("operational_zone")->keys());
        return   $zonesDownSitesAfterPowerAlarm;
    }

   public function sitesDownWithoutPowerAlarms()
   {
    $powerAlarmsStatestics = new PowerAlarmsHelpers($this->powerAlarms, $this->downAlarms,$this->week,$this->month);
    $zonessitesDownWithoutPowerAlarms = $powerAlarmsStatestics->sitesDownWithoutPowerAlarms($this->powerAlarms->groupBy("operational_zone")->keys());
    return  $zonessitesDownWithoutPowerAlarms;

   }

}