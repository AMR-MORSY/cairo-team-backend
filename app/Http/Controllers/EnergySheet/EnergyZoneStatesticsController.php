<?php

namespace App\Http\Controllers\EnergySheet;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EnergySheet\HighTempAlarm;
use Illuminate\Support\Facades\Validator;
use App\Exports\Energy\ZonesHTAlarmsExport;

class EnergyZoneStatesticsController extends Controller
{
    private function convertMinutesToHours($minutes)
    {
        $quotient = (int)($minutes / 60);
        $remainder = $minutes % 60;
        return " $quotient:$remainder";
    }
    public function downloadZoneHTAlarms(Request $request)
    {
        $ruls=[
            "period_No"=>["required","regex:/^(?:[1-9]|[1-4][0-9]|5[0-2])$/"],
            "zone"=>["required","regex:/^Cairo South|Cairo East|Cairo North|Giza$/"],
            "period"=>["required","regex:/^week|month$/"],

        ];
        $validator = Validator::make($request->all(), $ruls);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->getMessageBag()->toArray(),

                ],
                422
            );
            $this->throwValidationException(


                $validator

            );
        } else {
            $validated = $validator->validated();
            $sites = [];
            $siteCodes = HighTempAlarm::where("operational_zone", $validated["zone"])->where($validated["period"],$validated["period_No"])->get();
            $siteCodes=$siteCodes->groupBy("site_code");
           
            foreach ($siteCodes as $key => $codes) {
                $subs=[];
               
    
                $siteCode = $codes->sortByDesc("duration");
                $siteCode = $siteCode->first();
                array_push($subs,$siteCode["site_name"]);
                array_push($subs,$siteCode["site_code"]);
                array_push($subs,$codes->count());
                array_push($subs,$this->convertMinutesToHours($siteCode["duration"]));
                // $subs["siteName"] =  $siteCode->site_name;
                // $subs["siteCode"] =  $siteCode->site_code;
                // $subs["count"]=$codes->count();
                // $subs["highest_duration"] = $this->convertMinutesToHours($siteCode->duration);
               
                array_push($sites, $subs);
            }
    
             return new ZonesHTAlarmsExport($sites);
            // return response()->json([
            //     "site"=>$sites,

            // ]
               

            // ) ;

           
          
        }

    }
}
