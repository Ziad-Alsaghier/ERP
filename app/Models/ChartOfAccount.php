<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'sub_type',
        'mini_type',
        'parent',
        'is_enabled',
        'description',
        'created_by',
    ];

    public function types()
    {
        return $this->hasOne('App\Models\ChartOfAccountType', 'id', 'type');
    }

    public function accounts()
    {
        return $this->hasOne('App\Models\JournalItem', 'account', 'id');
    }

    public function balance()
    {
        $journalItem         = JournalItem::select(\DB::raw('sum(credit) as totalCredit'),
            \DB::raw('sum(debit) as totalDebit'),
            \DB::raw('sum(credit) - sum(debit) as netAmount'))->where('account', $this->id);
        $journalItem         = $journalItem->first();
        $data['totalCredit'] = $journalItem->totalCredit;
        $data['totalDebit']  = $journalItem->totalDebit;
        $data['netAmount']   = $journalItem->netAmount;

        return $data;
    }

    public function subType()
    {
        return $this->hasOne('App\Models\ChartOfAccountSubType', 'id', 'sub_type');
    }

    public function parentAccount()
    {
        return $this->hasOne('App\Models\ChartOfAccountParent', 'id', 'parent');
    }

    public function miniType (){
        return $this->hasOne('App\Models\ChartOfAccountMiniType', 'id', 'mini_type');
    }

    public function getBalanceAccount($account , $startDate , $endDate){
        return Utility::getAccountBalance($account->id, $startDate, $endDate);
    }

    // important

    public static function getAnAcount($name)
    {
        $account = self::where('name', $name)->where('created_by' , \Auth::id())->first();
        return $account ? $account->id : null;
    }
}
