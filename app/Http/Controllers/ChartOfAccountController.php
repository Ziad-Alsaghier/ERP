<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountSubType;
use App\Models\ChartOfAccountType;
use App\Models\User;
use App\Models\Utility;
use App\Models\JournalItem;
use App\Models\ChartOfAccountParent;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{

    public function index(Request $request)
    {

        if(\Auth::user()->can('manage chart of account'))
        {
            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start = $request->start_date;
                $end = $request->end_date;
            } else {
                $start = date('Y-01-01');
                $end = date('Y-m-d', strtotime('+1 day'));
            }
            $filter['startDateRange'] = $start;
            $filter['endDateRange'] = $end;
            $types = ChartOfAccountType::where('created_by', '=', \Auth::user()->creatorId())->get();
            $chartAccounts = ChartOfAccount::select(
                \DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name'),
                'chart_of_accounts.id',
                'chart_of_accounts.code',
                'chart_of_accounts.name',
                'chart_of_accounts.parent',
                'chart_of_accounts.type',
                'chart_of_accounts.is_enabled',
                'chart_of_account_types.name as type_name',
                'chart_of_account_sub_types.name as sub_type_name'
            )
            ->join('chart_of_account_types', 'chart_of_accounts.type', '=', 'chart_of_account_types.id')
            ->join('chart_of_account_sub_types', 'chart_of_accounts.sub_type', '=', 'chart_of_account_sub_types.id') // Add the join to the sub-types table
            ->where('chart_of_accounts.parent', '=', 0)
            ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
            ->get()
            ->toArray();

                // ترتيب الحقول الجدول بشكل صحيح
            usort($chartAccounts, function ($a, $b) {
                return $a['id'] <=> $b['id'];
            });


            $groupedChartAccounts = [];
            foreach ($chartAccounts as $chartAccount) {
                $groupedChartAccounts[$chartAccount['type_name']][] = $chartAccount;
            }

            // dd($subAccounts);
            $subAccounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name,
            chart_of_accounts.id,
            chart_of_accounts.code,
            chart_of_accounts.name,
            chart_of_accounts.is_enabled,
            chart_of_account_parents.account,
            chart_of_account_sub_types.name as sub_type_name
            '))
            ->join('chart_of_account_sub_types', 'chart_of_accounts.sub_type', '=', 'chart_of_account_sub_types.id')
            ->leftJoin('chart_of_account_parents', 'chart_of_accounts.parent', '=', 'chart_of_account_parents.id')
            ->where('chart_of_accounts.parent', '!=', 0)
            ->where('chart_of_accounts.created_by', \Auth::user()->creatorId())
            ->orderBy('chart_of_accounts.code') // ترتيب حسب العمود code
            ->get()
            ->toArray();
            // dd($subAccounts);
            return view('chartOfAccount.index', compact('groupedChartAccounts','chartAccounts','subAccounts', 'types' , 'filter'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function create()
    {
        $types = ChartOfAccountType::where('created_by', \Auth::user()->creatorId())->get();

        $account_type = [];

        foreach ($types as $type) {
            $accountTypes = ChartOfAccountSubType::where('type', $type->id)
                ->where('created_by', \Auth::user()->creatorId())
                ->orderBy('id')
                ->get();

            $temp = [];
            foreach ($accountTypes as $accountType) {
                $temp[$accountType->id] = __($accountType->name);
            }
            $account_type[__($type->name)] = $temp;
        }

        $selectAcc = [null => __(" --- Select Account ---")];
        $account_type = array_merge($selectAcc, $account_type);
        ksort($account_type);
        $account_type = Account::getParent();

        // Advanced children retrieval: build a tree structure


        return view('chartOfAccount.create', compact('account_type'));
    }



    public function store(Request $request)
    {

        if(\Auth::user()->can('create chart of account'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'sub_type' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $type = ChartOfAccountSubType::where('id',$request->sub_type)->where('created_by', '=', \Auth::user()->creatorId())->first();
            $account = ChartOfAccount::where('id',$request->parent)->where('created_by', '=', \Auth::user()->creatorId())->first();
            if($account !== null){
                $existingparentAccount = ChartOfAccountParent::where('name',$account->name)->where('created_by',\Auth::user()->creatorId())->first();
            }
            if (isset($existingparentAccount)) {
                $parentAccount = $existingparentAccount;
            } else {
                $parentAccount              = new ChartOfAccountParent();
            }
            if($account !== null){
                $parentAccount->name        = $account->name;
                $parentAccount->sub_type    = $request->sub_type;
                $parentAccount->type        = $type->type;
                $parentAccount->account      = $request->parent;
                $parentAccount->created_by  = \Auth::user()->creatorId();
                $parentAccount->save();
                }else{
                    $parentAccount->id=0;
                }
            $account              = new ChartOfAccount();
            $account->name        = $request->name;
            $account->code        = $request->code;
            $account->type        = $type->type;
            $account->sub_type    = $request->sub_type;
            $account->parent      = $parentAccount->id;
            $account->description = $request->description;
            $account->is_enabled  = isset($request->is_enabled) ? 1 : 0;
            $account->created_by  = \Auth::user()->creatorId();
            $account->save();

            return redirect()->route('chart-of-account.index')->with('success', __('Account successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show(ChartOfAccount $chartOfAccount,Request $request)
    {
        if(\Auth::user()->can('ledger report'))
        {
            if(!empty($request->start_date) && !empty($request->end_date))
            {
                $start = $request->start_date;
                $end   = $request->end_date;
            }
            else
            {
                $start = date('Y-m-01');
                $end   = date('Y-m-t');
            }
            if(!empty($request->start_date) && !empty($request->end_date))
            {
                $accounts = ChartOfAccount::select(\DB::raw('CONCAT(code, " - ", name) AS code_name, id'))
                    ->where('created_by', \Auth::user()->creatorId())
                    ->where('created_at', '>=', $start)
                    ->where('created_at', '<=', $end)
                    ->get()->pluck('code_name', 'id');
                $accounts->prepend('Select Account', '');

            }
            else
            {
                $accounts = ChartOfAccount::select(\DB::raw('CONCAT(code, " - ", name) AS code_name, id'))
                    ->where('created_by', \Auth::user()->creatorId())->get()
                    ->pluck('code_name', 'id');
                $accounts->prepend('Select Account', '');
            }
            if(!empty($request->account))
            {
                $account = ChartOfAccount::find($request->account);
            }
            else
            {
                $account = ChartOfAccount::find($chartOfAccount->id);
            }

            // $journalItems = JournalItem::select('journal_entries.journal_id', 'journal_entries.date as transaction_date', 'journal_items.*')
            //     ->leftjoin('journal_entries', 'journal_entries.id', 'journal_items.journal')
            //     ->where('journal_entries.created_by', '=', \Auth::user()->creatorId())
            //     ->where('account', !empty($account) ? $account->id : 0);
            // $journalItems->where('date', '>=', $start);
            // $journalItems->where('date', '<=', $end);
            // $journalItems = $journalItems->get();

            $balance = 0;
            $debit   = 0;
            $credit  = 0;

            // foreach($journalItems as $item)
            // {
            //     if($item->debit > 0)
            //     {
            //         $debit += $item->debit;
            //     }

            //     else
            //     {
            //         $credit += $item->credit;
            //     }

            //     $balance = $credit - $debit;
            // }

            $filter['startDateRange'] = $start;
            $filter['endDateRange']   = $end;

            return view('chartOfAccount.show', compact('filter', 'account', 'accounts'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function edit(ChartOfAccount $chartOfAccount)
    {
        $types = ChartOfAccountType::get()->pluck('name', 'id');
        $types->prepend('Select Account Type', Null);

        return view('chartOfAccount.edit', compact('chartOfAccount', 'types'));
    }


    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {

        if(\Auth::user()->can('edit chart of account'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }


            $chartOfAccount->name        = $request->name;
            $chartOfAccount->code        = $request->code;
            $chartOfAccount->description = $request->description;
            $chartOfAccount->is_enabled  = isset($request->is_enabled) ? 1 : 0;
            $chartOfAccount->save();



            return redirect()->route('chart-of-account.index')->with('success', __('Account successfully updated.'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function destroy(ChartOfAccount $chartOfAccount)
    {
        if(\Auth::user()->can('delete chart of account'))
        {
            $chartOfAccount->delete();

            return redirect()->route('chart-of-account.index')->with('success', __('Account successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getSubType(Request $request)
    {
        $types = ChartOfAccount::where('sub_type', $request->type)->get()->mapWithKeys(function ($item) {
            // جلب الاسم الخاص بالـ parent
            $parentName = ChartOfAccountParent::where('id', $item->parent)->value('name');

            // دمج اسم الـ parent مع اسم الـ account
            $fullName = $parentName ? __($parentName) . ' - ' . __($item->name) : __($item->name);

            return [$item->id => $fullName];
        });

        $types->prepend(__(' --- Select Account ---'), 0);
        return response()->json($types);
    }




    public function getaccountcode(Request $request)
    {
        $accounts = ChartOfAccount::where('sub_type', $request->account)->where('parent', '=', 0)->get()->pluck('id', 'code');

        $codes = $accounts->keys()->sort()->values();

        $lastCode = $codes->last();


        if ($lastCode !== null) {
            $lastCode = (int)$lastCode + 1;
        } else {
            $lastCode = 0;
        }

        return response()->json($lastCode);

    }
    public function getsubaccountcode(Request $request)
    {
        $accounts = ChartOfAccount::where('id', $request->account)->where('created_by', '=', \Auth::user()->creatorId())->first();
        // dd($accounts->type);
        $accounts_2 = ChartOfAccount::where('code', $accounts->code)->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('code','id');

        $first_code = $accounts_2->first();
        $first_key = $accounts_2->keys()->first();
        // dd($first_key , $first_code);


        $accounts_test = ChartOfAccountParent::where('account', $first_key)->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('id', 'account');
        $first_code_p = $accounts_test->first();
        $first_key_p = $accounts_test->keys()->first();
        // dd($first_code_p,$first_key_p);
        if($first_code_p == null && $first_key_p == null){
            $lastCode = $first_code . '01';
        }else{
            $accounts_3 = ChartOfAccount::where('parent',$first_code_p)->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('id', 'code');
                $codes_2 = $accounts_3->keys()->sort()->values();
                $lastCode_2 = $codes_2->last();
                $lastCode = (string) $lastCode_2 + 1;
            // dd($lastCode);
        }

        return response()->json($lastCode);
    }

}
