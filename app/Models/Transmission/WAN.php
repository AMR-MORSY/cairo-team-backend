<?php

namespace App\Models\Transmission;

use Carbon\Carbon;
use App\Models\Sites\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WAN extends Model
{
    use HasFactory;
    protected $table = "wans";
    protected $guarded = [];
    protected $hidden = ["created_at", "updated_at"];
    public function site()
    {
        return $this->belongsTo(Site::class, "site_code", "site_code");
    }

    protected function clearanceDate(): Attribute
    {
        return Attribute::make(
           
            set: function ($value) {
                if ($value=="") {
                   return null;
                }
                else{
                    $newDate = Carbon::parse($value);
                    return  $newDate->format("Y-m-d");
                }
            }
        );
    }
    protected function reportingDate(): Attribute
    {
        return Attribute::make(
            set: function ($value) {


                $newDate = Carbon::parse($value);
                return  $newDate->format("Y-m-d");
            }
        );
    }
    protected function siteCode(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return strtoupper($value);
            }
        );
    }
}
