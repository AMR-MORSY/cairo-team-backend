<?php

namespace App\Services\NUR\NURStatestics;

use App\Services\NUR\NURStatestics\NURHelpers;

class WeeklyStatestics
{

    private $NUR2G, $NUR3G, $NUR4G,$allTickets,$network_2G_cells,$network_3G_cells,$network_4G_cells;

    public function __construct($NUR2G, $NUR3G, $NUR4G,$network_2G_cells,$network_3G_cells,$network_4G_cells)
    {
        $this->NUR2G = $NUR2G;
        $this->NUR3G = $NUR3G;
        $this->NUR4G = $NUR4G;
        $this->allTickets=[];
        $this->network_2G_cells=$network_2G_cells;
        $this->network_3G_cells=$network_3G_cells;
        $this->network_4G_cells=$network_4G_cells;
        foreach($this->NUR2G as $NUR)
        {
            array_push($this->allTickets,$NUR);
        }
        foreach($this->NUR3G as $NUR)
        {
            array_push($this->allTickets,$NUR);
        }
        foreach($this->NUR4G as $NUR)
        {
            array_push($this->allTickets,$NUR);
        }
        $this->allTickets=collect($this->allTickets);
    }

    public function zonesSubsystemNUR()
    {
        $NURHelpers = new NURHelpers($this->allTickets,$this->network_2G_cells,$this->network_3G_cells,$this->network_4G_cells);
        $zonesSubsystemNUR = $NURHelpers->zonesSubsystemNUR($this->allTickets->groupBy('oz')->keys(),"nur");
        return $zonesSubsystemNUR;
       

    }
    public function zonesSubsystemCountTickts()
    {
        $NURHelpers = new NURHelpers($this->allTickets,$this->network_2G_cells,$this->network_3G_cells,$this->network_4G_cells);
        $zonesSubsystemCountTickts=$NURHelpers->zonesSubsystemCountTickts($this->allTickets->groupBy('oz')->keys());
        return $zonesSubsystemCountTickts;

    }

    public function zonesResponseWithAccess()
    {
        $NURHelpers = new NURHelpers($this->allTickets,$this->network_2G_cells,$this->network_3G_cells,$this->network_4G_cells);
        $zonesResponseWithAccess=$NURHelpers->zonesResponseWithAccess($this->allTickets->groupBy('oz')->keys(),"nur");
        return $zonesResponseWithAccess;
    }

    public function zonesResponseWithoutAccess()
    {
        $NURHelpers = new NURHelpers($this->allTickets,$this->network_2G_cells,$this->network_3G_cells,$this->network_4G_cells);
        $zonesResponseWithoutAccess=$NURHelpers->zonesResponseWithoutAccess($this->allTickets->groupBy('oz')->keys(),"nur");
        return $zonesResponseWithoutAccess;

    }
    public function zonesGeneratorStatestics()
    {
        $NURHelpers = new NURHelpers($this->allTickets,$this->network_2G_cells,$this->network_3G_cells,$this->network_4G_cells);
        $zonesGeneratorStatestics=$NURHelpers->zonesGeneratorStatestics($this->allTickets->groupBy('oz')->keys(),"nur");
        return $zonesGeneratorStatestics;

    }


