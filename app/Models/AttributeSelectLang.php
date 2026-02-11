<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeSelectLang extends Model
{
    use HasFactory;


    protected $fillable =[
        'attribute_select_id',
        'value',
        'lang'
    ];
    protected $table = 'attribute_select_langs';


}
