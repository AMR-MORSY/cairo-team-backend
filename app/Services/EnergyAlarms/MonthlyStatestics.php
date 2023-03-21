<?php

namespace App\Services\EnergyAlarms;

class MonthlyStatestics{
    protected $powerAlarms, $genAlarms, $HTAlarms, $downAlarms,$month;
    public function __construct($powerAlarms, $genAlarms, $HTAlarms, $downAlarms,$month)
    {
        $this->powerAlarms = $powerAlarms;
        $this->genAlarms = $genAlarms;
        $this->HTAlarms = $HTAlarms;
        $this->downAlarms = $downAlarms;
        $this->month=$month;
    }

}