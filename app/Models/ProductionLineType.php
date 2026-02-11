<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLineType extends Model
{
    use HasFactory;

    protected $table = 'production_line_type';

    protected $fillable = [
       
        'productionLine_id', // Recomended ‼‼
    ];
        protected $appends = ['name'];
            
    public function lang()
    {
        $l = app()->getLocale();
        return ProductionLineTypeLang::where('type_id', $this->id)->where('lang', $l)->first();
    }


    public function getNameAttribute()
    {
        
        return  $this->lang()->name ?? Null;
    }

    public function langs()
    {
        return $this->hasMany(ProductionLineTypeLang::class, 'type_id');
    }
}
