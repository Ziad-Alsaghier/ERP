<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'city_zip',
        'created_by',
        'account_id'
    ];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    // should have account_id related to chart of accounts
    public static function warehouse_id($warehouse_name)
    {
        $warehouse = DB::table('warehouses')
        ->where('id', $warehouse_name)
        ->where('created_by', Auth::user()->creatorId())
        ->select('id')
        ->first();

        return ($warehouse != null) ? $warehouse->id : 0;
    }
    public function products()
    {
        return $this->hasMany(WarehouseProduct::class, 'warehouse_id', 'id');
    }
}
