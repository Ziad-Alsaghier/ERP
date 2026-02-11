<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Language;
use App\Models\ProductionLine;
use App\Models\ProductionLineOperator;
use App\Models\ProductionLineType;
use App\Models\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductionLineController extends Controller
{


    // Production Line Module    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $productionLines = ProductionLine::orderBy('id', 'desc')->get();

        return view('production.line.index', compact('productionLines'));
    }
    /**
     * create
     *
     * @return void
     */

    public function create()
    {
        // Get Branchs Data
        $branchs = Branch::orderBy('id', 'desc')->get();
        $branchs = $branchs->pluck('name', 'id');
        // Get Production Line Types Data
        $productLineTypes = ProductionLineType::orderBy('id', 'desc')->get();
        $productLineTypes = $productLineTypes->pluck('name', 'id');
        // Get products  Data
        $products = ProductService::where('manufacturable', true)->orderBy('id', 'desc')->get();
        $products = $products->pluck('name', 'id');
        // Get Charts Account -> cost center Data
        $accounts = Account::orderBy('id', 'desc')->get();
        $accounts = $accounts->pluck('name', 'id');
        // Get Charts Account -> cost center Data
        $operators = Employee::orderBy('id', 'desc')->get();
        $operators = $operators->pluck('name', 'id');

        return view('production.line.create', get_defined_vars());
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'branch_id' => 'required',
            'type_id' => 'required',
            'product_id' => 'required',
            'operator_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->route('production.line.index')->with('error', $messages->first());
        } else {
            $data = $validator->validated(); // Get Array Data From Validate
            // dd($data);
            $newProductionLine = ProductionLine::create($data); // Create Production Line
            $newProductionLine->operators()->create(['employee_id' => $data['operator_id']]); // Create Production Line Operators
            $newProductionLine->linProducts()->create(['product_id' => $data['product_id']]); // Create Production Line Products
            $dataLang = [
                'attribute_select_id' => $newProductionLine->id,
                'name' => $data['name'],
            ];
            // Create Multi-Languages
            multi_languages($newProductionLine, $dataLang); // Start Create Multi-Languages
            return redirect()->route('production.line.index')->with('success', __('Production Line Type successfully created.'));
        }
    }
    public function edit(ProductionLine $line)
    {
           $line->load(['linProducts', 'operators', 'branch', 'type']);
                // Get Products Data
                $products = ProductService::get();
                $products = $products ->pluck('name','id');
                // Get Types Data
                $type = ProductionLineType::get();
                $types = $type->pluck('name','id');
                // Get Operators Data
            $operators = Employee::get();
            $operators = $operators->pluck('name','id');
                // Get Branchs Data
            $branchs = Branch::get();
            $branchs = $branchs->pluck('name','id');
        return view('production.line.edit', compact('line','products','types','operators','branchs'));
    }



    public function  update(Request $request, ProductionLine $line){
            $productionLineData = $request->only(  'name', 'type_id','branch_id','cost_center','is_enabled','product_id','employee_id')    ;

        $updatelanguages =   changeMultiLangData($line, ['name' => $productionLineData['name']], 'line_id', $line->id); // Update Language
            // Get Production Line Data For Update 
            $productionLineData['is_enabled'] = $productionLineData['is_enabled']  ?? '0'; 
            // Update production Line 
            $line->update($productionLineData);
            // Update Products
            $products = $productionLineData['product_id'];
            $line->linProducts()->updateOrCreate(
                [
                        'line_id'=> $line->id,
                        'product_id'=> $products,
                ],
                [
                    'product_id'=>$products
                ]);
                
            // Update Operators 
            $operators = $productionLineData['employee_id'];

             $line->operators()->updateOrCreate(
                [
                        'line_id'=> $line->id,
                        'employee_id'=> $operators,
                ],
                [
                    'employee_id'=>$operators
                ]);

                return redirect()->route('production.line.index')->with('success','Production Line Updated');
    }

        public function delete(productionLine $line){
            if($line->delete()){
                      return redirect()->route('production.line.index')->with('success','Production Line Deleted');
            }
        }


    // End


    // production Line Type Module    
    /**
     * productionLineType
     *
     * @return void
     */
    public function productionLineType()
    {
        $productionLineTypes  = ProductionLineType::orderBy('id', 'desc')->get();
        return view('production.line.type.index', compact('productionLineTypes'));
    }
    public function productionTypeCreate()
    {
        return view('production.line.type.create');
    }

    public function productionTypeStore(Request  $request)
    {
        $rules = [
            'name' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->route('production.line.type.index')->with('error', $messages->first());
        } else {
            // Create Production Line Type 
            $productionLineType =  ProductionLineType::create();
            // Create Production Line Type Multi-Languages
            $languages = Language::get();
            $data = $request->only('name');
            foreach ($languages as $lang) {
                $productionLineType->langs()->create(['name' => $data['name'], 'lang' => $lang->code]);
            }
            if ($productionLineType) {
                return redirect()->route('production.line.type.index')->with('success', __('Production Line Type successfully created.'));
            }
        }
    }

    public function productionTypeDelete($id)
    {
        $productionLineType = ProductionLineType::find($id);

        if ($productionLineType->delete()) {
            return redirect()->route('production.line.type.index')->with('success', __('Production Line Type successfully deleted.'));
        }
    }
    public function productionLineTypeEdit(ProductionLineType $productionLineType)
    {

        return view('production.line.type.edit', compact('productionLineType'));
    }
    public function productionLineTypeUpdate(ProductionLineType $productionLineType, Request $request)
    {
        $updatelanguages =   changeMultiLangData($productionLineType, ['name' => $request->name], 'type_id', $productionLineType->id); // Update Language
        if ($updatelanguages) {

            return redirect()->route('production.line.type.index')->with('success', __('Attributes successfully Updated.'));  // return Success Message
        }
    }
}
