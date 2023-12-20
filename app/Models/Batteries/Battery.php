<?php

namespace App\Models\Batteries;

use App\Models\Sites\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Battery extends Model
{
    protected $table="batteries";
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $guarded=[];

    use HasFactory;

    public function site()
    {
        return $this->belongsTo(Site::class,"site_code");
    }
}
