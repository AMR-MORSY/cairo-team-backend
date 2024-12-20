<?php

namespace App\Models\Modifications;

use App\Models\Sites\Site;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Modification extends Model 
{
    use HasFactory;
    use LogsActivity;

    protected $table="modifications";
    protected $guarded=[];

    public function site()
    {
        return $this->belongsTo(Site::class,"site_code","site_code"); 
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(["*"])
        ->useLogName('modifications');
        // Chain fluent methods for configuration options
    }
}
