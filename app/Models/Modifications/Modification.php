<?php

namespace App\Models\Modifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modification extends Model
{
    use HasFactory;

    protected $table="modifications";
    protected $guarded=[];

    public function site()
    {
        return $this->belongsTo(Site::class,"site_code","site_code"); 
    }
}
