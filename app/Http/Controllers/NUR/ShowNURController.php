<?php

namespace App\Http\Controllers\NUR;

use App\Models\NUR\NUR2G;
use App\Models\NUR\NUR3G;
use App\Models\NUR\NUR4G;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sites\Site;
use Illuminate\Support\Facades\Validator;
use App\Services\NUR\NURStatestics\WeeklyStatestics;
use App\Services\NUR\NURStatestics\YearlyStatestics;
use stdClass;

use function PHPUnit\Framework\isEmpty;

class ShowNURController extends Controller
{
    public function __construct()
    {
        $this->middleware(["role:admin|super-admin"]);
    }


    public function show_nur($week, $year)
    {
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

                ], 404);
            } else {
                return response()->json([

                    "NUR" => $weeklyNUR,


                ], 200);
            }
        }
    }

    public function cairoYearlyNUR_C($year)
    {
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
            ],404);
           
          
        } else {
           
            $statestics = new YearlyStatestics($total_year_tickets_2G, $total_year_tickets_3G, $total_year_tickets_4G,$year);
            $NUR_C_yearly=$statestics->cairoNUR_C();
            return response()->json([
                "NUR_C_yearly"=>$NUR_C_yearly
            ],200);
          
           
        }
    }
    public function cairoMWYearlyNUR($year)
    {
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
            ],404);
           
          
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
            ],404);
           
          
        } else {
           
            $statestics = new YearlyStatestics($total_year_tickets_2G, $total_year_tickets_3G, $total_year_tickets_4G,$year);
            $NUR_Gen_yearly=$statestics->cairoGenNUR();
            return response()->json([
                "NUR_C_yearly"=>$NUR_Gen_yearly
            ],200);
          
           
        }

    }
    private function getWeeklyNUR($week, $year)
    {
        $total_week_tickets_2G = NUR2G::where('year', $year)->where('week', $week)->get();
       
        $total_week_tickets_3G = NUR3G::where('year', $year)->where('week', $week)->get();
      
        $total_week_tickets_4G = NUR4G::where('year', $year)->where('week', $week)->get();
        
        $errors = [];
        if (count($total_week_tickets_2G) <= 0) {
            array_push($errors, "2G NUR does not exist");
        }
        if (count($total_week_tickets_3G) <= 0) {
            array_push($errors, "3G NUR does not exist");
        }
        if (count($total_week_tickets_4G) <= 0) {
            array_push($errors, "4G NUR does not exist");
        }
        if (count($errors) > 0) {
            $notFound["error"] = true;
            $notFound["errors"] = $errors;
            return $notFound;
        } else {
            $network_2G_cells= $total_week_tickets_2G->first()->network_cells;
            $network_3G_cells= $total_week_tickets_3G ->first()->network_cells;
            $network_4G_cells=$total_week_tickets_4G ->first()->network_cells;
            $statestics = new WeeklyStatestics($total_week_tickets_2G, $total_week_tickets_3G, $total_week_tickets_4G,$network_2G_cells,$network_3G_cells,$network_4G_cells);
            $NUR['NUR2G'] = $statestics->NUR2GStatestics();
            $NUR['NUR3G'] = $statestics->NUR3GStatestics();
            $NUR['NUR4G'] = $statestics->NUR4GStatestics();
            $NUR["zonesSubsystem"]=$statestics->zonesSubsystemNUR();
            $NUR["zonesSubsystemCountTickts"]=$statestics->zonesSubsystemCountTickts();
            $NUR["zonesResponseWithAccess"]=$statestics->zonesResponseWithAccess();
            $NUR["zonesResponseWithoutAccess"]=$statestics->zonesResponseWithoutAccess();
            $NUR["zonesGeneratorStatestics"]=$statestics->zonesGeneratorStatestics();
            $NUR["topRepeated"] = $statestics->zonesTopRepeated();
            $NUR["topNUR"] = $statestics->zonesTopNUR();
            $NUR['combined'] = $statestics->combinedNUR();
            return $NUR;
        }
    }

    public function siteNUR(Request $request)
    {
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

    public function cairoPowerWeeklyNUR($week,$year)
    {
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

            $NUR2G_tickets = NUR2G::where("week", $validated["week"])->where("year", $validated['year'])->where("sub_system", "main power")->get();
            $NUR3G_tickets = NUR3G::where("week", $validated["week"])->where("year", $validated['year'])->where("sub_system", "main power")->get();
            $NUR4G_tickets = NUR4G::where("week", $validated["week"])->where("year", $validated['year'])->where("sub_system", "main power")->get();

            $network_2g_cells = $NUR2G_tickets->whereStrict("technology", "2G")->first()->network_cells;
            $network_3g_cells = $NUR3G_tickets->whereStrict("technology", "3G")->first()->network_cells;
            $network_4g_cells = $NUR4G_tickets->whereStrict("technology", "4G")->first()->network_cells;

            $statestics=$this->cairoMainPowerWeeklyStatestics($NUR2G_tickets,$NUR3G_tickets,$NUR4G_tickets,$network_2g_cells,$network_3g_cells,$network_4g_cells);
              $tickets = $this->formArrayOfTickets($NUR2G_tickets, $NUR3G_tickets, $NUR4G_tickets);
            $sites = $this->getImpactedSites($tickets, $network_2g_cells,  $network_3g_cells,  $network_4g_cells);
            return response()->json([
                "statestics"=>$statestics,
                "tickets"=>$tickets,
                "sites"=>$sites
            ],200);

        }
    }

    private function cairoMainPowerWeeklyStatestics($NUR2G_tickets, $NUR3G_tickets, $NUR4G_tickets, $network_2g_cells,  $network_3g_cells,  $network_4g_cells)
    {
        $statestics = [];
        $NUR_2G_sum = $NUR2G_tickets->sum("nur");
        $NUR_3G_sum = $NUR3G_tickets->sum("nur");
        $NUR_4G_sum = $NUR4G_tickets->sum("nur");
        $combined = (($NUR_2G_sum * $network_2g_cells) + ($NUR_3G_sum * $network_3g_cells) + ($NUR_4G_sum * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_access = $NUR2G_tickets->where("access", 1)->sum("nur");
        $NUR_3G_access = $NUR3G_tickets->where("access", 1)->sum("nur");
        $NUR_4G_access = $NUR4G_tickets->where("access", 1)->sum("nur");
        $combined = (($NUR_2G_access * $network_2g_cells) + ($NUR_3G_access * $network_3g_cells) +($NUR_4G_access * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_access_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_without_access = $NUR2G_tickets->where("access", 0)->sum("nur");
        $NUR_3G_without_access = $NUR3G_tickets->where("access", 0)->sum("nur");
        $NUR_4G_without_access = $NUR4G_tickets->where("access", 0)->sum("nur");
        $combined = ($NUR_2G_without_access * $network_2g_cells + $NUR_3G_without_access * $network_3g_cells + $NUR_4G_without_access * $network_4g_cells) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_without_access_combined = number_format($combined, 2, '.', ',');

        $statestics["NUR_access_c"] = $NUR_access_combined;
        $statestics["NUR_without_access_c"] = $NUR_without_access_combined;
        $statestics["NUR_combined"] = $NUR_combined;

        return $statestics;


    }

    public function cairoMWweeklyNUR($week, $year)
    {
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

            $NUR2G_tickets = NUR2G::where("week", $validated["week"])->where("year", $validated['year'])->where("system", "transmission")->get();
            $NUR3G_tickets = NUR3G::where("week", $validated["week"])->where("year", $validated['year'])->where("system", "transmission")->get();
            $NUR4G_tickets = NUR4G::where("week", $validated["week"])->where("year", $validated['year'])->where("system", "transmission")->get();

            $network_2g_cells = $NUR2G_tickets->whereStrict("technology", "2G")->first()->network_cells;
            $network_3g_cells = $NUR3G_tickets->whereStrict("technology", "3G")->first()->network_cells;
            $network_4g_cells = $NUR4G_tickets->whereStrict("technology", "4G")->first()->network_cells;

            $statestics = $this->cairoMWWeeklyStatestics($NUR2G_tickets, $NUR3G_tickets, $NUR4G_tickets, $network_2g_cells,  $network_3g_cells,  $network_4g_cells);

            $tickets = $this->formArrayOfTickets($NUR2G_tickets, $NUR3G_tickets, $NUR4G_tickets);
            $sites = $this->getImpactedSites($tickets, $network_2g_cells,  $network_3g_cells,  $network_4g_cells);

            return response([
                "sites" => $sites,
                "statestics" => $statestics,
                "tickets" => $tickets
            ], 200);
        }
    }
    public function cairoGenweeklyNUR($week, $year)
    {
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

            $NUR2G_tickets = NUR2G::where("week", $validated["week"])->where("year", $validated['year'])->where("sub_system", "GENERATOR")->get();
            $NUR3G_tickets = NUR3G::where("week", $validated["week"])->where("year", $validated['year'])->where("sub_system", "GENERATOR")->get();
            $NUR4G_tickets = NUR4G::where("week", $validated["week"])->where("year", $validated['year'])->where("sub_system", "GENERATOR")->get();
            $network_2g_cells = $NUR2G_tickets->whereStrict("technology", "2G")->first()->network_cells;
            $network_3g_cells = $NUR3G_tickets->whereStrict("technology", "3G")->first()->network_cells;
            $network_4g_cells = $NUR4G_tickets->whereStrict("technology", "4G")->first()->network_cells;
            $statestics = $this->cairoGenWeeklyStatestics($NUR2G_tickets, $NUR3G_tickets, $NUR4G_tickets, $network_2g_cells,  $network_3g_cells,  $network_4g_cells);
            $tickets=$this->formArrayOfTickets($NUR2G_tickets,$NUR3G_tickets,$NUR4G_tickets);
            $sites=$this->getImpactedSites($tickets,$network_2g_cells,$network_3g_cells,$network_4g_cells);
            return response()->json([
                "statestics"=>$statestics,
                "tickets"=>$tickets,
                "sites"=>$sites
            ],200);
        }

    }
    private function getVFandETGenTickets($NUR_tickets)
    {
        $NUR_VF_tickets=[];
        $NUR_ET_tickets=[];
        foreach($NUR_tickets as $ticket)
        {
            $ticketArray = explode(" ", $ticket['solution']);

            foreach ($ticketArray as $filt) {
                if ($filt == "Vf") {

                    array_push($NUR_VF_tickets, $ticket);
                    break;
                }
                if ($filt == "Et") {
                    array_push(   $NUR_ET_tickets, $ticket);
                    break;
                }
            }


        }

        return [
            "VF"=>$NUR_VF_tickets,
            "ET"=>$NUR_ET_tickets
        ];

    }
    private function cairoGenWeeklyStatestics($NUR2G_tickets, $NUR3G_tickets, $NUR4G_tickets, $network_2g_cells,  $network_3g_cells,  $network_4g_cells)
    {
        $statestics = [];
        $NUR_2G_sum = $NUR2G_tickets->sum("nur");
        $NUR_3G_sum = $NUR3G_tickets->sum("nur");
        $NUR_4G_sum = $NUR4G_tickets->sum("nur");
        $combined = (($NUR_2G_sum * $network_2g_cells) + ($NUR_3G_sum * $network_3g_cells) + ($NUR_4G_sum * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_Org = $NUR2G_tickets->where("gen_owner", "orange")->sum("nur");
        $NUR_3G_Org = $NUR3G_tickets->where("gen_owner", "orange")->sum("nur");
        $NUR_4G_Org = $NUR4G_tickets->where("gen_owner", "orange")->sum("nur");
        $combined = (($NUR_2G_Org * $network_2g_cells) + ($NUR_3G_Org * $network_3g_cells) +($NUR_4G_Org * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_Org_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_Rented = $NUR2G_tickets->where("gen_owner", "rented")->sum("nur");
        $NUR_3G_Rented = $NUR3G_tickets->where("gen_owner", "rented")->sum("nur");
        $NUR_4G_Rented = $NUR4G_tickets->where("gen_owner", "rented")->sum("nur");
        $combined = (($NUR_2G_Rented * $network_2g_cells) + ($NUR_3G_Rented * $network_3g_cells) +($NUR_4G_Rented * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_Rented_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_ET_tickets=$this->getVFandETGenTickets($NUR2G_tickets)["ET"];
        $NUR_3G_ET_tickets=$this->getVFandETGenTickets($NUR3G_tickets)["ET"];
        $NUR_4G_ET_tickets=$this->getVFandETGenTickets($NUR4G_tickets)["ET"];
        $NUR_2G_VF_tickets=$this->getVFandETGenTickets($NUR2G_tickets)["VF"];
        $NUR_3G_VF_tickets=$this->getVFandETGenTickets($NUR3G_tickets)["VF"];;
        $NUR_4G_VF_tickets=$this->getVFandETGenTickets($NUR4G_tickets)["VF"];;
      
      

        $NUR_2G_ET_NUR = collect( $NUR_2G_ET_tickets)->sum("nur");
        $NUR_3G_ET_NUR =  collect( $NUR_3G_ET_tickets)->sum("nur");
        $NUR_4G_ET_NUR =  collect( $NUR_4G_ET_tickets)->sum("nur");
        $combined = (($NUR_2G_ET_NUR * $network_2g_cells) + ($NUR_3G_ET_NUR * $network_3g_cells) +($NUR_4G_ET_NUR * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_ET_combined = number_format($combined, 2, '.', ',');



        $NUR_2G_VF_NUR = collect( $NUR_2G_VF_tickets)->sum("nur");
        $NUR_3G_VF_NUR =  collect( $NUR_3G_VF_tickets)->sum("nur");
        $NUR_4G_VF_NUR =  collect( $NUR_4G_VF_tickets)->sum("nur");
        $combined = (($NUR_2G_VF_NUR * $network_2g_cells) + ($NUR_3G_VF_NUR * $network_3g_cells) +($NUR_4G_VF_NUR * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_VF_combined = number_format($combined, 2, '.', ',');


        $statestics["NUR_combined"] = $NUR_combined;
        $statestics["NUR_Org_combined"] = $NUR_Org_combined;
        $statestics["NUR_Rented_combined"] = $NUR_Rented_combined;
        $statestics["NUR_ET_combined"] = $NUR_ET_combined;
        $statestics["NUR_VF_combined"] = $NUR_VF_combined;

        return $statestics;

    }
    private function cairoMWWeeklyStatestics($NUR2G_tickets, $NUR3G_tickets, $NUR4G_tickets, $network_2g_cells,  $network_3g_cells,  $network_4g_cells)
    {
        $statestics = [];
        $NUR_2G_sum = $NUR2G_tickets->sum("nur");
        $NUR_3G_sum = $NUR3G_tickets->sum("nur");
        $NUR_4G_sum = $NUR4G_tickets->sum("nur");
        $combined = (($NUR_2G_sum * $network_2g_cells) + ($NUR_3G_sum * $network_3g_cells) + ($NUR_4G_sum * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_voluntary = $NUR2G_tickets->where("type", "Voluntary")->sum("nur");
        $NUR_3G_voluntary = $NUR3G_tickets->where("type", "Voluntary")->sum("nur");
        $NUR_4G_voluntary = $NUR4G_tickets->where("type", "Voluntary")->sum("nur");
        $combined = (($NUR_2G_voluntary * $network_2g_cells) + ($NUR_3G_voluntary * $network_3g_cells) +($NUR_4G_voluntary * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_voluntary_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_HDSL = $NUR2G_tickets->whereStrict("sub_system","HDSL")->sum("nur");
        $NUR_3G_HDSL = $NUR3G_tickets->whereStrict("sub_system","HDSL")->sum("nur");
        $NUR_4G_HDSL = $NUR4G_tickets->whereStrict("sub_system","HDSL")->sum("nur");
        $combined = (($NUR_2G_HDSL * $network_2g_cells) + ($NUR_3G_HDSL * $network_3g_cells) + ($NUR_4G_HDSL * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_HDSL_combined = number_format($combined, 2, '.', ',');


        $NUR_2G_involuntary = $NUR2G_tickets->where("type", "Involuntary")->sum("nur");
        $NUR_3G_involuntary = $NUR3G_tickets->where("type", "Involuntary")->sum("nur");
        $NUR_4G_involuntary = $NUR4G_tickets->where("type", "Involuntary")->sum("nur");
        $combined = ($NUR_2G_involuntary * $network_2g_cells + $NUR_3G_involuntary * $network_3g_cells + $NUR_4G_involuntary * $network_4g_cells) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_involuntary_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_access = $NUR2G_tickets->where("access", 1)->sum("nur");
        $NUR_3G_access = $NUR3G_tickets->where("access", 1)->sum("nur");
        $NUR_4G_access = $NUR4G_tickets->where("access", 1)->sum("nur");
        $combined = (($NUR_2G_access * $network_2g_cells) + ($NUR_3G_access * $network_3g_cells) +($NUR_4G_access * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_access_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_without_access = $NUR2G_tickets->where("access", 0)->sum("nur");
        $NUR_3G_without_access = $NUR3G_tickets->where("access", 0)->sum("nur");
        $NUR_4G_without_access = $NUR4G_tickets->where("access", 0)->sum("nur");
        $combined = ($NUR_2G_without_access * $network_2g_cells + $NUR_3G_without_access * $network_3g_cells + $NUR_4G_without_access * $network_4g_cells) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_without_access_combined = number_format($combined, 2, '.', ',');

        $statestics["NUR_combined"] = $NUR_combined;
        $statestics["NUR_voluntary_c"] = $NUR_voluntary_combined;
        $statestics["NUR_involuntary_c"] = $NUR_involuntary_combined;
        $statestics["NUR_access_c"] = $NUR_access_combined;
        $statestics["NUR_without_access_c"] = $NUR_without_access_combined;
        $statestics["NUR_HDSL_c"] = $NUR_HDSL_combined;


        return $statestics;
    }
    private function formArrayOfTickets($tickets2G, $tickets3G, $tickets4G)
    {
        $tickets = [];
        foreach ($tickets2G as $ticket) {
            array_push($tickets, $ticket);
        }
        foreach ($tickets3G as $ticket) {
            array_push($tickets, $ticket);
        }
        foreach ($tickets4G as $ticket) {
            array_push($tickets, $ticket);
        }
        return $tickets;
    }
    private function getImpactedSites($tickets, $network_2g_cells,  $network_3g_cells,  $network_4g_cells)
    {
        $tickets = collect($tickets);
        $site_codes = $tickets->groupBy("problem_site_code");
        $sites = [];
        foreach ($site_codes as $key => $NUR_tickets) {
            $site = [];
            $NUR_2G = $NUR_tickets->whereStrict("technology", "2G")->values();


            $site_data = Site::where("site_code", $key)->first();
            if (isset($site_data) && !empty($site_data)) {
                $site_name = $site_data->site_name;
                $site_zone = $site_data->oz;
            } else {
                $site_name = "";
                $site_zone = "";
            }

            $NUR_2G_sum = $NUR_2G->sum("nur");
            $NUR_2G_sum = number_format($NUR_2G_sum, 2, '.', ',');
            $NUR_3G = $NUR_tickets->whereStrict("technology", "3G")->values();

            $NUR_3G_sum = $NUR_3G->sum("nur");
            $NUR_3G_sum = number_format($NUR_3G_sum, 2, '.', ',');
            $NUR_4G = $NUR_tickets->whereStrict("technology", "4G")->values();


            $NUR_4G_sum = $NUR_4G->sum("nur");
            $NUR_4G_sum = number_format($NUR_4G_sum, 2, '.', ',');
            $site_information= new stdClass();
            $site_information->NUR_2G_sum=$NUR_2G_sum;
            $site_information->NUR_3G_sum=$NUR_3G_sum;
            $site_information->NUR_4G_sum=$NUR_4G_sum;
            $site_information->site_name=$site_name;
            $site_information->site_code=$key;
            $site_information->oz=$site_zone;
          
            $combined = (($NUR_2G_sum * $network_2g_cells) + ($NUR_3G_sum * $network_3g_cells) + ($NUR_4G_sum * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
          
            $site_information->NUR_C=number_format($combined, 2, '.', ',');
            $site["site_data"]=$site_information;
            array_push($sites, $site);
        }
        return $sites = collect($sites);
    }
}
