<?php

namespace App\Services\NUR\NURStatestics;

use App\Models\NUR\NUR4G;

class YearlyStatestics
{

    private $NUR2G, $NUR3G, $NUR4G,$year,$allTickets;

    public function __construct($NUR2G, $NUR3G, $NUR4G,$year)
    {
        $this->NUR2G = $NUR2G;
        $this->NUR3G = $NUR3G;
        $this->NUR4G = $NUR4G;
        $this->year=$year;
      
    }

    private function zonesNUR_C()
    {
        $zones=$this->NUR2G->where("year",$this->year)->groupBy("oz")->keys();
        $zonesNUR_C=[];
        foreach($zones as $zone)
        {
            $zoneNUR_2G=$this->NUR2G->where("oz",$zone)->where("year",$this->year);
            $zoneNUR_3G=$this->NUR3G->where("oz",$zone)->where("year",$this->year);
            $zoneNUR_4G=$this->NUR4G->where("oz",$zone)->where("year",$this->year);

            $allTickets=[];
            foreach( $zoneNUR_2G as $NUR)
            {
                array_push($allTickets,$NUR);
            }
            foreach(  $zoneNUR_3G as $NUR)
            {
                array_push($allTickets,$NUR);
            }
            foreach( $zoneNUR_4G as $NUR)
            {
                array_push($allTickets,$NUR);
            }
            $allTickets=collect($allTickets);
            $allTickets_week=$allTickets->groupBy("week");
            $NUR_week_c=[];
            foreach($allTickets_week as $week=>$tickets)
            {
                $network_2g_cells=$tickets->where("technology","2G")->first();
                if($network_2g_cells)
                {
                    $network_2g_cells=$network_2g_cells->network_cells;
                }
                else{
                    $network_2g_cells=0;
                }
                $network_3g_cells=$tickets->where("technology","3G")->first();
                if($network_3g_cells)
                {
                    $network_3g_cells=$network_3g_cells->network_cells;
                }
                else{
                    $network_2g_cells=0;
                }

                $network_4g_cells=$tickets->where("technology","4G")->first();
                if($network_4g_cells)
                {
                    $network_4g_cells=$network_4g_cells->network_cells;
                }
                else{
                    $network_4g_cells=0;
                }
             
              
                $NUR_week_c["week $week"]=$this->weekNUR_C( $tickets, $network_2g_cells, $network_3g_cells, $network_4g_cells);

            }

            
            $zonesNUR_C[$zone]=$NUR_week_c;

        }

        return $zonesNUR_C;
    }
    private function weekNUR_C($tickets, $network_2g_cells, $network_3g_cells, $network_4g_cells)
    {
        $NUR_2G_sum = $tickets->where("technology","2G")->sum("nur");
        $NUR_3G_sum =  $tickets->where("technology","3G")->sum("nur");
        $NUR_4G_sum = $tickets->where("technology","4G")->sum("nur");
        $combined = (($NUR_2G_sum * $network_2g_cells) + ($NUR_3G_sum * $network_3g_cells) + ($NUR_4G_sum * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_combined = number_format($combined, 2, '.', ',');

        return $NUR_combined;

    }
    private function getNUR_C()
    {
        $allTickets=$this->gatherTickets();
        $allTickets_weeks=$allTickets->groupBy("week");
        $NUR_C=[];

        foreach(  $allTickets_weeks as $week=>$tickets)
        {
          
            $network_2g_cells=$tickets->where("technology","2G")->first();
            if($network_2g_cells)
            {
                $network_2g_cells=$network_2g_cells->network_cells;
            }
            else{
                $network_2g_cells=0;
            }
            $network_3g_cells=$tickets->where("technology","3G")->first();
            if($network_3g_cells)
            {
                $network_3g_cells=$network_3g_cells->network_cells;
            }
            else{
                $network_2g_cells=0;
            }

            $network_4g_cells=$tickets->where("technology","4G")->first();
            if($network_4g_cells)
            {
                $network_4g_cells=$network_4g_cells->network_cells;
            }
            else{
                $network_4g_cells=0;
            }
         
            $NUR_C["week $week"]=$this->weekNUR_C($tickets, $network_2g_cells, $network_3g_cells, $network_4g_cells);

        }

        return $NUR_C;
        


    }
    public function cairoNUR_C()
    {
        $cairoNUR_C=$this->getNUR_C();
        $zonesNUR_c=$this->zonesNUR_C();

        return [
            "cairo"=>$cairoNUR_C,
            "zones"=>$zonesNUR_c
        ];
       
      
    }
  

    public function cairoTxNUR()
    {
        $cairoNUR_C=$this->getNUR_C();
        $zonesNUR_c=$this->zonesNUR_C();
        return [
            "cairo"=>$cairoNUR_C,
            "zones"=>$zonesNUR_c
        ];
      



    }
 
    public function cairoGenNUR()
    {
        $allTickets=$this->gatherTickets();
        $allTickets_weeks=$allTickets->groupBy("week");
        $gen_NUR_C=[];

        foreach(  $allTickets_weeks as $week=>$tickets)
        {
            $network_2g_cells=$tickets->where("technology","2G")->first();
            if($network_2g_cells)
            {
                $network_2g_cells=$network_2g_cells->network_cells;
            }
            else{
                $network_2g_cells=0;
            }
            $network_3g_cells=$tickets->where("technology","3G")->first();
            if($network_3g_cells)
            {
                $network_3g_cells=$network_3g_cells->network_cells;
            }
            else{
                $network_2g_cells=0;
            }

            $network_4g_cells=$tickets->where("technology","4G")->first();
            if($network_4g_cells)
            {
                $network_4g_cells=$network_4g_cells->network_cells;
            }
            else{
                $network_4g_cells=0;
            }
         
            $gen_NUR_C["week $week"]=$this->cairoGenWeeklyStatestics($tickets, $network_2g_cells, $network_3g_cells, $network_4g_cells);

        }

        return $gen_NUR_C;


    }
    private function cairoGenWeeklyStatestics($tickets, $network_2g_cells,  $network_3g_cells,  $network_4g_cells)
    {
        $statestics = [];
        $NUR_2G_sum = $tickets->where("technology","2G")->sum("nur");
        $NUR_3G_sum =  $tickets->where("technology","3G")->sum("nur");
        $NUR_4G_sum = $tickets->where("technology","4G")->sum("nur");
        $combined = (($NUR_2G_sum * $network_2g_cells) + ($NUR_3G_sum * $network_3g_cells) + ($NUR_4G_sum * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_Org = $tickets->where("technology","2G")->where("gen_owner", "orange")->sum("nur");
        $NUR_3G_Org =  $tickets->where("technology","3G")->where("gen_owner", "orange")->sum("nur");
        $NUR_4G_Org =  $tickets->where("technology","4G")->where("gen_owner", "orange")->sum("nur");
        $combined = (($NUR_2G_Org * $network_2g_cells) + ($NUR_3G_Org * $network_3g_cells) +($NUR_4G_Org * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_Org_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_Rented = $tickets->where("technology","2G")->where("gen_owner", "rented")->sum("nur");
        $NUR_3G_Rented = $tickets->where("technology","3G")->where("gen_owner", "rented")->sum("nur");
        $NUR_4G_Rented = $tickets->where("technology","4G")->where("gen_owner", "rented")->sum("nur");
        $combined = (($NUR_2G_Rented * $network_2g_cells) + ($NUR_3G_Rented * $network_3g_cells) +($NUR_4G_Rented * $network_4g_cells)) / ($network_4g_cells + $network_3g_cells + $network_2g_cells);
        $NUR_Rented_combined = number_format($combined, 2, '.', ',');

        $NUR_2G_ET_tickets=$this->getVFandETGenTickets( $tickets->where("technology","2G"))["ET"];
        $NUR_3G_ET_tickets=$this->getVFandETGenTickets( $tickets->where("technology","3G"))["ET"];
        $NUR_4G_ET_tickets=$this->getVFandETGenTickets( $tickets->where("technology","4G"))["ET"];
        $NUR_2G_VF_tickets=$this->getVFandETGenTickets( $tickets->where("technology","2G"))["VF"];
        $NUR_3G_VF_tickets=$this->getVFandETGenTickets( $tickets->where("technology","3G"))["VF"];;
        $NUR_4G_VF_tickets=$this->getVFandETGenTickets( $tickets->where("technology","4G"))["VF"];;
      
      

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
   

    private function gatherTickets()
    {
        $allTickets=[];
        foreach($this->NUR2G as $NUR)
        {
            array_push($allTickets,$NUR);
        }
        foreach($this->NUR3G as $NUR)
        {
            array_push($allTickets,$NUR);
        }
        foreach($this->NUR4G as $NUR)
        {
            array_push($allTickets,$NUR);
        }
        $allTickets=collect($allTickets);

        return $allTickets;
    }
   

    
   
}