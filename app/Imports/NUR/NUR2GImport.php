<?php

namespace App\Imports\NUR;




use App\Models\NUR\NUR2G;
use App\Services\NUR\MonthlyNUR;
use App\Services\NUR\Durations;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Services\NUR\WeeklyNUR;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class NUR2GImport implements ToModel, WithValidation,WithHeadingRow
{

    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public $week, $year, $technology_cells,$total_net_cells;



    public function __construct($week, $year, $cells,$total_net_cells)
    {
        $this->week = $week;
        $this->year = $year;
        $this->technology_cells = $cells;
        $this->total_net_cells=$total_net_cells;
    }



    public function rules(): array
    {
        return [

            "*.Problem source site code" => ['required','string'],
            "*.Site name" => ['required','string'],
            "*.Problem source site name" => [ "string"],
            "*.BSC" => ["nullable", "regex:/^([0-9a-zA-Z_-]|\s){3,50}$/"],
            "*.Cells" => ["required", "regex:/^([1-9][0-9]{0,2}|1000)$/"],
            '*.System' => ['string'],
            "*.Sub system" => ['string'],
            "*.Type" => ['required', "regex:/^Involuntary|Voluntary$/"],
            '*.Solution' => ['required', 'string'],
            "*.Access Problem" => ['required', 'boolean'],
            '*.Force Majeure' => ['nullable', 'boolean'],
            '*.Force Majeure Type' => ['nullable', 'string'],
            "*.Begin" => ["required", 'date'],
            "*.End" => ["required", "date"],
            "*.Operation Zone" => ["string"],
            "*.Generator Owner"=>["nullable","regex:/^Shared|Orange|Rented$/"],
            "*.Action OGS responsible"=>["nullable","string"]


        ];
    }

   
    public function model(array $row)
    {
        if (strtolower($row["Operation Zone"])=="delta north"||
        strtolower($row["Operation Zone"])=="delta south"||
        strtolower($row["Operation Zone"])=="north upper"||
        strtolower($row["Operation Zone"])=="south upper"||
        strtolower($row["Operation Zone"])=="alex"||
        strtolower($row["Operation Zone"])=="red sea"||
        strtolower($row["Operation Zone"])=="sinai"||
        strtolower($row["Operation Zone"])=="north coast") {
            return null;
        }
       
        $duration_min = Durations::DurationMin($row['Begin'], $row['End']);
        $duration_hr = Durations::DurationHr($duration_min);
        $weekly_nur = WeeklyNUR::calculate_NUR($duration_min, $row['Cells'], $this->technology_cells);
        $combinedNUR=WeeklyNUR::calculateCombinedNUR ($weekly_nur,$this->technology_cells,$this->total_net_cells);
        $month_as_number = Durations::getMonth($row['Begin']);
        $days_of_month = Durations::calculate_month_days($month_as_number);
        $monthly_nur = new MonthlyNUR($days_of_month, $duration_min, $row['Cells'], $this->technology_cells);
        return new NUR2G([
            "Action_OGS_responsible"=>$row["Action OGS responsible"],
            "impacted_sites" => $row["Site name"],
            "BSC" => strtolower( $row["BSC"]),
            "cells" => $row["Cells"],
            "oz" => strtolower($row["Operation Zone"]) ,
            "begin" => $row["Begin"],
            "end" => $row["End"],
            "problem_site_code" => $row["Problem source site code"],
            'problem_site_name' => $row["Problem source site name"],
            "week" => $this->week,
            "year" => $this->year,
            "system" => strtolower($row['System']) ,
            "sub_system" => strtolower($row["Sub system"]) ,
            "nur" => $weekly_nur,
            'total_network_cells'=>$this->total_net_cells,
            'Dur_min' => $duration_min,
            'Dur_Hr' => $duration_hr,
            'type' => $row['Type'],
            "nur_c"=>$combinedNUR,
            'solution' => strtolower($row['Solution']),
            "access" => $row['Access Problem'],
            'Force_Majeure' => $row['Force Majeure'],
            'Force_Majeure_type' => $row['Force Majeure Type'],
            'month' => $month_as_number,
            "network_cells_2G"=>$this->technology_cells,
            "gen_owner"=> strtolower($row["Generator Owner"]) ,
            'monthly_nur' => $monthly_nur->calculate_monthly_nur(),


        ]);
    }
  
    public function prepareForValidation(array $row)
    {
        $row["Begin"] = Durations::transformDate($row["Begin"]);


        $row["End"] = Durations::transformDate($row["End"]);


        return $row;
    }
  



    // public function batchSize(): int
    // {
    //     return 200;
    // }
   
}
