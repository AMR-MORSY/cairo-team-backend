<?php

namespace App\Http\Controllers\NUR;

use App\Models\NUR\NUR2G;
use App\Models\NUR\NUR3G;
use App\Models\NUR\NUR4G;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sites\Site;
use App\Services\NUR\NURStatestics\CairoWeeklyStatestics;
use Illuminate\Support\Facades\Validator;
use App\Services\NUR\NURStatestics\ZoneWeeklyStatestics;
use App\Services\NUR\NURStatestics\YearlyStatestics;
use App\Services\NUR\WeeklyNUR;
use Illuminate\Database\Eloquent\Collection;
use stdClass;



class ShowNURController extends Controller
{
    
   

    public function show_nur($week, $year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $data = [

            "week" => $week,

            "year" => $year
        ];


        $validator = Validator::make($data, ["week" => ["required", 'integer', "between:1,52"], "year" => ['required', 'regex:/^2[0-9]{3}$/']]);




        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        } else {
            $validated = $validator->validated();

            $weeklyNUR = $this->getWeeklyNUR($validated['week'], $validated['year']);
            if (isset($weeklyNUR['error'])) {
                return response()->json([
                    "errors" => $weeklyNUR['errors'],

                ], 200);
            } else {
                return response()->json([

                    "NUR" => $weeklyNUR,


                ], 200);
            }
        }
    }

    public function cairoYearlyNUR_C($year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $total_year_tickets_2G = NUR2G::where('year', $year)->get();
        $total_year_tickets_3G = NUR3G::where('year', $year)->get();
        $total_year_tickets_4G = NUR4G::where('year', $year)->get();
        $errors = [];
        if (count($total_year_tickets_2G) <= 0) {
            array_push($errors, "2G NUR does not exist");
        }
        if (count($total_year_tickets_3G) <= 0) {
            array_push($errors, "3G NUR does not exist");
        }
        if (count($total_year_tickets_4G) <= 0) {
            array_push($errors, "4G NUR does not exist");
        }
        if (count($errors) > 0) {
            return response()->json([
                "errors"=>$errors
            ],204);
           
          
        } else {
           
            $statestics = new YearlyStatestics($total_year_tickets_2G, $total_year_tickets_3G, $total_year_tickets_4G,$year);
            $NUR_C_yearly=$statestics->cairoNUR_C();
            return response()->json([
                "NUR_C_yearly"=>$NUR_C_yearly
            ],200);
          
           
        }
    }
    public function cairoModificationYearlyNUR($year){
        $this->authorize("viewAny",NUR2G::class);
        $total_year_tickets_2G = NUR2G::where('year', $year)->where("system","production")->get();
        $total_year_tickets_3G = NUR3G::where('year', $year)->where("system","production")->get();
        $total_year_tickets_4G = NUR4G::where('year', $year)->where("system","production")->get();
        $errors = [];
        if (count($total_year_tickets_2G) <= 0) {
            array_push($errors, "2G NUR does not exist");
        }
        if (count($total_year_tickets_3G) <= 0) {
            array_push($errors, "3G NUR does not exist");
        }
        if (count($total_year_tickets_4G) <= 0) {
            array_push($errors, "4G NUR does not exist");
        }
        if (count($errors) > 0) {
            return response()->json([
                "errors"=>$errors
            ],200);
           
          
        } else {
           
            $statestics = new YearlyStatestics($total_year_tickets_2G, $total_year_tickets_3G, $total_year_tickets_4G,$year);
            $NUR_Gen_yearly=$statestics->cairoModificationNUR();
            return response()->json([
                "NUR_C_yearly"=>$NUR_Gen_yearly
            ],200);
          
           
        }


    }
    public function cairoPowerYearlyNUR($year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $total_year_tickets_2G = NUR2G::where('year', $year)->where("sub_system","main power")->get();
        $total_year_tickets_3G = NUR3G::where('year', $year)->where("sub_system","main power")->get();
        $total_year_tickets_4G = NUR4G::where('year', $year)->where("sub_system","main power")->get();
        $errors = [];
        if (count($total_year_tickets_2G) <= 0) {
            array_push($errors, "2G NUR does not exist");
        }
        if (count($total_year_tickets_3G) <= 0) {
            array_push($errors, "3G NUR does not exist");
        }
        if (count($total_year_tickets_4G) <= 0) {
            array_push($errors, "4G NUR does not exist");
        }
        if (count($errors) > 0) {
            return response()->json([
                "errors"=>$errors
            ],200);
           
          
        } else {
           
            $statestics = new YearlyStatestics($total_year_tickets_2G, $total_year_tickets_3G, $total_year_tickets_4G,$year);
            $NUR_Gen_yearly=$statestics->cairoPowerNUR();
            return response()->json([
                "NUR_C_yearly"=>$NUR_Gen_yearly
            ],200);
          
           
        }

    }

    public function cairoNodeBYearlyNUR($year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $total_year_tickets_2G = NUR2G::where('year', $year)->where("system","bss")->get();
        $total_year_tickets_3G = NUR3G::where('year', $year)->where("system","ran")->get();
        $total_year_tickets_4G = NUR4G::where('year', $year)->where("system","ran")->get();
        $errors = [];
        if (count($total_year_tickets_2G) <= 0) {
            array_push($errors, "2G NUR does not exist");
        }
        if (count($total_year_tickets_3G) <= 0) {
            array_push($errors, "3G NUR does not exist");
        }
        if (count($total_year_tickets_4G) <= 0) {
            array_push($errors, "4G NUR does not exist");
        }
        if (count($errors) > 0) {
            return response()->json([
                "errors"=>$errors
            ],200);
           
          
        } else {
           
            $statestics = new YearlyStatestics($total_year_tickets_2G, $total_year_tickets_3G, $total_year_tickets_4G,$year);
            $NUR_Gen_yearly=$statestics->cairoTxNUR();
            return response()->json([
                "NUR_C_yearly"=>$NUR_Gen_yearly
            ],200);
          
           
        }

    }
    public function cairoMWYearlyNUR($year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $total_year_tickets_2G = NUR2G::where('year', $year)->where("system","transmission")->get();
        $total_year_tickets_3G = NUR3G::where('year', $year)->where("system","transmission")->get();
        $total_year_tickets_4G = NUR4G::where('year', $year)->where("system","transmission")->get();
        $errors = [];
        if (count($total_year_tickets_2G) <= 0) {
            array_push($errors, "2G NUR does not exist");
        }
        if (count($total_year_tickets_3G) <= 0) {
            array_push($errors, "3G NUR does not exist");
        }
        if (count($total_year_tickets_4G) <= 0) {
            array_push($errors, "4G NUR does not exist");
        }
        if (count($errors) > 0) {
            return response()->json([
                "errors"=>$errors
            ],200);
           
          
        } else {
           
            $statestics = new YearlyStatestics($total_year_tickets_2G, $total_year_tickets_3G, $total_year_tickets_4G,$year);
             $NUR_Gen_yearly=$statestics->cairoTxNUR();
            return response()->json([
                "NUR_C_yearly"=>$NUR_Gen_yearly
            ],200);
          
           
        }

    }
    public function cairoGenYearlyNUR($year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $total_year_tickets_2G = NUR2G::where('year', $year)->where("sub_system","GENERATOR")->get();
        $total_year_tickets_3G = NUR3G::where('year', $year)->where("sub_system","GENERATOR")->get();
        $total_year_tickets_4G = NUR4G::where('year', $year)->where("sub_system","GENERATOR")->get();
        $errors = [];
        if (count($total_year_tickets_2G) <= 0) {
            array_push($errors, "2G NUR does not exist");
        }
        if (count($total_year_tickets_3G) <= 0) {
            array_push($errors, "3G NUR does not exist");
        }
        if (count($total_year_tickets_4G) <= 0) {
            array_push($errors, "4G NUR does not exist");
        }
        if (count($errors) > 0) {
            return response()->json([
                "errors"=>$errors
            ],200);
           
          
        } else {
           
            $statestics = new YearlyStatestics($total_year_tickets_2G, $total_year_tickets_3G, $total_year_tickets_4G,$year);
            $NUR_Gen_yearly=$statestics->cairoGenNUR();
            return response()->json([
                "NUR_C_yearly"=>$NUR_Gen_yearly
            ],200);
          
           
        }

    }

    private function collectAllWeekTickets($total_week_tickets_2G,$total_week_tickets_3G,$total_week_tickets_4G):Collection///returns a collection of all technology tickets
    {
       
          
            foreach($total_week_tickets_3G as $ticket)
            {
               $total_week_tickets_2G->push($ticket);
            }
            $allTickets=$total_week_tickets_2G;

            foreach($total_week_tickets_4G as $ticket)
            {
                $allTickets->push($ticket);
            }
           
           return $allTickets;

    }

    private function getWeekTechTickets($week,$year):array/////this function returns total week tickets for each technology
    {
        $total_week_tickets_2G = NUR2G::where('year', $year)->where('week', $week)->get();/////collection
       
        $total_week_tickets_3G = NUR3G::where('year', $year)->where('week', $week)->get();
      
        $total_week_tickets_4G = NUR4G::where('year', $year)->where('week', $week)->get();
      
        $total_week['tickets_2G']=$total_week_tickets_2G;
        $total_week['tickets_3G']=$total_week_tickets_3G;
        $total_week['tickets_4G']=$total_week_tickets_4G;

        return $total_week;
        

    }
    private function getWeeklyNUR($week, $year)
    {
       
        $total_week=$this->getWeekTechTickets($week,$year);
        
        $errors = [];
        if (count($total_week['tickets_2G']) <= 0) {
            array_push($errors, "2G NUR does not exist");
        }
        if (count( $total_week['tickets_3G']) <= 0) {
            array_push($errors, "3G NUR does not exist");
        }
        if (count( $total_week['tickets_4G']) <= 0) {
            array_push($errors, "4G NUR does not exist");
        }
        if (count($errors) > 0) {
            $notFound["error"] = true;
            $notFound["errors"] = $errors;
            return $notFound;
        } else {
           
            $allTickets=$this->collectAllWeekTickets( $total_week['tickets_2G'], $total_week['tickets_3G'], $total_week['tickets_4G']);
            $NUR['NUR2G'] = ZoneWeeklyStatestics::NUR2GStatestics( $total_week['tickets_2G']);  
            $NUR['NUR3G'] = ZoneWeeklyStatestics::NUR3GStatestics( $total_week['tickets_3G']);
            $NUR['NUR4G'] = ZoneWeeklyStatestics::NUR4GStatestics( $total_week['tickets_4G']);
            $NUR["zonesSubsystem"]=ZoneWeeklyStatestics::zonesSubsystemNUR($allTickets);
            $NUR["zonesSubsystemCountTickts"]=ZoneWeeklyStatestics::zonesSubsystemCountTickts($allTickets);
            $NUR["zonesResponseWithAccess"]=ZoneWeeklyStatestics::zonesResponseWithAccess($allTickets);
            $NUR["zonesResponseWithoutAccess"]=ZoneWeeklyStatestics::zonesResponseWithoutAccess($allTickets);
            $NUR["zonesGeneratorStatestics"]=ZoneWeeklyStatestics::zonesGeneratorStatestics($allTickets);
            $NUR["topRepeated"] = ZoneWeeklyStatestics::zonesTopRepeated($allTickets);
            $NUR["topNUR"] = ZoneWeeklyStatestics::zonesTopNUR($allTickets);
             $NUR['combined'] = ZoneWeeklyStatestics:: combinedNUR($allTickets);

             return $NUR;
            
        }
    }

    public function siteNUR(Request $request)
    {
        $this->authorize("viewAny",NUR2G::class);
        $validator = validator::make($request->all(), ["site_code" => ["required", "regex:/^([0-9a-zA-Z]{4,6}(up|UP))|([0-9a-zA-Z]{4,6}(ca|CA))|([0-9a-zA-Z]{4,6}(de|DE))$/"]]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        } else {
            $validated = $validator->validated();
            $site_2GNUR = NUR2G::where("problem_site_code", $validated["site_code"])->get();
            $site_3GNUR = NUR3G::where("problem_site_code", $validated["site_code"])->get();
            $site_4GNUR = NUR4G::where("problem_site_code", $validated["site_code"])->get();

            return response()->json([
                "NUR2G" => $site_2GNUR,
                "NUR3G" => $site_3GNUR,
                "NUR4G" => $site_4GNUR,
            ], 200);
        }
    }
    public function vipSitesWeeklyNUR($zone, $week, $year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $data = [
            "zone" => $zone,
            "week" => $week,
            "year" => $year
        ];
        $validator = Validator::make($data, ["week" => ["required", 'integer', "between:1,52"], "zone" => ['required', 'regex:/^Cairo East|Cairo South|Cairo North|Giza$/'], "year" => ['required', 'regex:/^2[0-9]{3}$/']]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        } else {
            $validated = $validator->validated();
            $vip_sites = Site::where("oz", $validated["zone"])->where("category", "VIP")->orWhere("oz", $validated["zone"])->where("category", "VIP + NDL")->get();
            $vip_sites_codes = $vip_sites->groupBy("site_code")->keys();
            $vip_sites_names = $vip_sites->groupBy("site_name")->keys();

            $sites = $this->getVipORNodalNUR($vip_sites_codes, $vip_sites_names, $week, $validated['year']);



            return response()->json([
                "sites" => $sites
            ], 200);
        }
    }
    public function nodalSitesWeeklyNUR($zone, $week, $year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $data = [
            "zone" => $zone,
            "week" => $week,
            "year" => $year
        ];
        $validator = Validator::make($data, ["week" => ["required", 'integer', "between:1,52"], "zone" => ['required', 'regex:/^Cairo East|Cairo South|Cairo North|Giza$/'], "year" => ['required', 'regex:/^2[0-9]{3}$/']]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        } else {
            $validated = $validator->validated();
            $vip_sites = Site::where("oz", $validated["zone"])->where("category", "NDL")->orWhere("oz", $validated["zone"])->where("category", "VIP + NDL")->get();
            $nodal_sites_codes = $vip_sites->groupBy("site_code")->keys();
            $nodal_sites_names = $vip_sites->groupBy("site_name")->keys();
            $sites = $this->getVipORNodalNUR($nodal_sites_codes, $nodal_sites_names, $week, $validated['year']);
            return response()->json([
                "sites" => $sites
            ], 200);
        }
    }

    private function getVipORNodalNUR($vip_nodal_codes, $vip_nodal_names, $week, $year)
    {
        $count_vip_codes = count($vip_nodal_codes);
        $sites = [];


        for ($i = 0; $i < $count_vip_codes; $i++) {
            $vip = [];
            $NUR2G = NUR2G::where("problem_site_code", $vip_nodal_codes[$i])->where("week", $week)->where("year", $year)->get();
            $NUR3G = NUR3G::where("problem_site_code", $vip_nodal_codes[$i])->where("week", $week)->where("year", $year)->get();
            $NUR4G = NUR4G::where("problem_site_code", $vip_nodal_codes[$i])->where("week", $week)->where("year", $year)->get();

            if (count($NUR2G) > 0) {
                $vip["site_name"] = $vip_nodal_names[$i];
                $vip["site_code"] = $vip_nodal_codes[$i];
                $vip["NUR_2G_count_tickets"] = $NUR2G->count();
                $vip["NUR_2G_sum_nur"] = number_format($NUR2G->sum("nur"), 2, '.', ',');
                $vip["NUR_2G_tickets"] = $NUR2G;
            }
            if (count($NUR3G) > 0) {
                $vip["site_name"] = $vip_nodal_names[$i];
                $vip["site_code"] = $vip_nodal_codes[$i];
                $vip["NUR_3G_count_tickets"] = $NUR3G->count();
                $vip["NUR_3G_sum_nur"] = number_format($NUR3G->sum("nur"), 2, '.', ',');
                $vip["NUR_3G_tickets"] = $NUR3G;
            }
            if (count($NUR4G) > 0) {
                $vip["site_name"] = $vip_nodal_names[$i];
                $vip["site_code"] = $vip_nodal_codes[$i];
                $vip["NUR_4G_count_tickets"] = $NUR4G->count();
                $vip["NUR_4G_sum_nur"] = number_format($NUR4G->sum("nur"), 2, '.', ',');
                $vip["NUR_4G_tickets"] = $NUR4G;
            }
            if (count($vip) > 0) {
                array_push($sites, $vip);
            }
        }
        return $sites;
    }

    public function cairoModificationWeeklyNUR($week,$year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $data = [

            "week" => $week,
            "year" => $year
        ];
        $validator = Validator::make($data, ["week" => ["required", 'integer', "between:1,52"], "year" => ['required', 'regex:/^2[0-9]{3}$/']]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        } else {
            $validated = $validator->validated();
            $total_week=$this->getWeekTechTickets($validated["week"],$validated['year']);
            $allTickets=$this->collectAllWeekTickets( $total_week['tickets_2G'], $total_week['tickets_3G'], $total_week['tickets_4G']);

            $weeklyStatestics=CairoWeeklyStatestics::cairoModificationStatestics($allTickets);

           
            return response()->json([
                "statestics"=>$weeklyStatestics['statestics'],
                "tickets"=>$allTickets->where("type", "Voluntary")->values(),
                "sites"=>$weeklyStatestics['impactedSites']
            ],200);

        
        }


    }

    public function cairoPowerWeeklyNUR($week,$year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $data = [

            "week" => $week,
            "year" => $year
        ];
        $validator = Validator::make($data, ["week" => ["required", 'integer', "between:1,52"], "year" => ['required', 'regex:/^2[0-9]{3}$/']]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        } else {
            $validated = $validator->validated();
            $total_week=$this->getWeekTechTickets($validated["week"],$validated['year']);
            $allTickets=$this->collectAllWeekTickets( $total_week['tickets_2G'], $total_week['tickets_3G'], $total_week['tickets_4G']);
            $weeklyStatestics=CairoWeeklyStatestics::cairoMainPowerStatestics($allTickets);

           
            return response()->json([
                "statestics"=>$weeklyStatestics['statestics'],
                "tickets"=>$allTickets->where("sub_system", "MAIN POWER")->values(),
                "sites"=>$weeklyStatestics['impactedSites']
            ],200);

        }
    }


    public function cairoMWweeklyNUR($week, $year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $data = [

            "week" => $week,
            "year" => $year
        ];
        $validator = Validator::make($data, ["week" => ["required", 'integer', "between:1,52"], "year" => ['required', 'regex:/^2[0-9]{3}$/']]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        } else {
            $validated = $validator->validated();
            $total_week=$this->getWeekTechTickets($validated["week"],$validated['year']);
            $allTickets=$this->collectAllWeekTickets( $total_week['tickets_2G'], $total_week['tickets_3G'], $total_week['tickets_4G']);
            $weeklyStatestics=CairoWeeklyStatestics::cairoMWStatestics($allTickets);

           
            return response()->json([
                "statestics"=>$weeklyStatestics['statestics'],
                "tickets"=>$allTickets->where("system", "transmission")->values(),
                "sites"=>$weeklyStatestics['impactedSites']
            ],200);

          
        }
    }



    public function cairoGenweeklyNUR($week, $year)
    { $this->authorize("viewAny",NUR2G::class);
        $data = [

            "week" => $week,
            "year" => $year
        ];
        $validator = Validator::make($data, ["week" => ["required", 'integer', "between:1,52"], "year" => ['required', 'regex:/^2[0-9]{3}$/']]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        } else {
            $validated = $validator->validated();
            $total_week=$this->getWeekTechTickets($validated["week"],$validated['year']);
            $allTickets=$this->collectAllWeekTickets( $total_week['tickets_2G'], $total_week['tickets_3G'], $total_week['tickets_4G']);

            $weeklyStatestics=CairoWeeklyStatestics::cairoGenStatestics($allTickets);

            return response()->json([
                "statestics"=> $weeklyStatestics['statestics'],
                "tickets"=>$allTickets->where("sub_system", "GENERATOR")->values(),
                "sites"=>$weeklyStatestics['impactedSites']
            ],200);
        }

    }

   
    public function cairoNodeBWeeklyNUR($week,$year)
    {
        $this->authorize("viewAny",NUR2G::class);
        $data = [

            "week" => $week,
            "year" => $year
        ];
        $validator = Validator::make($data, ["week" => ["required", 'integer', "between:1,52"], "year" => ['required', 'regex:/^2[0-9]{3}$/']]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        } else {
            $validated = $validator->validated();
            $total_week=$this->getWeekTechTickets($validated["week"],$validated['year']);
            $allTickets=$this->collectAllWeekTickets( $total_week['tickets_2G'], $total_week['tickets_3G'], $total_week['tickets_4G']);

            $weeklyStatestics=CairoWeeklyStatestics::cairoNodeBStatestics($allTickets);

            return response()->json([
                "statestics"=> $weeklyStatestics['statestics'],
                "tickets"=>$weeklyStatestics['tickets'],
                "sites"=>$weeklyStatestics['impactedSites']
            ],200);

       
        }

    }


    
}
