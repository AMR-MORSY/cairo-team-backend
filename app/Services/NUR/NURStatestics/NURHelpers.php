<?php

namespace App\Services\NUR\NURStatestics;

use Maatwebsite\Excel\Concerns\ToArray;

class NURHelpers
{


    // private $NUR, $network_2G_cells, $network_3G_cells, $network_4G_cells;
    // public function __construct($NUR, $network_2G_cells, $network_3G_cells, $network_4G_cells)
    // {
    //     $this->NUR = $NUR;
    //     $this->network_2G_cells = $network_2G_cells;
    //     $this->network_3G_cells = $network_3G_cells;
    //     $this->network_4G_cells = $network_4G_cells;
    // }

    public static function zonesTotalNumTickets($zones,$NUR_tickets)
    {
        $oz = [];
        foreach ($zones as $zone) {

            $oz[$zone] = $NUR_tickets->where("oz", $zone)->count();
        }
        return $oz;
    }


    public static function zonesNUR($zones, $period,$NUR_tickets)
    {
        $oz = [];
        foreach ($zones as $zone) {
            $data =$NUR_tickets->where("oz", $zone)->sum($period);
            $oz[$zone] = number_format($data, 2, '.', ',');
        }
        return $oz;
    }

    public static function zonesSubsystemNUR($zones,$allTickets)
    {
        $oz = [];
        $subs = [];
        foreach ($zones as $zone) {
            $subsystems = $allTickets->groupBy("sub_system")->keys();
            foreach ($subsystems as $system) {
                $zoneSubSysTickets = $allTickets->whereStrict("oz", $zone)->whereStrict('sub_system', $system);
                if(count($zoneSubSysTickets)>0)
                {
                    $NUR_c=$zoneSubSysTickets->sum('nur_c');
                    $subs[$system] = number_format($NUR_c, 2, '.', ',');
                }
                // $NUR3G = $this->NUR->whereStrict("oz", $zone)->whereStrict("technology", "3G")->whereStrict('sub_system', $system);
                // $NUR4G = $this->NUR->whereStrict("oz", $zone)->whereStrict("technology", "4G")->whereStrict('sub_system', $system);
             
                // $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);
                // $subs[$system] = number_format($NUR_c, 2, '.', ',');
            }
            // $filtered = array_filter($subs, function ($value, $key) {
            //     return $value != 0;
            // }, ARRAY_FILTER_USE_BOTH);



            // $oz[$zone] = $filtered;
            $oz[$zone] = $subs;
        }



        return $oz;
    }

    public static function zonesSubsystemCountTickts($zones,$allTickets)
    {
        $oz = [];
        $subs = [];
        foreach ($zones as $zone) {
            $subsystems = $allTickets->groupBy("sub_system")->keys();
            foreach ($subsystems as $system) {
                $zoneSubSysTickets=$allTickets->where("oz", $zone)->where('sub_system', $system);
                if(count($zoneSubSysTickets)>0)
                {
                    $subs[$system] = $zoneSubSysTickets->count();
                }
                // $subs[$system] = $allTickets->where("oz", $zone)->where('sub_system', $system)->count();
            }
            // $filtered = array_filter($subs, function ($value, $key) {
            //     return $value != 0;
            // }, ARRAY_FILTER_USE_BOTH);

            // $oz[$zone] = $filtered;
            $oz[$zone] = $subs;
        }
        return $oz;
    }

    public static function zonesRepeatedSites($zones,$allTickets)
    {
        $oz = [];


        foreach ($zones as $zone) {
            $subs = [];
            // $siteCodes = $this->NUR->where("oz", $zone)->groupBy("problem_site_code");
            $siteCodes = $allTickets->where("oz", $zone)->where('technology',"3G")->groupBy("problem_site_code");
            foreach ($siteCodes as $key => $codes) {



                $site["siteName"] = $codes->first()->problem_site_name;
                $site["siteCode"] = $codes->first()->problem_site_code;
                $site["count"] = $codes->count();
                array_push($subs, $site);
            }

            $sub = collect($subs);

            $sub = $sub->sortByDesc("count");
            $sub = $sub->take(7);
            $oz[$zone] = $sub->values();
        }
        return $oz;
    }

