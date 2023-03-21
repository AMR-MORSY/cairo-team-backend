<?php

namespace App\Exports\sites;

use App\Models\Sites\Site;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class AllSitesExport implements FromCollection,WithHeadings,Responsable
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection

    */
    private $fileName = 'AllSites.xlsx';
    
    /**
    * Optional Writer Type
    */
    private $writerType = Excel::XLSX;
    
    /**
    * Optional headers
    */
    private $headers = [
        'Content-Type' => 'text/xlsx',
    ];
    public function collection()
    {
        return Site::all();
    }
    public function headings(): array
    {
        return [
            '#',
            'Site Code',
            'Site Name',
            "BSC",
            "RNC",
            "Office",
            "Type",
            "Category",
            "Severity",
            "Sharing",
            "Host",
            "Gest",
            "oz",
            "zone",
            "2G",
            "3G",
            "4G",
            
        ];
    }
}
