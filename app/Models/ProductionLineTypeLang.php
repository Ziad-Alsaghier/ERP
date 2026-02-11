<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLineTypeLang extends Model
{
    use HasFactory;


    protected $table = 'production_line_type_lang';
    protected $fillable = [
        'lang',
        'type_id',
        'name'
    ];


}