    public static function zonesTopSitesNUR($zones, $allTickets)
    {
        $oz = [];



        foreach ($zones as $zone) {
            $subs = [];
            $site = [];
            $siteCodes = $allTickets->where("oz", $zone)->groupBy("problem_site_code");


            foreach ($siteCodes as $key => $codes) {
                $site["siteName"] = $codes->first()->problem_site_name;
                $site["siteCode"] = $codes->first()->problem_site_code;
                // $NUR2G = $codes->whereStrict("technology", "2G")->values();
                // $NUR3G = $codes->whereStrict("technology", "3G")->values();
                // $NUR4G = $codes->whereStrict("technology", "4G")->values();
                // $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);
                $NUR_c=$codes->sum('nur_c');

                $site["NUR"] = number_format($NUR_c, 2, '.', ',');
                array_push($subs, $site);
            }

            $sub = collect($subs);

            $sub = $sub->sortByDesc("NUR");
            $sub = $sub->take(7);
            $oz[$zone] = $sub->values();
        }
        return $oz;
    }


    // public function zonesAccessCountTickts($zones)
    // {
    //     $oz = [];
    //     $sub = [];
    //     foreach ($zones as $zone) {


    //         $sub['access'] = $this->NUR->where("oz", $zone)->where('access', 1)->count();
    //         $oz[$zone] = $sub;
    //     }
    //     return $oz;
    // }

    // public function zonesAccessNUR($zones, $period)
    // {
    //     $oz = [];
    //     $sub = [];
    //     foreach ($zones as $zone) {
    //         $sub["access"] = number_format($this->NUR->where("oz", $zone)->where('access', 1)->sum($period), 2, '.', ',');
    //         $oz[$zone] = $sub;
    //     }
    //     return $oz;
    // }

    public static function zonesResponseWithAccess($zones,$allTickets)
    {
        $oz = [];
        $subs = [];
        foreach ($zones as $zone) {

            $zoneAccessTickets = $allTickets->Where("oz", $zone)->where("access", 1)->where('Dur_min', ">", 240);
            // $NUR3G = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', ">", 240)->where("technology", "3G");
            // $NUR4G = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', ">", 240)->where("technology", "4G");

            // $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);
            $NUR_c=$zoneAccessTickets->sum('nur_c');
           

            $subs['exceedSLA'] = number_format($NUR_c, 2, '.', ',');
            // $subs['exceedSLA_count_tickets'] = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', ">", 240)->count();
            $subs['exceedSLA_count_tickets'] = $zoneAccessTickets->count();
            $zoneWithinSLATickets = $allTickets->Where("oz", $zone)->where("access", 1)->where('Dur_min', "<=", 240);
            // $NUR2G = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', "<=", 240)->where("technology", "2G");
            // $NUR3G = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', "<=", 240)->where("technology", "3G");
            // $NUR4G = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', "<=", 240)->where("technology", "4G");
          
            // $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);
            $NUR_c= $zoneWithinSLATickets->sum('nur_c');
            $subs['withinSLA'] = number_format($NUR_c, 2, ".", ',');
            // $subs['withinSLA_count_tickets'] = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', "<=", 240)->count();
            $subs['withinSLA_count_tickets'] = $zoneWithinSLATickets->count();

            $oz[$zone] = $subs;
        }

        return $oz;
    }
    public static function zonesResponseWithoutAccess($zones, $allTickets)
    {
        $oz = [];
        $subs = [];
        foreach ($zones as $zone) {
            $zoneExceedTickets= $allTickets->Where("oz", $zone)->where("access", 0)->where('Dur_min', ">", 240);

            // $NUR2G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', ">", 240)->where("technology", "2G");
            // $NUR3G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', ">", 240)->where("technology", "3G");
            // $NUR4G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', ">", 240)->where("technology", "4G");
           
            // $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);
            $NUR_c=$zoneExceedTickets->sum('nur_c');
            $subs['exceedSLA'] = number_format($NUR_c, 2, '.', ',');
            // $subs['exceedSLA_count_tickets'] = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', ">", 240)->count();
            $subs['exceedSLA_count_tickets'] = $zoneExceedTickets->count();
            $zoneWithINTickets=$allTickets->Where("oz", $zone)->where("access", 0)->where('Dur_min', "<=", 240);
            // $NUR2G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', "<=", 240)->where("technology", "2G");
            // $NUR3G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', "<=", 240)->where("technology", "3G");
            // $NUR4G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', "<=", 240)->where("technology", "4G");
          
            // $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);
            $NUR_c=$zoneWithINTickets->sum('nur_c');
            $subs['withinSLA'] = number_format($NUR_c, 2, ".", ',');
            // $subs['withinSLA_count_tickets'] = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', "<=", 240)->count();
            $subs['withinSLA_count_tickets'] = $zoneWithINTickets->count();


            $oz[$zone] = $subs;
        }

        return $oz;
    }

