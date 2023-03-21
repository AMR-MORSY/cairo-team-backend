<?php

namespace App\Models\Sites;

use App\Models\Modifications\Modification;
use App\Models\Nodal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    protected $table="sites";
    protected $guarded=[];

    public function nodal()
    {
        return $this->hasOne(Nodal::class,"site_code","site_code");
    }
    public function modifications()
    {
        return $this->hasMany(Modification::class,"site_code","site_code");
    }
}
