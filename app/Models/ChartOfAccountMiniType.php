<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccountMiniType extends Model
{

    protected $table = 'chart_of_account_mini_types'; // Change this to a string
    protected $fillable = [
        'name',
        'sub_type',
        'created_by',
    ];
}