    // private function calculateCombinedNUR($NUR2G, $NUR3G, $NUR4G)
    // {
    //     if (count($NUR2G) > 0) {
    //         $sum_NUR_2G = number_format($NUR2G->sum("nur"), 2, '.', ',');
    //     } else {
    //         $sum_NUR_2G = 0;
    //     }
    //     if (count($NUR3G) > 0) {
    //         $sum_NUR_3G = number_format($NUR3G->sum("nur"), 2, '.', ',');
    //     } else {
    //         $sum_NUR_3G = 0;
    //     }
    //     if (count($NUR4G) > 0) {
    //         $sum_NUR_4G = number_format($NUR4G->sum("nur"), 2, '.', ',');
    //     } else {
    //         $sum_NUR_4G = 0;
    //     }
    //     $NUR_c = (($sum_NUR_2G * $this->network_2G_cells) + ($sum_NUR_3G * $this->network_3G_cells) + ($sum_NUR_4G * $this->network_4G_cells)) / ($this->network_2G_cells + $this->network_3G_cells + $this->network_4G_cells);
    //     return $NUR_c;
    // }



    public static function zonesGeneratorStatestics($zones, $allTickets)
    {
        $oz = [];
        $subs = [];

        foreach ($zones as $zone) {
            // // $VF = [];
            // $VFTickets=[];
            // // $ET = [];
            // $ETTickets=[];
            // $oRG = [];
            // $rented = [];
            // $WETickets=[];
            // $genTickets =$allTickets->where("oz", $zone)->where("sub_system", "GENERATOR");
            // $ORG = $this->NUR->where("oz", $zone)->where("gen_owner", "orange");
            $ORG = $allTickets->where("oz", $zone)->where("gen_owner", "orange");
            // $Rented = $this->NUR->where("oz", $zone)->where("gen_owner", "rented");
            $Rented = $allTickets->where("oz", $zone)->where("gen_owner", "rented");
            // $shared=$allTickets->where("oz", $zone)->where("gen_owner", "shared");
            // $allTickets = $allTickets->toArray();
            // foreach ($shared as $ticket) {
            //     $ticketArray = explode(" ", $ticket['solution']);

            //     foreach ($ticketArray as $filt) {
            //         if ($filt == "Vf") {

            //             array_push( $VFTickets, $ticket);
            //             break;
            //         }
            //         if ($filt == "Et") {
            //             array_push( $ETTickets, $ticket);
            //             break;
            //         }
            //     }
               
            // }
            // $VFTickets = collect($VFTickets);
            $VFTickets = $allTickets->where("oz", $zone)->where("gen_owner", "shared")->where("Action_OGS_responsible","ND(VF)");
            // $VFNUR2G = $VFTickets->where("technology", "2G");
            // $VFNUR3G = $VFTickets->where("technology", "3G");
            // $VFNUR4G = $VFTickets->where("technology", "4G");
            // $NUR_c = $this->calculateCombinedNUR($VFNUR2G, $VFNUR3G, $VFNUR4G);
            $NUR_c = $VFTickets->sum('nur_c');

            $VF["nur"] = number_format($NUR_c, 2, '.', ',');
            $VF['count'] = $VFTickets->count();
            $VF['tickets']=$VFTickets;

            // $ETTickets = collect($ETTickets);
            $ETTickets = $allTickets->where("oz", $zone)->where("gen_owner", "shared")->where("Action_OGS_responsible","ND(Etisalat)");
            // $ETNUR2G = $ETTickets->where("technology", "2G");
            // $ETNUR3G = $ETTickets->where("technology", "3G");
            // $ETNUR4G = $ETTickets->where("technology", "4G");

            // $NUR_c = $this->calculateCombinedNUR($ETNUR2G, $ETNUR3G, $ETNUR4G);
            $NUR_c =  $ETTickets->sum('nur_c');
            $ET['nur'] = number_format($NUR_c, 2, '.', ',');
            $ET["count"] = $ETTickets->count();
            $ET['tickets']=$ETTickets;

            $WETickets = $allTickets->where("oz", $zone)->where("gen_owner", "shared")->where("Action_OGS_responsible","ND(WE)");
            // $ETNUR2G = $ETTickets->where("technology", "2G");
            // $ETNUR3G = $ETTickets->where("technology", "3G");
            // $ETNUR4G = $ETTickets->where("technology", "4G");

            // $NUR_c = $this->calculateCombinedNUR($ETNUR2G, $ETNUR3G, $ETNUR4G);
            $NUR_c =  $WETickets->sum('nur_c');
            $WE['nur'] = number_format($NUR_c, 2, '.', ',');
            $WE["count"] = $WETickets->count();
            $WE['tickets']=$WETickets;


            // $ORGNUR2G = $ORG->where("technology", "2G");
            // $ORGNUR3G = $ORG->where("technology", "3G");
            // $ORGNUR4G = $ORG->where("technology", "4G");

            // $NUR_c= $this->calculateCombinedNUR($ORGNUR2G, $ORGNUR3G, $ORGNUR4G);
            $NUR_c= $ORG->sum('nur_c');
          
            $oRG['count'] = $ORG->count();
            $oRG['nur'] = number_format($NUR_c, 2, '.', ',');
            // $oRG["tickets"]=$ORG->values();
            $oRG["tickets"]=$ORG;

            // $rentedNUR2G = $Rented->where("technology", "2G");
            // $rentedNUR3G = $Rented->where("technology", "3G");
            // $rentedNUR4G = $Rented->where("technology", "4G");

            // $NUR_c= $this->calculateCombinedNUR($rentedNUR2G, $rentedNUR3G, $rentedNUR4G);
            $NUR_c= $Rented->sum('nur_c');

            $rented['count'] = $Rented->count();
            $rented['nur'] = number_format(  $NUR_c, 2, '.', ',');
            // $rented["tickets"]=$Rented->values();
            $rented["tickets"]=$Rented;
            $subs['VF'] = $VF;
            $subs['ET'] = $ET;
            $subs['ORG'] = $oRG;
            $subs['Rented'] = $rented;
            $subs['WE'] = $WE;

            $oz[$zone] = $subs;
        }
        return $oz;
    }

    // public function zonesAverageTicketsDur($zones)

    // {
    //     $oz = [];

    //     foreach ($zones as $zone) {
    //         $accessAverageDurations = $this->NUR->where("oz", $zone)->where('access', 1)->avg('Dur_min');
    //         $accessAverageDurations = number_format($accessAverageDurations / 60, 2, '.', ',');
    //         $withoutAccessAverageDurations = $this->NUR->where("oz", $zone)->where('access', 0)->avg('Dur_min');
    //         $withoutAccessAverageDurations = number_format($withoutAccessAverageDurations / 60, 2, '.', ',');

    //         // $averageTicketsDur=$durations->avg();

    //         $oz[$zone]['access'] =  $accessAverageDurations;
    //         $oz[$zone]['withoutAccess'] =  $withoutAccessAverageDurations;
    //     }
    //     return $oz;
    // }
}
