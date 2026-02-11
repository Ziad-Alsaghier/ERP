<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitLang extends Model
{
    use HasFactory;
    protected $fillable = [
        "unit_id",
        "lang",
        "name",
    ];
    protected $table = 'unit_langs';
    public $timestamps = false;

}