    public function NUR2GStatestics()
    {
        $NURHelpers = new NURHelpers($this->NUR2G,$this->network_2G_cells,$this->network_3G_cells,$this->network_4G_cells);
        $cairo2GNUR = number_format($this->NUR2G->sum('nur'), 2, '.', ',');
        $zonesNUR = $NURHelpers->zonesNUR($this->NUR2G->groupBy('oz')->keys(), "nur");
        // $zonesSubsystemNUR = $NURHelpers->zonesSubsystemNUR($this->NUR2G->groupBy('oz')->keys(),"nur");
        // $zonesSubsystemCountTickts =  $NURHelpers->zonesSubsystemCountTickts($this->NUR2G->groupBy('oz')->keys());
        // $zonesAccessCountTickts = $NURHelpers->zonesAccessCountTickts($this->NUR2G->groupBy('oz')->keys());
        // $zonesAccessNUR = $NURHelpers->zonesAccessNUR($this->NUR2G->groupBy('oz')->keys(),"nur");
        // $zonesTopSitesNur = $NURHelpers->zonesTopSitesNur($this->NUR2G->groupBy('oz')->keys(),"nur");
        // $zonesRepeatedSites = $NURHelpers->zonesRepeatedSites($this->NUR2G->groupBy('oz')->keys());
        // $zonesGeneratorStatestics =  $NURHelpers->zonesGeneratorStatestics($this->NUR2G->groupBy('oz')->keys(),"nur");
        // $zonesAverageTicketsDur =  $NURHelpers->zonesAverageTicketsDur($this->NUR2G->groupBy('oz')->keys());
        // $zonesResponseWithAccess=$NURHelpers->zonesResponseWithAccess($this->NUR2G->groupBy('oz')->keys(),"nur");
        // $zonesResponseWithoutAccess=$NURHelpers->zonesResponseWithoutAccess($this->NUR2G->groupBy('oz')->keys(),"nur");
        $zonesTotalNumTickets = $NURHelpers->zonesTotalNumTickets($this->NUR2G->groupBy('oz')->keys());

        $NUR2G['cairoNUR2G'] = $cairo2GNUR;
        $NUR2G['zonesNUR2G'] = $zonesNUR;
        // $NUR2G['zonesNUR2GSubsystemNUR'] = $zonesSubsystemNUR;
        // $NUR2G['zonesNUR2GSubsystemCountTickets'] = $zonesSubsystemCountTickts;
        // $NUR2G['zonesNUR2GAccessCountTickets'] =  $zonesAccessCountTickts;
        // $NUR2G['zonesNUR2GAccessNUR'] = $zonesAccessNUR;
        // $NUR2G['zonesNUR2GTopSitesNUR'] =  $zonesTopSitesNur;
        // $NUR2G['zonesNUR2GRepeatedSitesNUR'] =  $zonesRepeatedSites;
        // $NUR2G['zonesNUR2GGenStatestics'] =   $zonesGeneratorStatestics;
        // $NUR2G['zonesNUR2AverageTicketsDur'] =    $zonesAverageTicketsDur;
        // $NUR2G['zonesResponseWithoutAccess']= $zonesResponseWithoutAccess;
        // $NUR2G['zonesResponseWithAccess']= $zonesResponseWithAccess;
        $NUR2G['zonesTotalNumTickets'] = $zonesTotalNumTickets;


        return    $NUR2G;
    }
    public function NUR3GStatestics()
    {
        $NURHelpers = new NURHelpers($this->NUR3G,$this->network_2G_cells,$this->network_3G_cells,$this->network_4G_cells);
        $cairo3GNUR = number_format($this->NUR3G->sum('nur'), 2, '.', ',');
        $zonesNUR = $NURHelpers->zonesNUR($this->NUR3G->groupBy('oz')->keys(), "nur");
        // $zonesSubsystemNUR = $NURHelpers->zonesSubsystemNUR($this->NUR3G->groupBy('oz')->keys(), "nur");
        // $zonesSubsystemCountTickts =  $NURHelpers->zonesSubsystemCountTickts($this->NUR3G->groupBy('oz')->keys());
        // $zonesAccessCountTickts = $NURHelpers->zonesAccessCountTickts($this->NUR3G->groupBy('oz')->keys());
        // $zonesAccessNUR =   $NURHelpers->zonesAccessNUR($this->NUR3G->groupBy('oz')->keys(), "nur");
        // $zonesTopSitesNur =   $NURHelpers->zonesTopSitesNur($this->NUR3G->groupBy('oz')->keys(), "nur");
        // $zonesRepeatedSites =  $NURHelpers->zonesRepeatedSites($this->NUR3G->groupBy('oz')->keys());
        // $zonesGeneratorStatestics =  $NURHelpers->zonesGeneratorStatestics($this->NUR3G->groupBy('oz')->keys(), "nur");
        // $zonesAverageTicketsDur =  $NURHelpers->zonesAverageTicketsDur($this->NUR3G->groupBy('oz')->keys());
        // $zonesResponseWithAccess = $NURHelpers->zonesResponseWithAccess($this->NUR3G->groupBy('oz')->keys(), "nur");
        // $zonesResponseWithoutAccess = $NURHelpers->zonesResponseWithoutAccess($this->NUR3G->groupBy('oz')->keys(), "nur");
        $zonesTotalNumTickets = $NURHelpers->zonesTotalNumTickets($this->NUR3G->groupBy('oz')->keys());

        $NUR3G['cairoNUR3G'] = $cairo3GNUR;
        $NUR3G['zonesNUR3G'] = $zonesNUR;
        // $NUR3G['zonesNUR3GSubsystemNUR'] = $zonesSubsystemNUR;
        // $NUR3G['zonesNUR3GSubsystemCountTickets'] = $zonesSubsystemCountTickts;
        // $NUR3G['zonesNUR3GAccessCountTickets'] =  $zonesAccessCountTickts;
        // $NUR3G['zonesNUR3GAccessNUR'] = $zonesAccessNUR;
        // $NUR3G['zonesNUR3GTopSitesNUR'] =  $zonesTopSitesNur;
        // $NUR3G['zonesNUR3GRepeatedSitesNUR'] =  $zonesRepeatedSites;
        // $NUR3G['zonesNUR3GGenStatestics'] =   $zonesGeneratorStatestics;
        // $NUR3G['zonesNUR2AverageTicketsDur'] =    $zonesAverageTicketsDur;
        // $NUR3G['zonesResponseWithoutAccess'] = $zonesResponseWithoutAccess;
        // $NUR3G['zonesResponseWithAccess'] = $zonesResponseWithAccess;
        $NUR3G['zonesTotalNumTickets'] = $zonesTotalNumTickets;



        return   $NUR3G;
    }
    public function NUR4GStatestics()
    {
        $NURHelpers = new NURHelpers($this->NUR4G,$this->network_2G_cells,$this->network_3G_cells,$this->network_4G_cells);
        $cairo4GNUR = number_format($this->NUR4G->sum('nur'), 2, '.', ',');
        $zonesNUR =  $NURHelpers->zonesNUR($this->NUR4G->groupBy('oz')->keys(), "nur");
        // $zonesSubsystemNUR = $NURHelpers->zonesSubsystemNUR($this->NUR4G->groupBy('oz')->keys(),"nur");
        // $zonesSubsystemCountTickts =  $NURHelpers->zonesSubsystemCountTickts($this->NUR4G->groupBy('oz')->keys());
        // $zonesAccessCountTickts = $NURHelpers->zonesAccessCountTickts($this->NUR4G->groupBy('oz')->keys());
        // $zonesAccessNUR = $NURHelpers->zonesAccessNUR($this->NUR4G->groupBy('oz')->keys(),"nur");
        // $zonesTopSitesNur = $NURHelpers->zonesTopSitesNur($this->NUR4G->groupBy('oz')->keys(),"nur");
        // $zonesRepeatedSites = $NURHelpers->zonesRepeatedSites($this->NUR4G->groupBy('oz')->keys());
        // $zonesGeneratorStatestics =  $NURHelpers->zonesGeneratorStatestics($this->NUR4G->groupBy('oz')->keys(),"nur");
        // $zonesAverageTicketsDur = $NURHelpers->zonesAverageTicketsDur($this->NUR4G->groupBy('oz')->keys());
        // $zonesResponseWithAccess=$NURHelpers->zonesResponseWithAccess($this->NUR4G->groupBy('oz')->keys(),"nur");
        // $zonesResponseWithoutAccess=$NURHelpers->zonesResponseWithoutAccess($this->NUR4G->groupBy('oz')->keys(),"nur");
        $zonesTotalNumTickets = $NURHelpers->zonesTotalNumTickets($this->NUR4G->groupBy('oz')->keys());

        $NUR4G['cairoNUR4G'] = $cairo4GNUR;
        $NUR4G['zonesNUR4G'] = $zonesNUR;
        // $NUR4G['zonesNUR4GSubsystemNUR'] = $zonesSubsystemNUR;
        // $NUR4G['zonesNUR4GSubsystemCountTickets'] = $zonesSubsystemCountTickts;
        // $NUR4G['zonesNUR4GAccessCountTickets'] =  $zonesAccessCountTickts;
        // $NUR4G['zonesNUR4GAccessNUR'] = $zonesAccessNUR;
        // $NUR4G['zonesNUR4GTopSitesNUR'] =  $zonesTopSitesNur;
        // $NUR4G['zonesNUR4GRepeatedSitesNUR'] =  $zonesRepeatedSites;
        // $NUR4G['zonesNUR4GGenStatestics'] =   $zonesGeneratorStatestics;
        // $NUR4G['zonesNUR2AverageTicketsDur'] =    $zonesAverageTicketsDur;
        // $NUR4G['zonesResponseWithoutAccess']= $zonesResponseWithoutAccess;
        // $NUR4G['zonesResponseWithAccess']= $zonesResponseWithAccess;
        $NUR4G['zonesTotalNumTickets'] = $zonesTotalNumTickets;


        return   $NUR4G;
    }
    private function getRepeatedSites($repeated2G, $repeated3G, $repeated4G)
    {

        $array2G3G4G = [];

        foreach ($repeated2G as $site) {
            array_push($array2G3G4G, $site);
        }
        foreach ($repeated3G as $site) {
            array_push($array2G3G4G, $site);
        }
        foreach ($repeated4G as $site) {
            array_push($array2G3G4G, $site);
        }
        $array2G3G4G = collect($array2G3G4G);
        $sites = $array2G3G4G->groupBy("siteCode");
        $tops = [];
        foreach ($sites as $key => $values) {
            $top = $values->sortByDesc("count")->first();
            array_push($tops, $top);
        }
        return $tops;
    }

