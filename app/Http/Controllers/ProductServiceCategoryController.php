<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\Language;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\ProductServiceCategoryLang;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductServiceCategoryController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage constant category')) {
            $categories = ProductServiceCategory::where('created_by', '=', Auth::user()->creatorId())->get();

            return view('productServiceCategory.index', compact('categories'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('create constant category')) {
            $types = ProductServiceCategory::$catTypes;
            $type = ['' => 'Select Category Type'];
            $types = array_merge($type, $types);
            $chart_accounts = ChartOfAccount::select(DB::raw('CONCAT(code, " - ", name) AS code_name, id'))
                ->where('created_by', Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
            $chart_accounts->prepend('Select Account', '');
            $parents = ProductServiceCategory::where('created_by', '=', Auth::user()->creatorId())
                ->whereNull('parent_id')
                ->get();
            $parents = $parents->pluck('name', 'id');
            $parents->prepend('Select Parent', Null);
            return view('productServiceCategory.create', compact('parents', 'types', 'chart_accounts'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('create constant category')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:200',
                    'type' => 'required',
                    'color' => 'required',
                    'image' => 'required|max:2048',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if (isset($request->default)) {
                $request->default = 1;
            } else {
                $request->default = 0;
            }
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('categories', 'public');
                // Do not overwrite request data
                // Instead, assign it to a variable or model
                $imagePath = $path;
            }

            try {
                $category = new ProductServiceCategory();
                $category->color = $request->color;
                $category->type = $request->type;
                $category->parent_id = $request->parent_id;
                $category->def = $request->default;
                $category->chart_account_id = !empty($request->chart_account) ? $request->chart_account : 0;
                $category->created_by = Auth::user()->creatorId();


                $category->save();
                $languages = Language::langs();

                foreach ($languages as $lang) {
                    $categoryLang = new ProductServiceCategoryLang();
                    $categoryLang->cat_id = $category->id;
                    $categoryLang->lang = $lang->code;
                    $categoryLang->name = $request->name;
                    $categoryLang->save();
                }
            } catch (\Exception $ex) {
                throw new \Exception('Something went wrong with adding category');
            }

            return redirect()->route('product-category.index')->with('success', __('Category successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {

        if (Auth::user()->can('edit constant category')) {
            $types = ProductServiceCategory::$catTypes;
            $category = ProductServiceCategory::find($id);
            // dd($category);
            $parents = ProductServiceCategory::where('created_by', '=', Auth::user()->creatorId())->get();

            $parents = $parents->pluck('name', 'id');
            $parents->prepend('Append Category In Parent', '');

            return view('productServiceCategory.edit', compact('parents', 'category', 'types'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        $lang = App::getLocale();

        if (!Auth::user()->can('edit constant category')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $category = ProductServiceCategory::find($id);
        if (!$category || $category->created_by != Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:200',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        // New Feature ⭐ Add Parent
        $category->parent_id = $request->parent ?? $category->parent_id;

        $category->def = $request->default ?? 0;

        if ($request->hasFile('image')) {
            // ✅ Delete old image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            // ✅ Upload new image
            $path = $request->file('image')->store('categories', 'public');
            $category->image = $path;
        }

        $category->save();

        $categoryLang = ProductServiceCategoryLang::where('cat_id', $category->id)
            ->where('lang', $lang)
            ->first();

        if ($categoryLang) {


            $categoryLang->update(['name' => $request->name]);
        }

        return redirect()->route('product-category.index')->with('success', __('Category successfully updated.'));
    }

    public function destroy($id)
    {
        if (Auth::user()->can('delete constant category')) {
            $category = ProductServiceCategory::find($id);
            if ($category->created_by == Auth::user()->creatorId()) {

                if ($category->type == 0) {
                    $categories = ProductService::where('category_id', $category->id)->first();
                } elseif ($category->type == 1) {
                    $categories = Invoice::where('category_id', $category->id)->first();
                } else {
                    $categories = Bill::where('category_id', $category->id)->first();
                }

                if (!empty($categories)) {
                    return redirect()->back()->with('error', __('this category is already assign so please move or remove this category related data.'));
                }
                if ($category->hasChildren()) {
                    return redirect()->back()->with('error', __('This Category Can\'t be Deleted Cause Has Children.'));
                }
                $category->delete();

                return redirect()->route('product-category.index')->with('success', __('Category successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getProductCategories()
    {
        $cat = ProductServiceCategory::getallCategories();

        $html = '<div class="mb-3 mr-2 zoom-in ">
                  <div class="card rounded-10 card-stats mb-0 cat-active overflow-hidden" data-id="0">
                     <div class="category-select" data-cat-id="0">
                        <button type="button" class="btn tab-btns btn-primary">' . __("All Categories") . '</button>
                     </div>
                  </div>
               </div>';
        foreach ($cat as $key => $c) {
            $dcls = 'category-select';
            $html .= ' <div class="mb-3 mr-2 zoom-in cat-list-btn">
                          <div class="card rounded-10 card-stats mb-0 overflow-hidden " data-id="' . $c->id . '">
                             <div class="' . $dcls . '" data-cat-id="' . $c->id . '">
                                <button type="button" class="btn tab-btns btn-primary">' . $c->name . '</button>
                             </div>
                          </div>
                       </div>';
        }
        return Response($html);
    }

    public function getAccount(Request $request)
    {

        $chart_accounts = [];
        if ($request->type == 'income') {
            $chart_accounts = ChartOfAccount::select(DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'Revenue')
                ->where('parent', '=', 0)
                ->where('chart_of_accounts.created_by', Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
        } elseif ($request->type == 'expense') {
            $chart_accounts = ChartOfAccount::select(DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'Expenses')
                ->where('parent', '=', 0)
                ->where('chart_of_accounts.created_by', Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
        } elseif ($request->type == 'assets') {
            $chart_accounts = ChartOfAccount::select(DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'Assets')
                ->where('parent', '=', 0)
                ->where('chart_of_accounts.created_by', Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
        } elseif ($request->type == 'liability') {
            $chart_accounts = ChartOfAccount::select(DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'Liability')
                ->where('parent', '=', 0)
                ->where('chart_of_accounts.created_by', Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
        } elseif ($request->type == 'equity') {
            $chart_accounts = ChartOfAccount::select(DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                ->where('chart_of_account_types.name', 'Equity')
                ->where('parent', '=', 0)
                ->where('chart_of_accounts.created_by', Auth::user()->creatorId())->get()
                ->pluck('code_name', 'id');
        } else {
            $chart_accounts = 0;
        }

        $subAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account');
        $subAccounts->leftjoin('chart_of_account_parents', 'chart_of_accounts.parent', 'chart_of_account_parents.id');
        $subAccounts->where('chart_of_accounts.parent', '!=', 0);
        $subAccounts->where('chart_of_accounts.created_by', Auth::user()->creatorId());
        $subAccounts = $subAccounts->get()->toArray();

        $response = [
            'chart_accounts' => $chart_accounts,
            'sub_accounts' => $subAccounts,
        ];

        return response()->json($response);
    }
}
