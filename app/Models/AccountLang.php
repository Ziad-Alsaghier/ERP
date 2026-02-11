<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountLang extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'account_id',
        'lang',
    ];
    protected $table = "account_langs";

}

