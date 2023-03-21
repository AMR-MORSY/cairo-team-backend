<?php

namespace App\Http\Controllers\EnergySheet;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EnergySheet\GenAlarm;
use App\Models\EnergySheet\DownAlarm;
use App\Models\EnergySheet\PowerAlarm;
use App\Models\EnergySheet\HighTempAlarm;
use Illuminate\Support\Facades\Validator;
use App\Services\EnergyAlarms\WeeklyStatestics;
use App\Services\EnergyAlarms\MonthlyStatestics;
use App\Services\EnergyAlarms\WeekMonthFunctions;


class EnergyStatesticsController extends Controller
{
    public function statestics($week,$year)
    {
        $data=[
           
            "week"=>$week,
          
            "year"=>$year
        ];
     
            
            $validator=Validator::make($data,["week"=>["required",'integer',"between:1,52"],"year" => ['required', 'regex:/^2[0-9]{3}$/']]);
    
        if($validator->fails())
        {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray()
            ],422);
        }
        else{
            $validated=$validator->validated();
         
                $weeklyAlarms=$this->getWeeklyAlarms($validated['week'],$validated['year']);
                if($weeklyAlarms['error'])
                {
                    return response()->json([
                        "errors"=>$weeklyAlarms['errors']
    
                    ],404);
    
  
                }
                else
                {
                    return response()->json([

                        "Alarms"=>$weeklyAlarms['statestics'],
    
                    ],200);
    

                  
                }
                 
            
        }
       
       
       
    }

    private function getWeeklyAlarms($week,$year)
    {
        $powerAlarms=PowerAlarm::where('week',$week)->where('year',$year)->get();
        $genAlrms=GenAlarm::where('week',$week)->where('year',$year)->get();
        $HT=HighTempAlarm::where('week',$week)->where('year',$year)->get();
        $downAlarms=DownAlarm::where('week',$week)->where('year',$year)->get();
        $errors=[];
        if(count($powerAlarms)<=0)
        {
             array_push($errors,"Power Alarms does not exist");
        }
        if(count($genAlrms)<=0)
        {
             array_push($errors,"Gen Alarms does not exist");
        }
        if(count($HT)<=0)
        {
             array_push($errors,"High Temp Alarms does not exist");
        }
        if(count( $downAlarms)<=0)
        {
            array_push($errors,"Down Alarms does not exist");

        }
        if(count($errors)>0)
        {
            $notFound["error"]=true;
            $notFound["errors"]=$errors;
            return $notFound;
        }
        else
        {
            $statestics=new WeekMonthFunctions($powerAlarms,$genAlrms,$HT,$downAlarms,$week);
            $zonesPowerAlarmsCount=$statestics->zonesPowerAlarmsCount();
            $zonesSitesReportedPowerAlarms=$statestics->zonesSitesReportedPowerAlarms();
            $zonesHighiestPowerAlarmDuration=$statestics->zonesHighiestPowerAlarmDuration();
            $zonesPowerDurationLessThanHour=$statestics->zonesPowerDurationLessThanHour();
            $zonesSitesPowerAlarmsMoreThan=$statestics->zonesSitesPowerAlarmsMoreThan();
            $zonesDownSitesAfterPowerAlarm=$statestics->zonesDownSitesAfterPowerAlarm();
            // $zonesSitesDownWithoutPowerAlarms=$statestics->sitesDownWithoutPowerAlarms();


            $zonesHTAlarmsCount=$statestics->zonesHTAlarmsCount();
            $zonesSitesReportedHTAlarms=$statestics->zonesSitesReportedHTAlarms();
            $zonesSitesReportedHTAlarmsDetails=$statestics->zonesSitesReportedHTAlarmsDetails();


            $zonesGenAlarmsCount=$statestics->zonesGenAlarmsCount();
            $zonesSitesReportedGenAlarms=$statestics->zonesSitesReportedGenAlarms();
            $zonesSitesReportedGenAlarmsDetails=$statestics->zonesSitesReportedGenAlarmsDetails();

            $data["period"]="week";
            $data["period_No"]=$week;
            $data['zonesPowerAlarmsCount']=$zonesPowerAlarmsCount;
            $data['zonesSitesReportedPowerAlarms']=$zonesSitesReportedPowerAlarms;
            $data['zonesSitesReportedPowerAlarms']=$zonesSitesReportedPowerAlarms;
            $data['zonesHighiestPowerAlarmDuration']=$zonesHighiestPowerAlarmDuration;
            $data['zonesPowerDurationLessThanHour']=$zonesPowerDurationLessThanHour;
            $data['zonesSitesPowerAlarmsMoreThan2Times']=$zonesSitesPowerAlarmsMoreThan;
            $data['zonesDownSitesAfterPowerAlarm']=$zonesDownSitesAfterPowerAlarm;
            // $data['zonesSitesDownWithoutPowerAlarms']= $zonesSitesDownWithoutPowerAlarms;

            $data["zonesHTAlarmsCount"]=$zonesHTAlarmsCount;
            $data['zonesSitesReportedHTAlarms']=$zonesSitesReportedHTAlarms;
            $data['zonesSitesReportedHTAlarmsDetails']=$zonesSitesReportedHTAlarmsDetails;

            $data["zonesGenAlarmsCount"]=$zonesGenAlarmsCount;
            $data['zonesSitesReportedGenAlarms']= $zonesSitesReportedGenAlarms;
            $data['zonesSitesReportedGenAlarmsDetails']=$zonesSitesReportedGenAlarmsDetails;

            $notFound['error']=false;
            $notFound['statestics']=$data;

            return $notFound;

        }

    }
     
    private function getMonthlyAlarms($month,$year)
    {
        $powerAlarms=PowerAlarm::where("month",$month)->where('year',$year)->get();
        $genAlrms=GenAlarm::where("month",$month)->where('year',$year)->get();
        $HT=HighTempAlarm::where("month",$month)->where('year',$year)->get();
        $downAlarms=DownAlarm::where("month",$month)->where('year',$year)->get();
        $errors=[];
        if(count( $powerAlarms)<=0)
        {
             array_push($errors,"Power Alarms does not exist");
        }
        if(count($genAlrms)<=0)
        {
             array_push($errors,"Gen Alarms does not exist");
        }
        if(count($HT)<=0)
        {
             array_push($errors,"High Temp Alarms does not exist");
        }
        if(count( $downAlarms)<=0)
        {
            array_push($errors,"Down Alarms does not exist");

        }
        if(count($errors)>0)
        {
            $notFound["error"]=true;
            $notFound["errors"]=$errors;
            return $notFound;
        }
        else
        {
            $statestics=new WeekMonthFunctions($powerAlarms,$genAlrms,$HT,$downAlarms,null,$month);
            $zonesPowerAlarmsCount=$statestics->zonesPowerAlarmsCount();
            $zonesSitesReportedPowerAlarms=$statestics->zonesSitesReportedPowerAlarms();
            $zonesHighiestPowerAlarmDuration=$statestics->zonesHighiestPowerAlarmDuration();
            $zonesPowerDurationLessThanHour=$statestics->zonesPowerDurationLessThanHour();
            $zonesSitesPowerAlarmsMoreThan=$statestics->zonesSitesPowerAlarmsMoreThan();
            $zonesDownSitesAfterPowerAlarm=$statestics->zonesDownSitesAfterPowerAlarm();
            // $zonesSitesDownWithoutPowerAlarms=$statestics->sitesDownWithoutPowerAlarms();


            $zonesHTAlarmsCount=$statestics->zonesHTAlarmsCount();
            $zonesSitesReportedHTAlarms=$statestics->zonesSitesReportedHTAlarms();
            $zonesSitesReportedHTAlarmsDetails=$statestics->zonesSitesReportedHTAlarmsDetails();


            $zonesGenAlarmsCount=$statestics->zonesGenAlarmsCount();
            $zonesSitesReportedGenAlarms=$statestics->zonesSitesReportedGenAlarms();
            $zonesSitesReportedGenAlarmsDetails=$statestics->zonesSitesReportedGenAlarmsDetails();

            $data["period"]="month";
            $data["period_No"]=$month;
            $data['zonesPowerAlarmsCount']=$zonesPowerAlarmsCount;
            $data['zonesSitesReportedPowerAlarms']=$zonesSitesReportedPowerAlarms;
            $data['zonesSitesReportedPowerAlarms']=$zonesSitesReportedPowerAlarms;
            $data['zonesHighiestPowerAlarmDuration']=$zonesHighiestPowerAlarmDuration;
            $data['zonesPowerDurationLessThanHour']=$zonesPowerDurationLessThanHour;
            $data['zonesSitesPowerAlarmsMoreThan2Times']=$zonesSitesPowerAlarmsMoreThan;
            $data['zonesDownSitesAfterPowerAlarm']=$zonesDownSitesAfterPowerAlarm;
            // $data['zonesSitesDownWithoutPowerAlarms']= $zonesSitesDownWithoutPowerAlarms;

            $data["zonesHTAlarmsCount"]=$zonesHTAlarmsCount;
            $data['zonesSitesReportedHTAlarms']=$zonesSitesReportedHTAlarms;
            $data['zonesSitesReportedHTAlarmsDetails']=$zonesSitesReportedHTAlarmsDetails;

            $data["zonesGenAlarmsCount"]=$zonesGenAlarmsCount;
            $data['zonesSitesReportedGenAlarms']= $zonesSitesReportedGenAlarms;
            $data['zonesSitesReportedGenAlarmsDetails']=$zonesSitesReportedGenAlarmsDetails;

            $notFound['error']=false;
            $notFound['statestics']=$data;

            return $notFound;


        }


    }
    // public function siteAlarms(Request $request)
    // {
    //     $validator = Validator::make($request->all(), ["siteCode" => ["required", "regex:/^([0-9a-zA-Z]{4,6}(up|UP))|([0-9a-zA-Z]{4,6}(ca|CA))|([0-9a-zA-Z]{4,6}(de|DE))$/"]]);
    //     if ($validator->fails()) {
    //         return response()->json(array(

    //             'errors' => $validator->getMessageBag()->toArray()
    //         ), 422);


    //         $this->throwValidationException(

    //             $request,
    //             $validator

    //         );
    //     } else {
    //         $validated = $validator->validated();
    //         $powerAlarms = PowerAlarm::where("site_code", $validated['siteCode'])->get();
    //         $genAlrms=GenAlarm::where("site_code", $validated['siteCode'])->get();
    //         $HT=HighTempAlarm::where("site_code", $validated['siteCode'])->get();
    //         $downAlarms=DownAlarm::where("site_code", $validated['siteCode'])->get();
    //         return response()->json([
    //             "powerAlarms" => $powerAlarms,
    //             "downAlarms"=>$downAlarms,
    //             "HT"=>$HT,
    //             "genAlarms"=>$genAlrms,
    //         ], 200);
    //     }
    // }
}