    private function getTopNurSites($top2G, $top3G, $top4G)
    {
        $array2G3G4G = [];

        foreach ($top2G as $site) {
            $site['tech'] = "2G";
            array_push($array2G3G4G, $site);
        }
        foreach ($top3G as $site) {
            $site['tech'] = "3G";
            array_push($array2G3G4G, $site);
        }
        foreach ($top4G as $site) {
            $site['tech'] = "4G";
            array_push($array2G3G4G, $site);
        }

        $array2G3G4G = collect($array2G3G4G);
        $siteCodes = $array2G3G4G->groupBy("siteCode")->keys();
        $tops = [];

        foreach ($siteCodes as $code) {
            $Nur2G = $array2G3G4G->where("tech", "2G")->where("siteCode", $code)->first();
            $Nur3G = $array2G3G4G->where("siteCode", $code)->where("tech", "3G")->first();
            $Nur4G = $array2G3G4G->where("siteCode", $code)->where("tech", "4G")->first();

            $has["2G"] = $Nur2G;
            $has["3G"] = $Nur3G;
            $has["4G"] = $Nur4G;


            array_push($tops, $has);
        }
        $newSites = [];

        foreach ($tops as $top) {
            if (isset($top["2G"])) {
                $NUR2G = $top["2G"]['NUR'];
                $siteCode = $top["2G"]['siteCode'];
                $siteName = $top["2G"]['siteName'];
            } else {
                $NUR2G = 0;
            }
            if (isset($top["3G"])) {
                $NUR3G = $top["3G"]['NUR'];
                $siteCode = $top["3G"]['siteCode'];
                $siteName = $top["3G"]['siteName'];
            } else {
                $NUR3G = 0;
            }
            if (isset($top["4G"])) {
                $NUR4G = $top["4G"]['NUR'];
                $siteCode = $top["4G"]['siteCode'];
                $siteName = $top["4G"]['siteName'];
            } else {
                $NUR4G = 0;
            }
            $NurCombined = number_format(((floatval($NUR2G) * $this->NUR2G->first()->network_cells) + (floatval($NUR3G)  * $this->NUR3G->first()->network_cells) + (floatval($NUR4G)* $this->NUR4G->first()->network_cells)) /($this->NUR2G->first()->network_cells + $this->NUR3G->first()->network_cells + $this->NUR4G->first()->network_cells), 2, '.', ',');

            $topSite['siteCode'] = $siteCode;
            $topSite['siteName'] = $siteName;
            $topSite["NUR"] = $NurCombined;
            array_push($newSites, $topSite);
        }


       



        return $newSites;
    }

