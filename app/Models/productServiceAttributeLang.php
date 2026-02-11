<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productServiceAttributeLang extends Model
{
    use HasFactory;

    protected $table ='attributes_lang';
    protected $fillable = [
        'attribute_id',
        'name',
        'lang'
    ];
}
