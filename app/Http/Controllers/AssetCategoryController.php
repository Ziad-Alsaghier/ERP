<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Services\AccountsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AssetCategoryController extends Controller
{

    protected $accountsService;
    public function __construct(AccountsService $accountsService)
    {
        $this->accountsService = $accountsService;
    }
    // Show the list of asset categories
    public function index()
    {
        $categories = AssetCategory::with('assetAccount','depreciationExpenseAccount','accumulatedDepreciationAccount')->where('created_by',Auth::id())->get();
        return view('asset_categories.index', compact('categories'));
    }

    // Show the form to create a new asset category
    public function create()
    {

        $prefix = 'AC';
        $counter = 1;
    
        // Check for existing references and increment
        do {
            $referenceNumber = $prefix . $counter;
            $exists = AssetCategory::where('reference_number', $referenceNumber)->exists();
            $counter++;
        } while ($exists);
        $accounts_type = $this->accountsService->getChartAccountTypes();
        $miniTypes = $this->accountsService->getMiniTypesByCategories(['fixed_assets']);
        $totalaccounts = $this->accountsService->getChartAccounts($miniTypes);
        $accounts = $totalaccounts['parent_accounts'];
        $subAccounts = $totalaccounts['sub_accounts'];
        return view('asset_categories.create',compact('accounts_type','accounts','subAccounts','referenceNumber'));
    }

    // Store a new asset category
   

public function store(Request $request)
{
    try {
        // Validate the request data
        $validatedData = $request->validate([
            'reference_number' => 'required|unique:asset_categories|max:255',
            'english_name' => 'required|max:255',
            'arabic_name' => 'required|max:255',
            'is_depreciable' => 'boolean',
            'depreciation_method' => 'nullable|in:Straight-line method,Reducing balance method,Units of production method,Sum of years digits method',
            'useful_life' => 'nullable|integer',
            'useful_life_unit' => 'nullable|in:years,percent',
            'asset_account' => 'required|exists:chart_of_accounts,id',
            'depreciation_expense_account' => 'nullable|exists:chart_of_accounts,id|required_if:is_depreciable,1',  
            'accumulated_depreciation_account' => 'nullable|exists:chart_of_accounts,id|required_if:is_depreciable,1',
            'manual_depreciation' => 'boolean',
            'recorded_depreciation' => 'boolean',
        ]);
        $validatedData['created_by'] = \Auth::id();
        AssetCategory::create($validatedData);
        
        return redirect()->route('asset_categories.index')->with('success', 'Asset Category created successfully');
    } catch (ValidationException $e) {
        
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        // Catch any other exceptions that might occur (e.g., database issues)
        return redirect()->back()->with('error', 'Something went wrong. Please try again.')->withInput();
    }
}

    
    // Show the form to edit an existing asset category
    public function edit($id)
    {
        $assetCategory = AssetCategory::findOrFail($id);
        $accounts_type = $this->accountsService->getChartAccountTypes();
        $miniTypes = $this->accountsService->getMiniTypesByCategories(['fixed_assets']);
        $totalaccounts = $this->accountsService->getChartAccounts($miniTypes);
        $accounts = $totalaccounts['parent_accounts'];
        $subAccounts = $totalaccounts['sub_accounts'];
        return view('asset_categories.edit',compact('accounts_type','accounts','subAccounts','assetCategory'));
        }

    // Update an existing asset category
    public function update(Request $request, $id)
    {
        try {
            $assetCategory = AssetCategory::findOrFail($id);
    
            // Start by setting up the base validation rules
            $validationRules = [
                'reference_number' => 'required|max:255|unique:asset_categories,reference_number,' . $assetCategory->id,
                'english_name' => 'required|max:255',
                'arabic_name' => 'required|max:255',
                'is_depreciable' => 'boolean',
                'depreciation_method' => 'nullable|in:Straight-line method,Reducing balance method,Units of production method,Sum of years digits method',
                
                'asset_account' => 'required|exists:chart_of_accounts,id',
            ];
    
            // If 'is_depreciable' is true, apply validation rules for depreciation-related fields
            if ($request->has('is_depreciable')) {
                $validationRules = array_merge($validationRules, [
                    'depreciation_expense_account' => 'nullable|exists:chart_of_accounts,id|required_if:is_depreciable,1',
                    'accumulated_depreciation_account' => 'nullable|exists:chart_of_accounts,id|required_if:is_depreciable,1',
                    'useful_life' => 'nullable|integer',
                'useful_life_unit' => 'nullable|in:years,percent',
                    'manual_depreciation' => 'boolean',
                    'recorded_depreciation' => 'boolean',
                ]);
            } else {
                
                $request->merge([
                    'is_depreciable' => false,
                    'depreciation_expense_account' => null,
                    'accumulated_depreciation_account' => null,
                    'useful_life' => null,
                    'useful_life_unit' => null,
                    'manual_depreciation' => false,
                    'recorded_depreciation' => false,
                ]);
                $validationRules = array_merge($validationRules, [
                    'depreciation_expense_account' => 'nullable|exists:chart_of_accounts,id|required_if:is_depreciable,1',
                    'accumulated_depreciation_account' => 'nullable|exists:chart_of_accounts,id|required_if:is_depreciable,1',
                    'useful_life' => 'nullable|integer',
                'useful_life_unit' => 'nullable|in:years,percent',
                    'manual_depreciation' => 'nullable|boolean',
                    'recorded_depreciation' => 'nullable|boolean',
                ]);
            }
            // dd($request->all());
            // Validate the request data based on the defined rules
            $validatedData = $request->validate($validationRules);
    
            // Update the AssetCategory with validated data
            $assetCategory->update($validatedData);
    
            // Redirect back with success message
            return redirect()->route('asset_categories.index')->with('success', 'Asset Category updated successfully');
        } catch (ValidationException $e) {
            // If validation fails, return the errors
            Log::info($e->getMessage());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Catch any other exceptions and handle them
            return redirect()->back()->with('error', 'Something went wrong. Please try again.')->withInput();
        }
    }
    
    // Delete an asset category
    public function destroy($id)
    {
        $assetCategory = AssetCategory::findOrFail($id);
        $assetCategory->delete();

        return redirect()->route('asset_categories.index')->with('success', 'Asset Category deleted successfully');
    }
}
