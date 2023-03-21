<?php

namespace App\Services\NUR\NURStatestics;

use Maatwebsite\Excel\Concerns\ToArray;

class NURHelpers
{


    private $NUR, $network_2G_cells, $network_3G_cells, $network_4G_cells;
    public function __construct($NUR, $network_2G_cells, $network_3G_cells, $network_4G_cells)
    {
        $this->NUR = $NUR;
        $this->network_2G_cells = $network_2G_cells;
        $this->network_3G_cells = $network_3G_cells;
        $this->network_4G_cells = $network_4G_cells;
    }

    public function zonesTotalNumTickets($zones)
    {
        $oz = [];
        foreach ($zones as $zone) {

            $oz[$zone] = $this->NUR->where("oz", $zone)->count();
        }
        return $oz;
    }


    public  function zonesNUR($zones, $period)
    {
        $oz = [];
        foreach ($zones as $zone) {
            $data = $this->NUR->where("oz", $zone)->sum($period);
            $oz[$zone] = number_format($data, 2, '.', ',');
        }
        return $oz;
    }

    public function zonesSubsystemNUR($zones, $period)
    {
        $oz = [];
        $subs = [];
        foreach ($zones as $zone) {
            $subsystems = $this->NUR->groupBy("sub_system")->keys();
            foreach ($subsystems as $system) {
                $NUR2G = $this->NUR->whereStrict("oz", $zone)->whereStrict("technology", "2G")->whereStrict('sub_system', $system);
                $NUR3G = $this->NUR->whereStrict("oz", $zone)->whereStrict("technology", "3G")->whereStrict('sub_system', $system);
                $NUR4G = $this->NUR->whereStrict("oz", $zone)->whereStrict("technology", "4G")->whereStrict('sub_system', $system);
             
                $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);
                $subs[$system] = number_format($NUR_c, 2, '.', ',');
            }
            $filtered = array_filter($subs, function ($value, $key) {
                return $value != 0;
            }, ARRAY_FILTER_USE_BOTH);



            $oz[$zone] = $filtered;
        }



