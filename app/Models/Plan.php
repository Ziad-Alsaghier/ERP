<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{

    protected $fillable = [
        'name',
        'price',
        'duration',
        'max_users',
        'max_customers',
        'max_venders',
        'max_clients',
        'user_price',
        'customers_price',
        'storage_price',
        'venders_price',
        'clients_price',
        'trial',
        'trial_days',
        'description',
        'image',
        'crm',
        'hrm',
        'account',
        'project',
        'pos',
        'manfuc',
        'chatgpt',
        'storage_limit',
        'is_disable',
        'is_visible',
    ];

    private static $getplans = NULL;

    public static $arrDuration = [
        'month' => 'Per Month',
        'year' => 'Per Year',
        'one time' => 'One Time',
        'month-ex' => 'Month-ex',
        'year-ex' => 'Year-ex',
        'lifetime' => 'Lifetime'

    ];

    public function status()
    {
        return [
            __('lifetime'),
            __('Per Month'),
            __('Per Year'),
        ];
    }

    public static function total_plan()
    {
        return Plan::count();
    }

    public static function most_purchese_plan()
    {
//        $free_plan = Plan::where('price', '<=', 0)->first()->id;
        $plan =  User::select(DB::raw('count(*) as total') , 'plan')->where('type', '=', 'company')->groupBy('plan')->first();

        return $plan;
    }

    public static function getPlan($id)
    {
        if(self::$getplans == null)
        {
            $plan = Plan::find($id);
            self::$getplans = $plan;
        }
        return self::$getplans;
    }


    protected $appends = ['countusers'];
    public function getCountusersAttribute()
    {
        return $this->countUsers();
    }
    public function countUsers()
    {
        return User::where('plan', $this->id)
                    ->count();
    }

}
