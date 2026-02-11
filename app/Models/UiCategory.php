<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UiCategory extends Model
{
    use HasFactory;

            protected $table = 'ui_category';
            protected $fillable = [
                'cat_id',
                'section',
                'is_enabled',
            ];

                    // One To One Relational
       public function category()
    {
         return   $this->belongsTo(ProductServiceCategory::class, 'cat_id');

    }

}
