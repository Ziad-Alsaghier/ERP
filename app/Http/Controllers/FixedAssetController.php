<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountMiniType;

use App\Models\FixedAsset;
use App\Models\ProductServiceUnit;
use App\Models\Tax;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FixedAssetController extends Controller
{
    public function index(Request $request)
    {
        $query = FixedAsset::where('created_by', Auth::id());
    
        // Check if category filter is provided in the request
        if ($request->has('category') && $request->category != null) {
            $query->where('category', $request->category);
        }
    
        $name = 'english_name';
        $fixedAssets = $query->get();
        $categories = AssetCategory::where('created_by', Auth::id())->get()->pluck('english_name','id'); // Assuming you want a dropdown of categories
    
        return view('fixed_assets.index', compact('fixedAssets', 'categories'));
    }

    public function create()
    {
        $prefix = 'FXA';
        $counter = 1;

        // Check for existing references and increment
        do {
            $referenceNumber = $prefix . $counter;
            $exists = FixedAsset::where('reference_number', $referenceNumber)->exists();
            $counter++;
        } while ($exists);
        $unit = ProductServiceUnit::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $tax = Tax::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $name = 'english_name';
        if (app()->isLocale('ar')) {
            $name = 'arabic_name';
        }
        $category = AssetCategory::where('created_by',\Auth::id())->pluck($name, 'id');
        return view('fixed_assets.create', compact('referenceNumber', 'unit', 'tax', 'category'));
    }

    public function store(Request $request)
{
    try {
        // Validate incoming request
        $validated = $request->validate([
            'arabic_name' => 'required|string|max:255',
            'english_name' => 'required|string|max:255',
            'reference_number' => 'required|string|max:255',
            'category' => 'required|exists:asset_categories,id', 
            'description' => 'nullable|string',
            'measurement_unit' => 'required|exists:product_service_units,id', 
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'barcode' => 'nullable|string|max:255',
            'asset_image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        // Handle file upload for asset_image if present
        if ($request->hasFile('asset_image')) {
            $image_size = $request->file('asset_image')->getSize();
            $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
            
            if ($result == 1) {
                $fileName = time() . "_" . $request->asset_image->getClientOriginalName();
                $validated['asset_image'] = $fileName;
                $dir = 'uploads/pro_image';
                $path = Utility::upload_file($request, 'asset_image', $fileName, $dir, []);
            }
        }

        // Assign creator ID
        $validated['created_by'] = \Auth::id();

        // Create the FixedAsset record
        $fixedAsset = FixedAsset::create($validated);

        // Return success response
        return redirect()->route('fixed-assets.index')->with('success','Asset Created Successfully');

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error($e->getMessage());
        return redirect()->back()
    ->withErrors($e->errors())
    ->withInput(); // 422 Unprocessable Entity
    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        return redirect()->back()
    ->withErrors($e->getMessage());
    }
}


    public function show(FixedAsset $fixedAsset)
    {
     //
    }

    public function edit(FixedAsset $fixedAsset)
    {
        $asset = $fixedAsset;
        $unit = ProductServiceUnit::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $tax = Tax::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $name = 'english_name';
        if (app()->isLocale('ar')) {
            $name = 'arabic_name';
        }
        $category = AssetCategory::where('created_by',\Auth::id())->pluck($name, 'id');
        return view('fixed_assets.edit', compact( 'unit', 'tax', 'category','asset'));
    }

    public function update(Request $request, FixedAsset $fixedAsset)
    {
       
        try {
            $validated = $request->validate([
                'arabic_name' => 'required|string|max:255',
                'english_name' => 'required|string|max:255',
                'reference_number' => 'required|string|max:255',
                'category' => 'required|exists:asset_categories,id', 
                'description' => 'nullable|string',
                'measurement_unit' => 'required|exists:product_service_units,id', 
                'tax_percentage' => 'required|numeric|min:0|max:100',
                'barcode' => 'nullable|string|max:255',
                'asset_image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048', 
            ]);
    
    
            if(!empty($request->asset_image))
            {
                //storage limit
                $file_path = '/uploads/pro_image/'.$fixedAsset->asset_image;
                $image_size = $request->file('asset_image')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                if($result==1)
                {
                    if($fixedAsset->asset_image)
                    {
                        Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                        $path = storage_path('uploads/pro_image' . $fixedAsset->asset_image);
                       if(file_exists($path))
                       {
                           \File::delete($path);
                       }
                    }
                    $fileName = time() . "_" . $request->asset_image->getClientOriginalName(); 
    
                    $validated['asset_image'] = $fileName;
                    $dir = 'uploads/pro_image';
                    $path = Utility::upload_file($request,'asset_image',$fileName,$dir,[]);
                }
    
            }
            $fixedAsset->update($validated);
    
            return redirect()->route('fixed-assets.index')->with('success','Asset Updated Successfully');
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error($e->getMessage());
            return redirect()->back()
        ->withErrors($e->errors())
        ->withInput(); // 422 Unprocessable Entity
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return redirect()->back()
        ->withErrors($e->getMessage());
        }

    }

    public function destroy(FixedAsset $fixedAsset)
    {
        $fixedAsset->delete();

        return redirect()->route('fixed-assets.index')->with('success','Asset Deleted Successfully');
    }
}
