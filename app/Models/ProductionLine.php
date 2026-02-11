<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLine extends Model
{
    use HasFactory;
    protected     $table = 'production_line';
    protected     $fillable = [
        'type_id',
        'branch_id',
        'cost_center',
        'is_enabled',
    ];
    protected $appends = ['name'];




    public function operators()
    {
        return $this->hasMany(ProductionLineOperator::class, 'line_id');
    }
    public function linProducts()
    {
        return $this->hasMany(ProductionLineProduct::class, 'line_id');
    }
    public function type()
    {
        return $this->belongsTo(ProductionLineType::class , 'type_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class , 'branch_id');
    }


    public function lang()
    {
        $l = app()->getLocale();
        return ProductionLineLang::where('line_id', $this->id)->where('lang', $l)->first();
    }


    public function getNameAttribute()
    {

        return  $this->lang()->name ?? 'Null';
    }

    public function langs()
    {
        return $this->hasMany(ProductionLineLang::class, 'line_id');
    }
}
