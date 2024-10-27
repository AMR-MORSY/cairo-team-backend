<?php

namespace App\Models\NUR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FM2G extends Model
{
    use HasFactory;
    protected $table="fm2gnurs";
    protected $guarded=[];

    protected function subSystem(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
        );
    }
    protected function oz(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
        );
    }
    protected function solution(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucwords($value),
        );
    }
}
