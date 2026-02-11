<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductServiceImage extends Model
{
    use HasFactory;
        protected $table = 'product_service_images';
        protected $fillable = ['product_service_id', 'image'];

    public function productService()
    {
        return $this->belongsTo(ProductService::class);
    }


}
