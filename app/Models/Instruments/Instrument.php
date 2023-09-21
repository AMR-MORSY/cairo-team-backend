<?php

namespace App\Models\Instruments;

use App\Models\Sites\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instrument extends Model
{
   
    use HasFactory;
    protected $table="instruments";
    protected $guarded=[];
    public function site()
    {
        return $this->belongsTo(Site::class,"site_code");
    }


}
