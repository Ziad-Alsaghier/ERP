<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'unit_id',
        'type',
        'created_by',
    ];

    protected $table = 'attributes';
    protected $appends = ['name'];
    public function options()
    {
        return  $this->hasMany(AttributeSelect::class, 'attr_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function lang()
    {
        $l = app()->getLocale();
        return productServiceAttributeLang::where('attribute_id', $this->id)->where('lang', $l)->first();
    }
    public function langs()
    {
        return $this->hasMany(productServiceAttributeLang::class, 'attribute_id');
    }


    public function getNameAttribute()
    {

        return $this?->lang()?->name;
    }
}