    public function zonesTopRepeated()
    {
        $NURHelpers = new NURHelpers($this->allTickets,$this->network_2G_cells,$this->network_3G_cells,$this->network_4G_cells);
        $zonesRepeatedSites=$NURHelpers->zonesRepeatedSites($this->allTickets->groupBy('oz')->keys());
        return $zonesRepeatedSites;


        
    }

    public function zonesTopNUR()
    {
        $NURHelpers = new NURHelpers($this->allTickets,$this->network_2G_cells,$this->network_3G_cells,$this->network_4G_cells);
        $zonesTopSitesNUR=$NURHelpers->zonesTopSitesNUR($this->allTickets->groupBy('oz')->keys(),"nur");
        return  $zonesTopSitesNUR;

       
    }

    public function combinedNUR()
    {
        $NUR2G = $this->NUR2GStatestics();
        $zonesNUR2G = $NUR2G['zonesNUR2G'];
        $NUR3G = $this->NUR3GStatestics();
        $zonesNUR3G = $NUR3G['zonesNUR3G'];
        $NUR4G = $this->NUR4GStatestics();
        $zonesNUR4G = $NUR4G['zonesNUR4G'];


        $cairoEastCombined = number_format((($zonesNUR2G['CAIRO EAST'] * $this->network_2G_cells) + ($zonesNUR3G['CAIRO EAST'] * $this->network_3G_cells) + ($zonesNUR4G['CAIRO EAST'] * $this->network_4G_cells)) / ($this->network_2G_cells + $this->network_3G_cells + $this->network_4G_cells), 2, '.', ',');

        $cairoSouthCombined = number_format((($zonesNUR2G['CAIRO SOUTH'] * $this->network_2G_cells) + ($zonesNUR3G['CAIRO SOUTH'] * $this->network_3G_cells) + ($zonesNUR4G['CAIRO SOUTH'] * $this->network_4G_cells)) / ($this->network_2G_cells + $this->network_3G_cells + $this->network_4G_cells), 2, '.', ',');
        $cairoNorthCombined = number_format((($zonesNUR2G['CAIRO NORTH'] * $this->network_2G_cells) + ($zonesNUR3G['CAIRO NORTH'] * $this->network_3G_cells) + ($zonesNUR4G['CAIRO NORTH'] * $this->network_4G_cells)) / ($this->network_2G_cells + $this->network_3G_cells + $this->network_4G_cells), 2, '.', ',');
        $gizaCombined = number_format((($zonesNUR2G['GIZA'] * $this->network_2G_cells) + ($zonesNUR3G['GIZA'] * $this->network_3G_cells) + ($zonesNUR4G['GIZA'] * $this->network_4G_cells)) / ($this->network_2G_cells + $this->network_3G_cells + $this->network_4G_cells), 2, '.', ',');
        $combined['CAIRO EAST'] =  $cairoEastCombined;
        $combined['CAIRO SOUTH'] =  $cairoSouthCombined;
        $combined['CAIRO NORTH'] =  $cairoNorthCombined;
        $combined['GIZA'] =  $gizaCombined;
        $combined["cairo"] = number_format($cairoEastCombined + $cairoNorthCombined + $cairoSouthCombined + $gizaCombined, 2, '.', ',');
        return $combined;
    }
}
