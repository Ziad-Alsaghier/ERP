<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductServiceLang extends Model
{
    use HasFactory;

    protected $table = 'product_services_lang';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description'   
    ];
}
