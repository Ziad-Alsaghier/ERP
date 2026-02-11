<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccountSubType extends Model
{
    protected $fillable = [
        'name',
        'type',
        'created_by',
    ];

    public function miniType(){
        return $this->hasMany('App\Models\ChartOfAccountMiniType', 'sub_type', 'id');

    }
}
