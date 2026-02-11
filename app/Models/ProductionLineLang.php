<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLineLang extends Model
{
    use HasFactory;

    protected     $table = 'production_line_lang';
    protected $fillable = [
        'line_id',
        'lang',
        'name',
    ];
   

}
