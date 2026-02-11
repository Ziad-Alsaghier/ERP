<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'arabic_name',
        'english_name',
        'reference_number',
        'category',
        'description',
        'measurement_unit',
        'tax_percentage',
        'barcode',
        'asset_image',
        'created_by',
    ];

    public function kind()
    {
        return $this->belongsTo('App\Models\AssetCategory', 'category', 'id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\ProductServiceUnit', 'measurement_unit', 'id');
    }

    public function tax()
    {
        return $this->belongsTo('App\Models\Tax', 'tax_percentage', 'id');
    }
}
