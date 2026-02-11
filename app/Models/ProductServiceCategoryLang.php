<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductServiceCategoryLang extends Model
{
    use HasFactory;
    protected $fillable = [
            'name'
    ];
   protected  $table = 'product_service_categories_lang';
   public $timestamps = false;




}