        return $oz;
    }

    public function zonesSubsystemCountTickts($zones)
    {
        $oz = [];
        $subs = [];
        foreach ($zones as $zone) {
            $subsystems = $this->NUR->groupBy("sub_system")->keys();
            foreach ($subsystems as $system) {
                $subs[$system] = $this->NUR->where("oz", $zone)->where('sub_system', $system)->count();
            }
            $filtered = array_filter($subs, function ($value, $key) {
                return $value != 0;
            }, ARRAY_FILTER_USE_BOTH);

            $oz[$zone] = $filtered;
        }
        return $oz;
    }

    public function zonesRepeatedSites($zones)
    {
        $oz = [];


        foreach ($zones as $zone) {
            $subs = [];
            $siteCodes = $this->NUR->where("oz", $zone)->groupBy("problem_site_code");
            foreach ($siteCodes as $key => $codes) {



                $site["siteName"] = $codes->first()->problem_site_name;
                $site["siteCode"] = $codes->first()->problem_site_code;
                $site["count"] = $codes->count();
                array_push($subs, $site);
            }

            $sub = collect($subs);

            $sub = $sub->sortByDesc("count");
            $sub = $sub->take(5);
            $oz[$zone] = $sub->values();
        }
        return $oz;
    }

    public function zonesTopSitesNUR($zones, $period)
    {
        $oz = [];



        foreach ($zones as $zone) {
            $subs = [];
            $site = [];
            $siteCodes = $this->NUR->where("oz", $zone)->groupBy("problem_site_code");


            foreach ($siteCodes as $key => $codes) {
                $site["siteName"] = $codes->first()->problem_site_name;
                $site["siteCode"] = $codes->first()->problem_site_code;
                $NUR2G = $codes->whereStrict("technology", "2G")->values();
                $NUR3G = $codes->whereStrict("technology", "3G")->values();
                $NUR4G = $codes->whereStrict("technology", "4G")->values();
                $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);

                $site["NUR"] = number_format($NUR_c, 2, '.', ',');
                array_push($subs, $site);
            }

            $sub = collect($subs);

            $sub = $sub->sortByDesc("NUR");
            $sub = $sub->take(5);
            $oz[$zone] = $sub->values();
        }
        return $oz;
    }


    public function zonesAccessCountTickts($zones)
    {
        $oz = [];
        $sub = [];
        foreach ($zones as $zone) {


            $sub['access'] = $this->NUR->where("oz", $zone)->where('access', 1)->count();
            $oz[$zone] = $sub;
        }
        return $oz;
    }

    public function zonesAccessNUR($zones, $period)
    {
        $oz = [];
        $sub = [];
        foreach ($zones as $zone) {
            $sub["access"] = number_format($this->NUR->where("oz", $zone)->where('access', 1)->sum($period), 2, '.', ',');
            $oz[$zone] = $sub;
        }
        return $oz;
    }

    public function zonesResponseWithAccess($zones, $period)
    {
        $oz = [];
        $subs = [];
        foreach ($zones as $zone) {

            $NUR2G = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', ">", 240)->where("technology", "2G");
            $NUR3G = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', ">", 240)->where("technology", "3G");
            $NUR4G = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', ">", 240)->where("technology", "4G");

            $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);
           

            $subs['exceedSLA'] = number_format($NUR_c, 2, '.', ',');
            $subs['exceedSLA_count_tickets'] = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', ">", 240)->count();
            $NUR2G = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', "<=", 240)->where("technology", "2G");
            $NUR3G = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', "<=", 240)->where("technology", "3G");
            $NUR4G = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', "<=", 240)->where("technology", "4G");
          
            $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);
            $subs['withinSLA'] = number_format($NUR_c, 2, ".", ',');
            $subs['withinSLA_count_tickets'] = $this->NUR->Where("oz", $zone)->where("access", 1)->where('Dur_min', "<=", 240)->count();

            $oz[$zone] = $subs;
        }

        return $oz;
    }
    public function zonesResponseWithoutAccess($zones, $period)
    {
        $oz = [];
        $subs = [];
        foreach ($zones as $zone) {

            $NUR2G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', ">", 240)->where("technology", "2G");
            $NUR3G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', ">", 240)->where("technology", "3G");
            $NUR4G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', ">", 240)->where("technology", "4G");
           
            $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);

            $subs['exceedSLA'] = number_format($NUR_c, 2, '.', ',');
            $subs['exceedSLA_count_tickets'] = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', ">", 240)->count();
            $NUR2G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', "<=", 240)->where("technology", "2G");
            $NUR3G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', "<=", 240)->where("technology", "3G");
            $NUR4G = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', "<=", 240)->where("technology", "4G");
          
            $NUR_c=$this->calculateCombinedNUR($NUR2G,$NUR3G,$NUR4G);
            $subs['withinSLA'] = number_format($NUR_c, 2, ".", ',');
            $subs['withinSLA_count_tickets'] = $this->NUR->Where("oz", $zone)->where("access", 0)->where('Dur_min', "<=", 240)->count();

            $oz[$zone] = $subs;
        }

        return $oz;
    }

    private function calculateCombinedNUR($NUR2G, $NUR3G, $NUR4G)
    {
        if (count($NUR2G) > 0) {
            $sum_NUR_2G = number_format($NUR2G->sum("nur"), 2, '.', ',');
        } else {
            $sum_NUR_2G = 0;
        }
        if (count($NUR3G) > 0) {
            $sum_NUR_3G = number_format($NUR3G->sum("nur"), 2, '.', ',');
        } else {
            $sum_NUR_3G = 0;
        }
        if (count($NUR4G) > 0) {
            $sum_NUR_4G = number_format($NUR4G->sum("nur"), 2, '.', ',');
        } else {
            $sum_NUR_4G = 0;
        }
        $NUR_c = (($sum_NUR_2G * $this->network_2G_cells) + ($sum_NUR_3G * $this->network_3G_cells) + ($sum_NUR_4G * $this->network_4G_cells)) / ($this->network_2G_cells + $this->network_3G_cells + $this->network_4G_cells);
        return $NUR_c;
    }



    public function zonesGeneratorStatestics($zones, $period)
    {
        $oz = [];
        $subs = [];

        foreach ($zones as $zone) {
            $VF = [];
            $VFTickets=[];
            $ET = [];
            $ETTickets=[];
            $oRG = [];
            $rented = [];
            $allTickets = $this->NUR->where("oz", $zone)->where("sub_system", "GENERATOR");
            $ORG = $this->NUR->where("oz", $zone)->where("gen_owner", "orange");
            $Rented = $this->NUR->where("oz", $zone)->where("gen_owner", "rented");
            // $allTickets = $allTickets->toArray();
            foreach ($allTickets as $ticket) {
                $ticketArray = explode(" ", $ticket['solution']);

                foreach ($ticketArray as $filt) {
                    if ($filt == "Vf") {

                        array_push( $VFTickets, $ticket);
                        break;
                    }
                    if ($filt == "Et") {
                        array_push( $ETTickets, $ticket);
                        break;
                    }
                }
               
            }
            $VFTickets = collect($VFTickets);
            $VFNUR2G = $VFTickets->where("technology", "2G");
            $VFNUR3G = $VFTickets->where("technology", "3G");
            $VFNUR4G = $VFTickets->where("technology", "4G");
            $NUR_c = $this->calculateCombinedNUR($VFNUR2G, $VFNUR3G, $VFNUR4G);

            $VF["nur"] = number_format($NUR_c, 2, '.', ',');
            $VF['count'] = $VFTickets->count();
            $VF['tickets']=$VFTickets;

            $ETTickets = collect($ETTickets);
            $ETNUR2G = $ETTickets->where("technology", "2G");
            $ETNUR3G = $ETTickets->where("technology", "3G");
            $ETNUR4G = $ETTickets->where("technology", "4G");

            $NUR_c = $this->calculateCombinedNUR($ETNUR2G, $ETNUR3G, $ETNUR4G);
            $ET['nur'] = number_format($NUR_c, 2, '.', ',');
            $ET["count"] = $ETTickets->count();
            $ET['tickets']=$ETTickets;

            $ORGNUR2G = $ORG->where("technology", "2G");
            $ORGNUR3G = $ORG->where("technology", "3G");
            $ORGNUR4G = $ORG->where("technology", "4G");

            $NUR_c= $this->calculateCombinedNUR($ORGNUR2G, $ORGNUR3G, $ORGNUR4G);
          
            $oRG['count'] = $ORG->count();
            $oRG['nur'] = number_format($NUR_c, 2, '.', ',');
            $oRG["tickets"]=$ORG->values();

            $rentedNUR2G = $Rented->where("technology", "2G");
            $rentedNUR3G = $Rented->where("technology", "3G");
            $rentedNUR4G = $Rented->where("technology", "4G");

            $NUR_c= $this->calculateCombinedNUR($rentedNUR2G, $rentedNUR3G, $rentedNUR4G);

            $rented['count'] = $Rented->count();
            $rented['nur'] = number_format(  $NUR_c, 2, '.', ',');
            $rented["tickets"]=$Rented->values();

            $subs['VF'] = $VF;
            $subs['ET'] = $ET;
            $subs['ORG'] = $oRG;
            $subs['Rented'] = $rented;

            $oz[$zone] = $subs;
        }
        return $oz;
    }

    public function zonesAverageTicketsDur($zones)

    {
        $oz = [];

        foreach ($zones as $zone) {
            $accessAverageDurations = $this->NUR->where("oz", $zone)->where('access', 1)->avg('Dur_min');
            $accessAverageDurations = number_format($accessAverageDurations / 60, 2, '.', ',');
            $withoutAccessAverageDurations = $this->NUR->where("oz", $zone)->where('access', 0)->avg('Dur_min');
            $withoutAccessAverageDurations = number_format($withoutAccessAverageDurations / 60, 2, '.', ',');

            // $averageTicketsDur=$durations->avg();

            $oz[$zone]['access'] =  $accessAverageDurations;
            $oz[$zone]['withoutAccess'] =  $withoutAccessAverageDurations;
        }
        return $oz;
    }
}
