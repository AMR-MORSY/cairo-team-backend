<?php

namespace App\Services\NUR\NURStatestics;

use stdClass;
use App\Models\Sites\Site;

class CairoNURHelpers{

    public static function getImpactedSites($tickets)
    {
       
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
          
            $combined = $NUR_tickets->sum('nur_c');
          
            $site_information->NUR_C=number_format($combined, 2, '.', ',');
            $site["site_data"]=$site_information;
            array_push($sites, $site);
        }
        return $sites = collect($sites);
    }

}