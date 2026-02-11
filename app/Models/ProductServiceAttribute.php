<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductServiceAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attr_id',
        'attr_value',
        'value_mode',
        'is_dynamic'
    ];

    protected $appends =['name'];
    protected $table = 'product_service_attributes';


    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'atrributer_id');
    }

}
