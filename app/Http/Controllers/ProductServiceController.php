<?php

namespace App\Http\Controllers;

use Form;
use Exception;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vender;
use App\Models\Product;
use App\Models\Utility;
use App\Models\Language;
use Carbon\Traits\Units;
use App\Models\warehouse;
use App\Models\CustomField;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\ProductService;
use Illuminate\Validation\Rule;
use App\Services\AccountsService;
use App\Models\ChartOfAccountType;
use App\Models\ProductServiceLang;
use App\Models\ProductServiceUnit;
use Illuminate\Support\Facades\DB;
use App\Models\ProductCategoryLang;
use Illuminate\Support\Facades\App;
use Illuminate\Support\FacadesAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductServiceExport;
use App\Imports\ProductServiceImport;
use App\Models\ProductServiceCategory;
use Illuminate\Support\Facades\Storage;
use App\Models\Attribute;
use Illuminate\Support\Facades\Validator;
use App\Models\productServiceAttributeLang;
use App\Models\AttributeSelect;
use App\Models\AttributeSelectLang;
use App\Models\ProductServiceAttribute;
use App\Models\ProductServiceImage;
use App\Models\WarehouseProduct;
use Stripe\Service\Climate\ProductService as ClimateProductService;

class ProductServiceController extends Controller
{

    protected $accountService;

