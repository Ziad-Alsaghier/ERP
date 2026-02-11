<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLineProduct extends Model
{
    use HasFactory;
    protected     $table = 'production_line_product';
    protected     $fillable = [
        'line_id',
        'product_id',
    ];


    public function products(){
        return $this->belongsTo(ProductService::class, 'product_id');
    }



    
}
