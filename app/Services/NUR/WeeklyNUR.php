<?php

namespace App\Services\NUR;

use App\services\NurHelpers\DurationMin\DurationMin;
use Illuminate\Validation\Rules\In;

class WeeklyNUR
{
   

    protected int $duration_min,$cells, $total_network_cells;



    public function __construct(int $duration_min,int $cells,int $total_network_cells)
    {
        $this->duration_min = $duration_min;
        $this->cells=$cells;
        $this->total_network_cells=$total_network_cells;

       
    }

    public function calculate_NUR()

    {
        
        $NX = ($this->duration_min * $this->cells) / (60 * 24);
        $weekly_nur = ($NX * 100000) / ($this->total_network_cells * 7);
        $weekly_nur = number_format($weekly_nur, 2, '.', ',');
        return $weekly_nur;
    }
}
