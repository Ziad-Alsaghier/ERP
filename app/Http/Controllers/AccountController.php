<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountLang;
use App\Models\Language;
use App\Models\Utility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    //
    protected $account;
    public function construct(Account $account)
    {
        $this->account = $account;
    }
    public function jsTreeJson(Request $request)
    {
        $parentId = $request->get('parentId');

        $accounts = Account::where('parent_id', $parentId)->orderBy('acc_code')->get();

        $result = [];

        foreach ($accounts as $account) {
            $hasChildren = Account::where('parent_id', $account->id)->exists();

            $result[] = [
                'id' => $account->id,
                'children' => $hasChildren,
                'text' => "
                <strong>{$account->acc_code}</strong> - {$account->name}",
                'icon' => $hasChildren ? 'fa fa-folder' : 'fa fa-file',
                'link' => "<strong>{$account->acc_code}</strong> - {$account->name}
                <a href='#' class='edit-icon' data-id='{$account->id}'>
                    <i class='fas fa-edit text-primary ml-2'></i>
                </a>",
                'data' => [
                    'balance' => $account->balance,
                    'type' => $account->type,
                    'created_at' => $account->created_at->toDateString(),
                    'edit_url' => route('account.edit', $account->id),
                ],
                'title' => $account->name,
                'state' => [
                    'opened' => false,
                    'disabled' => false,
                    'selected' => false,
                ],

            ];
        }
        return response()->json($result);
    }



    public function treePage()
    {

        // Return View
        $allAccounts = Account::get();


        $buildTree = function ($elements, $parentId = null) use (&$buildTree) {
            $branch = [];

            foreach ($elements as $element) {
                if ($element['parent_id'] == $parentId) {
                    $children = $buildTree($elements, $element['id']);
                    $node = [
                        'id' => $element['id'],
                        'text' => $element['name'],
                    ];
                    if ($children)
                        $node['children'] = $children;

                    $branch[] = $node;
                }
            }

            return $branch;
        };

        $account_tree = $buildTree($allAccounts->toArray());
        return view('accounts.tree', compact('account_tree'));
    }
    public function create()
    {
        // Used for dropdowns or other places — DON'T TOUCH
        $accounts = Account::where('created_by', Auth::user()->creatorId())
            ->whereNull('parent_id')
            ->get();

        $account_type = $accounts->pluck('id');
        $allAccounts = Account::where('created_by', Auth::user()->creatorId())
            ->get(['id', 'parent_id']);

        $buildTree = function ($elements, $parentId = null) use (&$buildTree) {
            $branch = [];

            foreach ($elements as $element) {
                if ($element['parent_id'] == $parentId) {
                    $children = $buildTree($elements, $element['id']);
                    $node = [
                        'id' => $element['id'],
                        'text' => $element['name'],
                    ];
                    if ($children) {
                        $node['children'] = $children;
                    }
                    $branch[] = $node;
                }
            }

            return $branch;
        };

        $account_tree = $buildTree($allAccounts->toArray());

        // start for ai module
        $plan = Utility::getChatGPTSettings();
        // ✅ Return everything
        return view('accounts.create', compact('plan', 'account_type', 'accounts', 'account_tree'));
    }

    function flattenTree($elements, $parentId = null, $prefix = '')
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $branch[$element['id']] = $prefix . $element['name'];
                $children = $this->flattenTree($elements, $element['id'], $prefix . '— ');
                $branch += $children;
            }
        }

        return $branch;
    }

    function buildTree($elements, $parentId = null)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $element['text'] = $element['name']; // required for jsTree
                $element['id'] = $element['id'];     // jsTree node ID
                $branch[] = $element;
            }
        }

        return $branch;
    }
    public function store(Request $request)
    {
        if (Auth::user()->can('create chart of account')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'is_enabled' => 'required',
                'parent_id' => 'nullable|exists:accounts,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            $data = $validator->validated();
            $data['created_by'] = Auth::user()->creatorId();
            $data['updated_by'] = $data['created_by']; // <-- fix for foreign key
            $data['is_enabled'] = $request->is_enabled ?? null;

            $newAccount = Account::create($data);

            // Insert multilingual
            $languages = Language::get();
            $langData = [];
            foreach ($languages as $language) {
                $langData[] = [
                    'account_id' => $newAccount->id ?? null,
                    'lang' => $language->code ?? null,
                    'name' => $data['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            AccountLang::insert($langData);

            return redirect()->route('account.index')->with('success', __('Account successfully created.'));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }







    public function move(Request $request)
    {
        //    $account =  Account::findOrFail($request->parent_id);
        //      if (!Auth::user()->can('edit chart of account')) {
        //     return response()->json(['error' => __('Permission denied.')], 401);
        // }
        //     $data = $request->only(['name', 'taxable', 'parent_id']);
        //     if (!$account) {
        //         return redirect()->route('account.index')->with('error', __('Account Not Found.'));
        //     }
        //         // ✅ If parent_id has changed, regenerate acc_code
        //         $parent_id = $request->parent_id ?? Null;
        //         // ✅ Optionally: recursively update child codes
        //         $account->update($data); // save before updating children
        return response()->json(['message' => 'Account Tree Updating Successfullysuccess']);
    }
    public function edit(Request $request)
    {
        // Get Account Need Edit
        $chartOfAccount = Account::find($request->accountId);
        $parents = Account::get()->pluck('id'); // Get Account needed Updated
        $parents->prepend('Select Account Type', Null); // Select Types
        // Return View
        $allAccounts = Account::where('created_by', Auth::user()->creatorId())
            ->get(['id', 'parent_id']);

        $buildTree = function ($elements, $parentId = null) use (&$buildTree) {
            $branch = [];
            foreach ($elements as $element) {
                if ($element['parent_id'] == $parentId) {
                    $children = $buildTree($elements, $element['id']);
                    $node = [
                        'id' => $element['id'],
                        'text' => $element['name'],
                    ];
                    if ($children) {
                        $node['children'] = $children;
                    }
                    $branch[] = $node;
                }
            }
            return $branch;
        };

        $account_tree = $buildTree($allAccounts->toArray());

        return view('accounts.edit', [
            'parents' => $parents,
            'chartOfAccount' => $chartOfAccount,
            'account_tree' => $account_tree,
        ]);
    }
    public function update(Account $account, Request $request)
    {

        if (!Auth::user()->can('edit chart of account')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $data = $request->only(['name', 'taxable', 'parent_id']);

        if (!$account) {
            return redirect()->route('account.index')->with('error', __('Account Not Found.'));
        }

        // ✅ If parent_id has changed, regenerate acc_code
        $parent_id = $request->parent_id ?? Null;
        // ✅ Optionally: recursively update child codes
        $account->update($data); // save before updating children

        // ✅ Update current language name
        $lang = app()->getLocale();
        AccountLang::updateOrCreate(
            ['account_id' => $account->id, 'lang' => $lang],
            ['name' => $data['name']]
        );

        return redirect()->route('account.index')->with('success', __('Account successfully updated.'));
    }



}
