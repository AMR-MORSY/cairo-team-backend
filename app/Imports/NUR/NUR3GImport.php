<?php

namespace App\Imports\NUR;

use App\Models\NUR\NUR3G;
use App\Services\NUR\Durations;


use App\Services\NUR\WeeklyNUR;
use App\Services\NUR\MonthlyNUR;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
HeadingRowFormatter::default('none');

class NUR3GImport implements ToModel, WithHeadingRow,WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public  $week, $year,$network_cells;

    public function __construct($week, $year,$network_cells)
    {
        $this->week = $week;
        $this->year = $year;
        $this->network_cells=$network_cells;
    }
 
    public function rules(): array
    {
        return [
         
            "*.Problem source site code" => ["required",'string'],
            "*.Site Name (Node B)" => ["required",'string'],
            "*.Problem source site name"=>["required",'string'],
            "*.RNC" => ["required", "regex:/^([0-9a-zA-Z_-]|\s){3,50}$/"],
            "*.No of cells"=>["required","regex:/^(200)|[1-9]\d?$/"],
            '*.System'=>['required','string'],
            "*.Sub System"=>['required','string'],
            "*.Type"=>['required',"regex:/^Involuntary|Voluntary$/"],
            '*.Solution'=>['required','string'],
            "*.Access Problem"=>['required','boolean'],
            '*.Force Majeure'=>['nullable','boolean'],
            '*.Force Majeure Type'=>['nullable','string'],
             "*.Incident Start Time" => ["required", 'date'],
             "*.Incident End Time" => ["required","date"],
            "*.Operation Zone" => ["required",'string'],
            "*.Generator Owner"=>["nullable","regex:/^Shared|Orange|Rented$/"],
          

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
       
        $duration_min = Durations::DurationMin($row['Incident Start Time'], $row['Incident End Time']);
        $duration_hr = Durations::DurationHr($duration_min);
        $weekly_nur = new WeeklyNUR($duration_min, $row['No of cells'], $this->network_cells);
        $month_as_number = Durations::getMonth($row['Incident Start Time']);
        $days_of_month = Durations::calculate_month_days($month_as_number);
        $monthly_nur = new MonthlyNUR($days_of_month, $duration_min, $row['No of cells'], $this->network_cells);
        return new NUR3G([
            "impacted_sites"=>$row["Site Name (Node B)"],
            "RNC"=>strtolower( $row["RNC"]),
            "cells"=>$row["No of cells"],
            "oz"=>strtolower($row["Operation Zone"]) ,
            "begin"=>$row["Incident Start Time"],
            "end"=>$row["Incident End Time"],
            "problem_site_code"=>$row["Problem source site code"],
            'problem_site_name'=>$row["Problem source site name"],
            "week"=>$this->week,
            "year"=>$this->year,
            "network_cells"=>$this->network_cells,
            "system"=>strtolower($row['System']) ,
            "sub_system"=>strtolower($row["Sub System"]) ,
            "nur"=>$weekly_nur->calculate_NUR(),
            'Dur_min'=> $duration_min,
            'Dur_Hr'=>  $duration_hr,
            'type'=>$row['Type'],
            'solution'=>strtolower($row['Solution']) ,
            "gen_owner"=>strtolower($row["Generator Owner"]) ,
            "access"=>$row['Access Problem'],
            'Force_Majeure'=>$row['Force Majeure'],
            'Force_Majeure_type'=>$row['Force Majeure Type'],
            'month'=>$month_as_number,
            'monthly_nur'=> $monthly_nur->calculate_monthly_nur(),
            

        ]);
    }
    public function prepareForValidation(array $row)
    {
        
    
             $row["Incident Start Time"] = Durations::transformDate($row["Incident Start Time"]);
          
       
             $row["Incident End Time"] = Durations::transformDate($row["Incident End Time"]);
          

            return $row;

      
       
    }
   
   
  
   
}
