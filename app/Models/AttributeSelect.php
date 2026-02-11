<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeSelect extends Model
{
    use HasFactory;

    protected $fillable = [
        'attr_id',
    'value',
    ];

    protected $table = 'attribute_selects';

    public function lang()
{
$l = app()->getLocale();
return AttributeSelectLang::where('attribute_select_id', $this->id)->where('lang', $l)->first();
}

    public function langs()
    {
        return $this->hasMany(AttributeSelectLang::class, 'attribute_select_id');
    }



    public function getValueAttribute(){
return $this?->lang()?->value ;
}

}
