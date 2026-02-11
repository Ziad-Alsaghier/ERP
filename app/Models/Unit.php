<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        "name"
    ];

    protected $appends = ['name'];
    protected $table = "units";



    public function productUnit(){
return $this->belongsTo(ProductService::class);
    }


    public function lang()
    {
    $l = app()->getLocale();
    return UnitLang::where('unit_id', $this->id)->where('lang', $l)->first();
    }


    public function getNameAttribute(){

    return $this->lang()->name ;
    }

    public function unitLangs()
    {
        return $this->hasMany(UnitLang::class, 'unit_id');
    }
}
