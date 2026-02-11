<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Utility;
use App\Models\Customer;
use App\Models\CustomField;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
// use App\Models\Proposal;
use App\Models\ManufacProposals;
use App\Models\ManufacProposalProducts;
use App\Models\Manufacturing_products;
use App\Models\Manufacturing_categories;
use App\Models\Manufacturing_sheet_stn_size;
use App\Models\Manufacturing_sheet_stn_amount;
use App\Models\Manufacturing_unite_stand_amount;
use App\Models\Manufacturing_addons;
use App\Models\Manufacturing_selection;
use App\Models\Manufacturing_options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class ManufacturingController extends Controller
{
    public function index(){
        // @dd(Auth::user()->email,Auth::user()->password);
        if(Auth::user()->type !== 'client' && Auth::user()->type !== 'super admin' ){
            $url_manfuc = env('MANUFACT_URL','/manfuc');
            $id_token = Auth::user()->id;
            $user = Auth::user()->email;
            $passwod = Auth::user()->password;
            return redirect()->to($url_manfuc.'/index.php?login&user='.$user.'&token='.$passwod.'&id_token='.$id_token);
        }else{
            return redirect()->back()->with('error', 'Permission denied!');
        }
    }
    public function instantquote(Request $request){
        return view('manufacturing.index');
    }
    public function create($customerId = 0){
        $customFields    = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'proposal')->get();
        $proposal_number = \Auth::user()->proposalNumberFormat($this->proposalNumber());
        $customers       = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $customers->prepend('Select Customer', '');
        $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->where('type', 'income')->get()->pluck('name', 'id');
        $category->prepend('Select Category', '');
        $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $product_services->prepend(__('Stander product'), '');

        return view('manufacturing.create', compact('customers', 'proposal_number', 'product_services', 'category', 'customFields', 'customerId'));
    }

    function proposalNumber()
    {
        $latest = ManufacProposals::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }
        return $latest->proposal_id + 1;
    }


    public function create_manufac(Request $request){

        $categories = Manufacturing_categories::where('user_id', \Auth::user()->creatorId())->where('visiblity', 1)->where('active', 1)->get();
        $products = Manufacturing_products::where('user_id', \Auth::user()->creatorId())->where('visiblity', 1)->where('active', 1)->get();
        return view('manufacturing.custom-create', compact('products','categories'));
    }

    public function getProductsByCategory($categoryId){
        $products = Manufacturing_products::where('categorie', $categoryId)->get();
        return response()->json($products);
    }
    public function getProductsDetails($productID)
    {
        $product_details = Manufacturing_products::where('id',$productID)->first();
        if($product_details['pricingtype']  == "sheet"){
            $sizes = Manufacturing_sheet_stn_size::where('product_id', $productID)->get();
            $quantities = Manufacturing_sheet_stn_amount::where('product_id', $productID)->get();
        }elseif( $product_details['pricingtype']  == "unite"){
            $quantities = Manufacturing_unite_stand_amount::where('product_id', $productID)->get();
            $sizes = null;
        }

        $addons = Manufacturing_addons::where('product_id', $productID)->get();
        $tables = Manufacturing_selection::where('product_id', $productID)->get();
        $options = Manufacturing_options::where('product_id', $productID)->get();


        return response()->json([
            'product' => $product_details,
            'sizes' => $sizes,
            'quantities' => $quantities,
            'addons' => $addons,
            'tables' => $tables,
            'options' => $options
        ]);
    }
    public function uploadfile(Request $request){

        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:2048', // تعديل القواعد حسب الحاجة
        ]);

        if ($request->hasFile('file')) {
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if ($settings['storage_setting'] == 'local') {
                $dir = '/uploads/designes/';
            } else {
                $dir = 'uploads/designes';
            }
            $url = '';
            $path = Utility::upload_file($request, 'file', $fileNameToStore, $dir, []);
            if ($path['flag'] == 1) {
                $url = $path['url'];
                return response()->json(['success' => true, 'path' => $url]);
            } else {
                return response()->json(['error' => false, 'message' => 'فشل في رفع الملف']);
            }

        }




    }


}