    // Inject the AccountService using the constructor
    public function __construct(AccountsService $accountService)
    {
        $this->accountService = $accountService;
    }
    public function index(Request $request)
    {

        if (Auth::user()->can('manage product & service')) {
            $category = ProductServiceCategory::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', 'product & service')->get()->pluck('name', 'id');
            $category->prepend(__('Select Category'), '');
            $warehouseProduct = warehouse::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $warehouseProduct->prepend(__('Select Warehouse'), '');


            if (!empty($request->category)) {
                $productServices = ProductService::where('created_by', '=', Auth::user()->creatorId())->where('category_id', $request->category)->with(['category', 'unit'])->get();
            } else {
                $productServices = ProductService::where('created_by', '=', Auth::user()->creatorId())->with(['category', 'unit'])->get();
            }

            $parents = ProductService::where('created_by', '=', Auth::user()->creatorId())->whereNull('parent_id')->get();
            $parents = $parents->pluck('name', 'id');
            $parents->prepend('Select Parent Product', '');
            return view('productservice.index', compact('parents', 'productServices', 'category', 'warehouseProduct'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function attribute()
    {
        $attributes = Attribute::where('created_by', '=', Auth::user()
            ->creatorId())
            ->with(['options', 'unit'])
            ->get(); // Get Attributes
        return view('productservice.attribute.attribute', compact('attributes')); // Show Interface
    }

    public function ajaxList()
    {
        $attributes = Attribute::with(['options', 'unit'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($attributes);
    }

    public function attributeCreate()
    {

        if (Auth::user()->can('create product & service')) { // Check If User Have Permessions for This Action
            $types = [
                'Select Field' => null,
                'Select' => 'select',
                'Numeric' => 'numeric'
            ]; // Fields Type Pass For View
            $units = Unit::where('created_by', '=', Auth::user()->creatorId())->get(); // Get units
            $units = $units->pluck('name', 'id'); // specification Attributes
            $units->prepend('N/A', null);

            return view('productservice.attribute.attribute_create', compact('types', 'units')); // Return View

        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function attributeStore(Request $request)
    {
        $data = $request->all();
        if (Auth::user()->can('create product & service')) {
            $rules = [
                'attribute_name' => ['required', 'min:4'],
                'type' => ['required', 'min:5'],
                'unit' => 'sometimes',
                'options.*' => 'required_if:type,select',

            ]; // Make Rule For Request
            $validator = Validator::make($data, $rules); // Validate Request Data
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return response()->json(['error' => $messages], 400);
            } else {

                $attribute = Attribute::create([
                    'created_by' => Auth::user()->creatorId(),
                    'type' => $data['type'],
                    'unit_id' => $data['unit'],
                ]); // Create New Attribute
                $attributeLang = new  productServiceAttributeLang();
                $langData = [
                    'attribute_id' => $attribute->id,
                    'name' => $data['attribute_name']
                ];
                buildMultiLangData($attributeLang, $langData); // Use Helper Function To Create Languages
                $options = $request->only('options');
                if ($options['options']) { // Create options if it has
                    foreach ($options['options'] as $option) {
                        if ($option !== Null) {
                            // Start Create Options 
                            $createdOption =  AttributeSelect::create(
                                [
                                    'attr_id' => $attribute->id,
                                    'value' => $option
                                ]
                            ); // End Create Options 
                            if ($createdOption) {
                                // Start Handl Data For Create Multi-Language
                                $optionLang = [
                                    'attribute_select_id' => $createdOption->id,
                                    'value' => $option,
                                ];
                                // Create Multi-Languages
                                multi_languages($createdOption, $optionLang);
                            }
                        } else {
                            $attribute->options()->create(['value' => 'Number']);
                        }
                    }
                }
            }
            Notification::create([ // Start Notify
                'creator_id' => Auth::user()->creatorId(),
                'user_id' => Auth::user()->id,
                'type' => 'product & attribute',
                'data' => json_encode([
                    'action' => 'delete',
                    'name' => $data['attribute_name'],
                ]),
                'is_read' => 0,
            ]);
            return response()->json(['success' => __('Product Attributes successfully created.')], 200);
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function editAttribute($id)
    {
        $Attribute = Attribute::with('unit')->find($id); // Get Attribute Need Update
        if (Auth::user()->can('edit product & service')) {
            if ($Attribute->created_by == Auth::user()->creatorId()) {
                $units = Unit::where('created_by', '=', Auth::user()->creatorId())->get(); // Get Unit
                $units = $units->pluck('name', 'id'); // Spicification Attributes
                $units->prepend('N/A', null);
                $Attribute->unit; // Get relational Data

                $Attribute->options; // Get relational Data
                return view('productservice.attribute.edit', compact('Attribute', 'units')); // Return View Modale

            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function updateAttribute(Request $request, Attribute $attribute)
    { // Method Action To Update and Get The row From Model
        if (Auth::user()->can('edit product & service')) {
            if ($attribute->created_by == Auth::user()->creatorId()) {
                $attributeLang = new productServiceAttributeLang();
                if ($attribute->type !== 'select') {
                    $type = $attribute->type; // Get Type

                    if ($type === 'select') {
                        $options = $request->input('options'); // associative array: [id => value]
                        $lang = app()->getLocale();
                        // CASE-1 : If Delete Options 
                        $toDeleteIds = $request->input('deleted_option_ids', []);
                        if (!empty($toDeleteIds)) {
                            AttributeSelect::whereIn('id', $toDeleteIds)
                                ->where('attr_id', $attribute->id)
                                ->delete();
                        } else {
                            if (!empty($options)) {
                                $attributeOptions = $attribute->options()->pluck('id')->toArray();
                                $optionsIds = array_keys($options);
                                foreach ($options as $key => $value) {
                                    if (is_numeric($key) && in_array($key, $attributeOptions)) {
                                        // ✅ Update existing option
                                        $attribute->options()->where('id', $key)->update([
                                            'value' => $value,
                                        ]);
                                        $lang = app()->getLocale();
                                        $updateLang = AttributeSelectLang::where('attribute_select_id', $key)->where('lang', $lang)->update(['value' => $value]);
                                    } else {
                                        // ✅ Create new option (no matching ID in DB)
                                        $newOptions =   $attribute->options()->create([
                                            'value' => $value,
                                        ]);
                                        $optionLang = new AttributeSelectLang();
                                        $data = [
                                            'attribute_select_id' => $newOptions->id,
                                            'value' => $value
                                        ];
                                        buildMultiLangData($optionLang, $data);
                                    }
                                }

                                $deletedOptionIds = array_diff($attributeOptions, $optionsIds);
                                if (!empty($deletedOptionIds)) {
                                    $attribute->options()->whereIn('id', $deletedOptionIds)->delete();
                                }


                                $attribute->options();
                            }
                        }
                        // CASE-2

                    } else {
                        return redirect()->route('productservice.attributes')->with('error', __('Options Can\'t Be Empty'));
                    }
                }

                changeMultiLangData($attribute, ['name' => $request->name], 'attribute_id', $attribute->id);
                $attribute->update([
                    'unit_id' => $request->unit_id,
                    'type' => $request->type,
                ]); // Update Language
                return redirect()->route('productservice.attributes')->with('success', __('Attributes successfully Updated.'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }



    public function attributeDestroy(Attribute $attribute)
    {
        if ($attribute->delete()) {
            return redirect()->route('productservice.attributes')->with('success', __('Product Attributes successfully Deleted.'));
        }
    }


    public function datajson(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'code',
            2 => 'name',
            3 => 'parent',
            4 => 'category_id',
            5 => 'unit_id',
            6 => 'type',
        ];

        $search = $request->input('search.value');
        $category = $request->category;
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderCol = $columns[$request->input('order.0.column')] ?? 'id';
        $orderDir = $request->input('order.0.dir') ?? 'asc';

        // Base query with optional filters
        $query = ProductService::with(['parentProducts', 'category', 'unit', 'attributes'])
            ->orderBy('code', 'desc')
            ->when($category, fn($q) => $q->where('category_id', $category))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('id', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
                });
            });

        $totalData = ProductService::when($category, fn($q) => $q->where('category_id', $category))->count();
        $totalFiltered = $query->count();

        $products = $query->orderBy($orderCol, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();

        // Prepare DataTables response
        $data = [];
        foreach ($products as $product) {
            $nestedData['name'] = $product->name;
            $nestedData['code'] = $product->code;

            $nestedData['parent'] = $product->parentProducts->pluck('name')->implode(', ') ?: '-';
            $nestedData['sku'] = $product->sku;
            $nestedData['sale_price'] = Auth::user()->priceFormat($product->sale_price);
            $nestedData['purchase_price'] = Auth::user()->priceFormat($product->purchase_price);
            $nestedData['category_id'] = $product->category->name ?? '';
            $nestedData['unit_id'] = $product->unit->name ?? '';
            $nestedData['type'] = $product->type;

            // Attributes
            // $attributeValues = $product->attributes->map(function ($attribute) {
            //     $name = $attribute->name;
            //     $value = $attribute->pivot->value_mode ?? '';
            //     return "<li><span class='badge bg-primary m-1 '>{$name}</span> <span class='badge bg-secondary'>{$value}</span></li>";
            // })->implode('');

            // $nestedData['attribute'] = "<ul class='mb-0 list-unstyled'>{$attributeValues}</ul>";

            // Action buttons
            $id = $product->id;
            $nestedData['action'] = '
            <div class="action-btn bg-warning ms-2">
                <a href="#" class="mx-3 btn btn-lg align-items-center" data-url="' . route('productservice.detail', $id) . '" data-ajax-popup="true" data-bs-toggle="tooltip" title="' . __('Warehouse Details') . '" data-title="' . __('Warehouse Details') . '">
                    <i class="ti ti-eye text-white"></i>
                </a>
            </div>';

            if (Auth::user()->can('edit product & service')) {
                $nestedData['action'] .= '
                <div class="action-btn bg-info ms-2">
                    <a href="#" class="mx-3 btn btn-lg align-items-center" data-url="' . route('productservice.edit', $id) . '" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="' . __('Edit') . '" data-title="' . __('Edit Product') . '">
                        <i class="ti ti-pencil text-white"></i>
                    </a>
                </div>';
            }

            if (Auth::user()->can('delete product & service')) {
                $nestedData['action'] .= '
                <div class="action-btn bg-danger ms-2">
                    ' . Form::open(['method' => 'DELETE', 'route' => ['productservice.destroy', $id], 'id' => 'delete-form-' . $id]) . '
                    <a href="#" class="mx-3 btn btn-lg align-items-center bs-pass-para" data-bs-toggle="tooltip" title="' . __('Delete') . '">
                        <i class="ti ti-trash text-white"></i>
                    </a>' . Form::close() . '
                </div>';
            }

            $data[] = $nestedData;
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ]);
    }


    public function create(Request $request)
    {
        if (Auth::user()->can('create product & service')) {
            $customFields = CustomField::where('created_by', '=', Auth::user()->creatorId())->where('module', '=', 'product')->get();
            $category     = ProductServiceCategory::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', 'product & service')->get()->pluck('name', 'id');
            $unit         = Unit::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $parents = ProductService::where('created_by', '=', Auth::user()->creatorId())->whereNull('parent_id')->get();
            $parents = $parents->pluck('name', 'id');
            $attributes = Attribute::select('id', 'type')->with('options')->orderBy('id', 'desc')->get();
            // dd($attributes->keyBy('id'));
            return view('productservice.create', compact('parents', 'category', 'unit', 'attributes', 'customFields'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {

        if (Auth::user()->can('create product & service')) {

            $rules = [
                'name' => 'required',
                'attributeIds' => 'required',
                // 'sku' => ['required', Rule::unique('product_services')->where(function ($query) {
                //    return $query->where('created_by', Auth::user()->id);
                //  })
                // ],
                'sale_price' => 'required|numeric',
                'purchase_price' => 'required|numeric',
                'category_id' => 'required',
                'unit_id' => 'required',
                'is_service' => 'required', // Change Attribute Type to is_service ❗
                'is_dynamic' => 'sometimes', // Change Attribute Type to is_service ❗
            ];



            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->route('productservice.index')->with('error', $messages->first());
            }

            $productService                      = new ProductService();
            $productService->name                = $request->name;
            $productService->description         = $request->description;
            $productService->sku                 = !empty($request->sku) ?  $request->sku : '';
            $productService->sale_price          = $request->sale_price;
            $productService->purchase_price      = $request->purchase_price;
            $productService->tax_id              = !empty($request->tax_id) ? implode(',', $request->tax_id) : '';
            $productService->unit_id             = $request->unit_id;
            $productService->category_id  =  $request->category_id  ??  null;
            $productService->parent_id = $request->parent_id ?? null; // Add Feature  Parent For Product ⭐
            $productService->manufacturable = $request->manufacturable ?? null; // Add Feature  Manufacturable For Product ⭐
            if (!empty($request->quantity)) {
                $productService->quantity        = $request->quantity;
            } else {
                $productService->quantity   = 0;
            }
            $productService->is_service = $request->is_service; //

            if (!empty($request->pro_image)) {
                //storage limit
                $image_size = $request->file('pro_image')->getSize();
                $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $image_size);
                if ($result == 1) {
                    if ($productService->pro_image) {
                        $path = storage_path('uploads/pro_image' . $productService->pro_image);
                        if (file_exists($path)) {
                            File::delete($path);
                        }
                    }
                    $fileName = time() . "_" . $request->pro_image->getClientOriginalName();
                    $productService->pro_image = $fileName;
                    $dir        = 'uploads/pro_image';
                    $path = Utility::upload_file($request, 'pro_image', $fileName, $dir, []);
                }
            }

            $productService->created_by = Auth::user()->creatorId();
            // dd($productService);


            $productService->save();
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName();

                    // Store the file
                    $path = $image->storeAs('productServiceImage', $filename, 'public');

                    // Save record in DB
                    ProductServiceImage::create([
                        'product_service_id' => $productService->id,
                        'image' => $path, // stored like 'productServiceImage/filename.jpg'
                    ]);
                }
            }


            /*
                        ----------------------------------------------------
                        | New Feature Add Attributes With Value ⭐|
                        ----------------------------------------------------
                */
            $product_id = $productService->id;
            $attributeValues = $request->input('attribute_value', []);
            $dynamicValues   = $request->input('attribute_is_dynamic', []);

            if (is_array($attributeValues)) {
                foreach ($attributeValues as $attributeId => $value) {
                    // لو جاي من الفورم هيدخل قيمته، لو مش جاي يعتبر 0
                    $isDynamic = isset($dynamicValues[$attributeId]) ? 1 : 0;

                    ProductServiceAttribute::create([
                        'product_id' => $product_id,
                        'attr_id'    => $attributeId,
                        'attr_value' => $isDynamic ? null : $value, // لو dynamic خليه null
                        'is_dynamic' => $isDynamic,
                    ]);
                }
            }




            try {
                $parents = $request->parentIds ?? [];
                if (is_array($parents)) {
                    $productService->parentProducts()->sync($parents); // أو attach() حسب حالتك
                }
                $languages = Language::langs();
                $productLangData = [];
                foreach ($languages as $lang) {
                    $productLangData[] = [
                        'product_id'  => $productService->id,
                        'lang'        => $lang->code,
                        'name'        => $request->name,
                        'description' => $request->description,
                    ];
                }
                ProductServiceLang::insert($productLangData);
            } catch (\Exception $ex) {
                return redirect()->back()->with('error', __("Something Wrong When Add Parent"));
            }



            CustomField::saveData($productService, $request->customField);

            Notification::create([
                'creator_id' => Auth::user()->creatorId(),
                'user_id' => Auth::user()->id,
                'type' => 'product & service',
                'data' => json_encode([
                    'action' => 'create',
                    'name' => $productService->name,
                    'description' => $productService->description,
                    'sku' => $productService->sku,
                    'sale_price' => $productService->sale_price,
                    'purchase_price' => $productService->purchase_price,
                    'tax_id' => $productService->tax_id,
                    'unit_id' => $productService->unit_id,
                    'quantity' => $productService->quantity,
                    'type' => $productService->type,
                    'sale_chartaccount_id' => $productService->sale_chartaccount_id,
                    'expense_chartaccount_id' => $productService->expense_chartaccount_id,
                    'category_id' => $productService->category_id,
                ]),
                'is_read' => 0,
            ]);

            return redirect()->route('productservice.index')->with('success', __('Product successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function chooseMainImage(ProductServiceImage $image)
    {
        // Get the product ID for this image
        $productId = $image->product_service_id;

        // Reset all other images for this product
        ProductServiceImage::where('product_service_id', $productId)->update(['status' => '0']);
        // Set selected image to main
        $image->status = '1';
        if ($image->save()) {
            return redirect()->route('productservice.index')->with('success', __('Image Choose For Product Successfully.'));
        }

        return redirect()->back()->with('error', __('Failed to update main image.'));
    }

    public function show()
    {
        return redirect()->route('productservice.index');
    }

    public function edit($id)
    {
        $productService = ProductService::find($id);
        $productService->parentProducts;
        if (Auth::user()->can('edit product & service')) {
            if ($productService->created_by == Auth::user()->creatorId()) {
                $category = ProductServiceCategory::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', 'product & service')->get()->pluck('name', 'id');
                $unit     = Unit::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');
                $tax      = Tax::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');

                $productService->customField = CustomField::getData($productService, 'product');
                $customFields                = CustomField::where('created_by', '=', Auth::user()->creatorId())->where('module', '=', 'product')->get();
                $productService->tax_id      = explode(',', $productService->tax_id);
                $incomeChartAccounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                    ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                    ->where('chart_of_account_types.name', 'Revenue')
                    ->where('parent', '=', 0)
                    ->where('chart_of_accounts.created_by', Auth::user()->creatorId())->get()
                    ->pluck('code_name', 'id');
                $incomeChartAccounts->prepend('Select Account', 0);

                $incomeSubAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account');
                $incomeSubAccounts->leftjoin('chart_of_account_parents', 'chart_of_accounts.parent', 'chart_of_account_parents.id');
                $incomeSubAccounts->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type');
                $incomeSubAccounts->where('chart_of_account_types.name', 'Revenue');
                $incomeSubAccounts->where('chart_of_accounts.parent', '!=', 0);
                $incomeSubAccounts->where('chart_of_accounts.created_by', Auth::user()->creatorId());
                $incomeSubAccounts = $incomeSubAccounts->get()->toArray();


                $expenseChartAccounts = ChartOfAccount::select(\DB::raw('CONCAT(chart_of_accounts.code, " - ", chart_of_accounts.name) AS code_name, chart_of_accounts.id as id'))
                    ->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type')
                    ->whereIn('chart_of_account_types.name', ['Expenses', 'Costs of Goods Sold'])
                    ->where('chart_of_accounts.created_by', Auth::user()->creatorId())->get()
                    ->pluck('code_name', 'id');
                $expenseChartAccounts->prepend('Select Account', '');

                $expenseSubAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account');
                $expenseSubAccounts->leftjoin('chart_of_account_parents', 'chart_of_accounts.parent', 'chart_of_account_parents.id');
                $expenseSubAccounts->leftjoin('chart_of_account_types', 'chart_of_account_types.id', 'chart_of_accounts.type');
                $expenseSubAccounts->whereIn('chart_of_account_types.name', ['Expenses', 'Costs of Goods Sold']);
                $expenseSubAccounts->where('chart_of_accounts.parent', '!=', 0);
                $expenseSubAccounts->where('chart_of_accounts.created_by', Auth::user()->creatorId());
                $expenseSubAccounts = $expenseSubAccounts->get()->toArray();
                // Parent Data
                // If the product has parents, get their IDs and set as selected; otherwise, get all possible parents (excluding self)
                $selected = [];
                // Get selected parent IDs if any
                $selected = $productService->parentProducts->pluck('id');
                // Get all possible parent products except the current product
                $parents = ProductService::where('created_by', Auth::user()->creatorId())
                    ->whereNull('parent_id')
                    ->whereNotIn('id', $selected)
                    ->get();
                $attributes = Attribute::select('id', 'type')->with('options')->orderBy('id', 'desc')->get();
                $parents = $parents->pluck('name', 'id');
                return view('productservice.edit', compact('attributes', 'parents', 'category', 'unit', 'tax', 'productService', 'customFields', 'incomeChartAccounts', 'expenseChartAccounts', 'incomeSubAccounts', 'expenseSubAccounts'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {

        if (Auth::user()->can('edit product & service')) {
            $lang = App::getLocale();

            $productService = ProductService::find($id);
            if ($productService->created_by == Auth::user()->creatorId()) {
                $rules = [
                    'name' => 'required',
                    'sku' => 'required',
                    Rule::unique('product_services')->ignore($productService->id),
                    'sale_price' => 'required|numeric',
                    'purchase_price' => 'required|numeric',
                    'category_id' => 'required',
                    'unit_id' => 'required',

                ];
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('productservice.index')->with('error', $messages->first());
                }

                $productService->name           = $request->name;
                $productService->description    = $request->description;
                $productService->sku            = $request->sku;
                $productService->sale_price     = $request->sale_price;
                $productService->purchase_price = $request->purchase_price;
                $productService->tax_id         = !empty($request->tax_id) ? implode(',', $request->tax_id) : '';
                $productService->unit_id        = $request->unit_id;
                $productService->manufacturable        = $request->manufacturable;
                if (!empty($request->quantity)) {
                    $productService->quantity   = $request->quantity;
                } else {
                    $productService->quantity   = 0;
                }
                $productService->is_service = $request->is_service ?? $productService->is_service;
                $productService->category_id                = $request->category_id;

                if (!empty($request->pro_image)) {
                    //storage limit
                    $file_path = '/uploads/pro_image/' . $productService->pro_image;
                    $image_size = $request->file('pro_image')->getSize();
                    $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $image_size);
                    if ($result == 1) {
                        if ($productService->pro_image) {
                            Utility::changeStorageLimit(Auth::user()->creatorId(), $file_path);
                            $path = storage_path('uploads/pro_image' . $productService->pro_image);
                            if (file_exists($path)) {
                                File::delete($path);
                            }
                        }
                        $fileName = time() . "_" . $request->pro_image->getClientOriginalName();

                        $productService->images()->update(['image' => $fileName]);
                        $dir        = 'uploads/pro_image';
                        $path = Utility::upload_file($request, 'pro_image', $fileName, $dir, []);
                    }
                }
                // New Feature ⭐ Edit Lang
                // Update ProductServiceLang for the current product and language
                $productLang = ProductServiceLang::where('product_id', $productService->id)
                    ->where('lang', $lang)
                    ->first();

                if ($productLang) {
                    $productLang->update([
                        'name' => $request->name,
                        'description' => $request->description,
                    ]);
                } else {
                    // If not exists, create new
                    ProductServiceLang::create([
                        'product_id' => $productService->id,
                        'lang' => $lang,
                        'name' => $request->name,
                        'description' => $request->description,
                    ]);
                }
                // New Feature ⭐ Edit Parents with Pivot Table
                $parents = $request->parentIds ?? [];
                if (is_array($parents)) {
                    $productService->parentProducts()->attach($parents);
                }
                $productService->created_by     = Auth::user()->creatorId();

                $productService->save();
                CustomField::saveData($productService, $request->customField);
                Notification::create([
                    'creator_id' => Auth::user()->creatorId(),
                    'user_id' => Auth::user()->id,
                    'type' => 'product & service',
                    'data' => json_encode([
                        'action' => 'edit',
                        'name' => $productService->name,
                        'description' => $productService->description,
                        'sku' => $productService->sku,
                        'sale_price' => $productService->sale_price,
                        'purchase_price' => $productService->purchase_price,
                        'tax_id' => $productService->tax_id,
                        'unit_id' => $productService->unit_id,
                        'quantity' => $productService->quantity,
                        'type' => $productService->type,
                        'sale_chartaccount_id' => $productService->sale_chartaccount_id,
                        'expense_chartaccount_id' => $productService->expense_chartaccount_id,
                        'category_id' => $productService->category_id,
                    ]),
                    'is_read' => 0,
                ]);

                return redirect()->route('productservice.index')->with('success', __('Product successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('delete product & service')) {
            $productService = ProductService::find($id);
            if ($productService->created_by == Auth::user()->creatorId()) {
                Notification::create([
                    'creator_id' => Auth::user()->creatorId(),
                    'user_id' => Auth::user()->id,
                    'type' => 'product & service',
                    'data' => json_encode([
                        'action' => 'delete',
                        'name' => $productService->name,
                        'description' => $productService->description,
                        'sku' => $productService->sku,
                        'sale_price' => $productService->sale_price,
                        'purchase_price' => $productService->purchase_price,
                        'tax_id' => $productService->tax_id,
                        'unit_id' => $productService->unit_id,
                        'quantity' => $productService->quantity,
                        'type' => $productService->type,
                        'sale_chartaccount_id' => $productService->sale_chartaccount_id,
                        'expense_chartaccount_id' => $productService->expense_chartaccount_id,
                        'category_id' => $productService->category_id,
                    ]),
                    'is_read' => 0,
                ]);

                if (!empty($productService->pro_image)) {
                    //storage limit
                    $file_path = '/uploads/pro_image/' . $productService->pro_image;
                    $result = Utility::changeStorageLimit(Auth::user()->creatorId(), $file_path);
                }

                $productService->delete();

                return redirect()->route('productservice.index')->with('success', __('Product successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // public function export()
    // {
    //     $name = 'product_service_' . date('Y-m-d i:h:s');
    //     $data = Excel::download(new ProductServiceExport(), $name . '.xlsx');

    //     return $data;
    // }

    // public function importFile()
    // {
    //     return view('productservice.import');
    // }

    // public function import(Request $request)
    // {
    //     $rules = [
    //         'file' => 'required|mimes:csv,txt',
    //     ];

    //     $validator = Validator::make($request->all(), $rules);

    //     if ($validator->fails()) {
    //         $messages = $validator->getMessageBag();

    //         return redirect()->back()->with('error', $messages->first());
    //     }
    //     $products     = (new ProductServiceImport)->toArray(request()->file('file'))[0];
    //     $totalProduct = count($products) - 1;
    //     $errorArray   = [];
    //     for ($i = 1; $i <= count($products) - 1; $i++) {
    //         $items  = $products[$i];

    //         $taxes     = explode(';', $items[5]);

    //         $taxesData = [];
    //         foreach ($taxes as $tax) {
    //             $taxes       = Tax::where('id', $tax)->first();
    //             //                $taxesData[] = $taxes->id;
    //             $taxesData[] = !empty($taxes->id) ? $taxes->id : 0;
    //         }

    //         $taxData = implode(',', $taxesData);
    //         //            dd($taxData);

    //         if (!empty($productBySku)) {
    //             $productService = $productBySku;
    //         } else {
    //             $productService = new ProductService();
    //         }

    //         $productService->name           = $items[0];
    //         $productService->sku            = $items[1];
    //         $productService->sale_price     = $items[2];
    //         $productService->purchase_price = $items[3];
    //         $productService->quantity       = $items[4];
    //         $productService->tax_id         = $items[5];
    //         $productService->category_id    = $items[6];
    //         $productService->unit_id        = $items[7];
    //         $productService->type           = $items[8];
    //         $productService->description    = $items[9];
    //         $productService->created_by     = Auth::user()->creatorId();

    //         if (empty($productService)) {
    //             $errorArray[] = $productService;
    //         } else {
    //             $productService->save();
    //         }
    //     }

    //     $errorRecord = [];
    //     if (empty($errorArray)) {

    //         $data['status'] = 'success';
    //         $data['msg']    = __('Record successfully imported');
    //     } else {
    //         $data['status'] = 'error';
    //         $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalProduct . ' ' . 'record');


    //         foreach ($errorArray as $errorData) {

    //             $errorRecord[] = implode(',', $errorData);
    //         }

    //         \Session::put('errorArray', $errorRecord);
    //     }

    //     return redirect()->back()->with($data['status'], $data['msg']);
    // }

    public function warehouseDetail($id)
    {
        //$products = WarehouseProduct::with(['warehouse'])->where('product_id', '=', $id)->where('created_by', '=', Auth::user()->creatorId())->get();
        $products = ProductService::orderBy('code')->get();
        return view('productservice.detail', compact('products'));
    }

    public function searchProducts(Request $request)
    {

        $lastsegment = $request->session_key;

        if (Auth::user()->can('manage pos') && $request->ajax() && isset($lastsegment) && !empty($lastsegment)) {

            $output = "";
            if ($request->war_id == '0') {
                $ids = WarehouseProduct::where('warehouse_id', 1)->get()->pluck('product_id')->toArray();

                if ($request->cat_id !== '' && $request->search == '') {
                    if ($request->cat_id == '0') {
                        $products = ProductService::getallproducts()->whereIn('product_services.id', $ids)->get();
                    } else {
                        $products = ProductService::getallproducts()->where('category_id', $request->cat_id)->whereIn('product_services.id', $ids)->get();
                    }
                } else {
                    if ($request->cat_id == '0') {
                        $products = ProductService::getallproducts()->where('product_services.name', 'LIKE', "%{$request->search}%")->get();
                    } else {
                        $products = ProductService::getallproducts()->where('product_services.name', 'LIKE', "%{$request->search}%")->orWhere('category_id', $request->cat_id)->get();
                    }
                }
            } else {
                $ids = WarehouseProduct::where('warehouse_id', $request->war_id)->get()->pluck('product_id')->toArray();

                if ($request->cat_id == '0') {
                    $products = ProductService::getallproducts()->whereIn('product_services.id', $ids)->with(['unit'])->get();
                } else {
                    $products = ProductService::getallproducts()->whereIn('product_services.id', $ids)->where('category_id', $request->cat_id)->with(['unit'])->get();
                }
            }

            if (count($products) > 0) {
                foreach ($products as $key => $product) {
                    $quantity = $product->warehouseProduct($product->id, $request->war_id != 0 ? $request->war_id : 1);

                    $unit = (!empty($product) && !empty($product->unit)) ? $product->unit->name : '';

                    if (!empty($product->pro_image)) {
                        $image_url = ('uploads/pro_image') . '/' . $product->pro_image;
                    } else {
                        $image_url = ('uploads/pro_image') . '/default.png';
                    }
                    if ($request->session_key == 'purchases') {
                        $productprice = $product->purchase_price != 0 ? $product->purchase_price : 0;
                    } else if ($request->session_key == 'pos') {
                        $productprice = $product->sale_price != 0 ? $product->sale_price : 0;
                    } else {
                        $productprice = $product->sale_price != 0 ? $product->sale_price : $product->purchase_price;
                    }

                    $output .= '

                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-4 col-12">
                                <div class="tab-pane fade show active toacart w-100" data-url="' . url('add-to-cart/' . $product->id . '/' . $lastsegment) . '">
                                    <div class="position-relative card">
                                        <img alt="Image placeholder" src="' . asset(Storage::url($image_url)) . '" class="card-image avatar shadow hover-shadow-lg" style=" height: 6rem; width: 100%;">
                                        <div class="p-0 custom-card-body card-body d-flex ">
                                            <div class="card-body my-2 p-2 text-left card-bottom-content">
                                                <h6 class="mb-2 text-dark product-title-name">' . $product->name . '</h6>
                                                <small class="badge badge-primary mb-0">' . Auth::user()->priceFormat($productprice) . '</small>

                                                <small class="top-badge badge badge-danger mb-0">' . $quantity . ' ' . $unit . '</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    ';
                }

                return Response($output);
            } else {
                $output = '<div class="card card-body col-12 text-center">
                    <h5>' . __("No Product Available") . '</h5>
                    </div>';
                return Response($output);
            }
        }
    }

    public function addToCart(Request $request, $id, $session_key)
    {

        if (Auth::user()->can('manage product & service') && $request->ajax()) {
            $product = ProductService::find($id);
            $productquantity = 0;

            if ($product) {
                $productquantity = $product->getTotalProductQuantity();
            }

            if (!$product || ($session_key == 'pos' && $productquantity == 0)) {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => 'Error',
                        'error' => __('This product is out of stock!'),
                    ],
                    404
                );
            }

            $productname = $product->name;

            if ($session_key == 'purchases') {

                $productprice = $product->purchase_price != 0 ? $product->purchase_price : 0;
            } else if ($session_key == 'pos') {

                $productprice = $product->sale_price != 0 ? $product->sale_price : 0;
            } else {

                $productprice = $product->sale_price != 0 ? $product->sale_price : $product->purchase_price;
            }

            $originalquantity = (int)$productquantity;

            $taxes = Utility::tax($product->tax_id);

            $totalTaxRate = Utility::totalTaxRate($product->tax_id);

            $product_tax = '';
            $product_tax_id = [];
            foreach ($taxes as $tax) {
                $product_tax .= !empty($tax) ? "<span class='badge badge-primary'>" . $tax->name . ' (' . $tax->rate . '%)' . "</span><br>" : '';
                $product_tax_id[] = !empty($tax) ? $tax->id : 0;
            }

            if (empty($product_tax)) {
                $product_tax = "-";
            }
            $producttax = $totalTaxRate;


            $tax = ($productprice * $producttax) / 100;

            $subtotal        = $productprice + $tax;
            $cart            = session()->get($session_key);
            $image_url = (!empty($product->pro_image) && Storage::exists($product->pro_image)) ? $product->pro_image : 'uploads/pro_image/' . $product->pro_image;

            $model_delete_id = 'delete-form-' . $id;

            $carthtml = '';

            $carthtml .= '<tr data-product-id="' . $id . '" id="product-id-' . $id . '">
                            <td class="cart-images">
                                <img alt="Image placeholder" src="' . asset(Storage::url($image_url)) . '" class="card-image avatar shadow hover-shadow-lg">
                            </td>

                            <td class="name">' . $productname . '</td>

                            <td class="">
                                   <span class="quantity buttons_added">
                                         <input type="button" value="-" class="minus">
                                         <input type="number" step="1" min="1" max="" name="quantity" title="' . __('Quantity') . '" class="input-number" size="4" data-url="' . url('update-cart/') . '" data-id="' . $id . '">
                                         <input type="button" value="+" class="plus">
                                   </span>
                            </td>


                            <td class="tax">' . $product_tax . '</td>

                            <td class="price">' . Auth::user()->priceFormat($productprice) . '</td>

                            <td class="subtotal">' . Auth::user()->priceFormat($subtotal) . '</td>

                            <td class="">
                                 <a href="#" class="action-btn bg-danger bs-pass-para-pos" data-confirm="' . __("Are You Sure?") . '" data-text="' . __("This action can not be undone. Do you want to continue?") . '" data-confirm-yes=' . $model_delete_id . ' title="' . __('Delete') . '}" data-id="' . $id . '" title="' . __('Delete') . '"   >
                                   <span class=""><i class="ti ti-trash btn btn-sm text-white"></i></span>
                                 </a>
                                 <form method="post" action="' . url('remove-from-cart') . '"  accept-charset="UTF-8" id="' . $model_delete_id . '">
                                      <input name="_method" type="hidden" value="DELETE">
                                      <input name="_token" type="hidden" value="' . csrf_token() . '">
                                      <input type="hidden" name="session_key" value="' . $session_key . '">
                                      <input type="hidden" name="id" value="' . $id . '">
                                 </form>

                            </td>
                        </td>';

            // if cart is empty then this the first product
            if (!$cart) {
                $cart = [
                    $id => [
                        "name" => $productname,
                        "quantity" => 1,
                        "price" => $productprice,
                        "id" => $id,
                        "tax" => $producttax,
                        "subtotal" => $subtotal,
                        "originalquantity" => $originalquantity,
                        "product_tax" => $product_tax,
                        "product_tax_id" => !empty($product_tax_id) ? implode(',', $product_tax_id) : 0,
                    ],
                ];


                if ($originalquantity < $cart[$id]['quantity'] && $session_key == 'pos') {
                    return response()->json(
                        [
                            'code' => 404,
                            'status' => 'Error',
                            'error' => __('This product is out of stock!'),
                        ],
                        404
                    );
                }

                session()->put($session_key, $cart);

                return response()->json(
                    [
                        'code' => 200,
                        'status' => 'Success',
                        'success' => $productname . __(' added to cart successfully!'),
                        'product' => $cart[$id],
                        'carthtml' => $carthtml,
                    ]
                );
            }

            // if cart not empty then check if this product exist then increment quantity
            if (isset($cart[$id])) {

                $cart[$id]['quantity']++;
                $cart[$id]['id'] = $id;

                $subtotal = $cart[$id]["price"] * $cart[$id]["quantity"];
                $tax      = ($subtotal * $cart[$id]["tax"]) / 100;

                $cart[$id]["subtotal"]         = $subtotal + $tax;
                $cart[$id]["originalquantity"] = $originalquantity;

                if ($originalquantity < $cart[$id]['quantity'] && $session_key == 'pos') {
                    return response()->json(
                        [
                            'code' => 404,
                            'status' => 'Error',
                            'error' => __('This product is out of stock!'),
                        ],
                        404
                    );
                }

                session()->put($session_key, $cart);

                return response()->json(
                    [
                        'code' => 200,
                        'status' => 'Success',
                        'success' => $productname . __(' added to cart successfully!'),
                        'product' => $cart[$id],
                        'carttotal' => $cart,
                    ]
                );
            }

            // if item not exist in cart then add to cart with quantity = 1
            $cart[$id] = [
                "name" => $productname,
                "quantity" => 1,
                "price" => $productprice,
                "tax" => $producttax,
                "subtotal" => $subtotal,
                "id" => $id,
                "originalquantity" => $originalquantity,
                "product_tax" => $product_tax,
            ];

            if ($originalquantity < $cart[$id]['quantity'] && $session_key == 'pos') {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => 'Error',
                        'error' => __('This product is out of stock!'),
                    ],
                    404
                );
            }

            session()->put($session_key, $cart);

            return response()->json(
                [
                    'code' => 200,
                    'status' => 'Success',
                    'success' => $productname . __(' added to cart successfully!'),
                    'product' => $cart[$id],
                    'carthtml' => $carthtml,
                    'carttotal' => $cart,
                ]
            );
        } else {
            return response()->json(
                [
                    'code' => 404,
                    'status' => 'Error',
                    'error' => __('This Product is not found!'),
                ],
                404
            );
        }
    }

    public function updateCart(Request $request)
    {

        $id          = $request->id;
        $quantity    = $request->quantity;
        $discount    = $request->discount;
        $session_key = $request->session_key;

        if (Auth::user()->can('manage product & service') && $request->ajax() && isset($id) && !empty($id) && isset($session_key) && !empty($session_key)) {
            $cart = session()->get($session_key);


            if (isset($cart[$id]) && $quantity == 0) {
                unset($cart[$id]);
            }

            if ($quantity) {

                $cart[$id]["quantity"] = $quantity;

                $producttax            = isset($cart[$id]) ? $cart[$id]["tax"] : 0;
                $productprice          = $cart[$id]["price"];

                $subtotal = $productprice * $quantity;
                $tax      = ($subtotal * $producttax) / 100;

                $cart[$id]["subtotal"] = $subtotal + $tax;
            }

            if (isset($cart[$id]) && ($cart[$id]["originalquantity"]) < $cart[$id]['quantity'] && $session_key == 'pos') {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => 'Error',
                        'error' => __('This product is out of stock!'),
                    ],
                    404
                );
            }

            $subtotal = array_sum(array_column($cart, 'subtotal'));
            $discount = $request->discount;
            $total = $subtotal - $discount;
            $totalDiscount = User::priceFormats($total);
            $discount = $totalDiscount;


            session()->put($session_key, $cart);

            return response()->json(
                [
                    'code' => 200,
                    'success' => __('Cart updated successfully!'),
                    'product' => $cart,
                    'discount' => $discount,
                ]
            );
        } else {
            return response()->json(
                [
                    'code' => 404,
                    'status' => 'Error',
                    'error' => __('This Product is not found!'),
                ],
                404
            );
        }
    }

    public function emptyCart(Request $request)
    {
        $session_key = $request->session_key;

        if (Auth::user()->can('manage product & service') && isset($session_key) && !empty($session_key)) {
            $cart = session()->get($session_key);
            if (isset($cart) && count($cart) > 0) {
                session()->forget($session_key);
            }

            return redirect()->back()->with('error', __('Cart is empty!'));
        } else {
            return redirect()->back()->with('error', __('Cart cannot be empty!.'));
        }
    }

    public function warehouseemptyCart(Request $request)
    {
        $session_key = $request->session_key;

        $cart = session()->get($session_key);
        if (isset($cart) && count($cart) > 0) {
            session()->forget($session_key);
        }

        return response()->json();
    }

    public function removeFromCart(Request $request)
    {
        $id          = $request->id;
        $session_key = $request->session_key;
        if (Auth::user()->can('manage product & service') && isset($id) && !empty($id) && isset($session_key) && !empty($session_key)) {
            $cart = session()->get($session_key);
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session()->put($session_key, $cart);
            }

            return redirect()->back()->with('error', __('Product removed from cart!'));
        } else {
            return redirect()->back()->with('error', __('This Product is not found!'));
        }
    }
}
