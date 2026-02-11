<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;

    protected $table = 'asset_categories';

    
    protected $primaryKey = 'id';

    protected $fillable = [
        'reference_number', 
        'english_name', 
        'arabic_name', 
        'is_depreciable', 
        'depreciation_method', 
        'useful_life', 
        'useful_life_unit', 
        'asset_account', 
        'depreciation_expense_account', 
        'accumulated_depreciation_account', 
        'manual_depreciation', 
        'recorded_depreciation',
        'created_by',
    ];

    public function assetAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'asset_account');
    }

    public function depreciationExpenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'depreciation_expense_account');
    }

    public function accumulatedDepreciationAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'accumulated_depreciation_account');
    }
}
