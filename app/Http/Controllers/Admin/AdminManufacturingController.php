<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Utility;
use App\Models\Customer;



// product
use App\Models\Manufacturing_products;
// addons
use App\Models\Manufacturing_addons;
// stander sizes
use App\Models\Manufacturing_sheet_stn_size;
// stander amount
use App\Models\Manufacturing_sheet_stn_amount;
// tables
use App\Models\Manufacturing_selection;
use App\Models\Manufacturing_options;

use App\Models\Manufacturing_product_unite;
use App\Models\Manufacturing_categories;
use App\Models\Manufacturing_categories_products;


class AdminManufacturingController extends Controller
{
    public function index(){
        $users = User::where('type','company')->pluck('name','id');
        return view('manufacturing.admin.index',compact('users'));
    }
    public function add(request $request){
        $source = User::find($request->source_id);
        $user = User::find($request->user_id);
        $source_product = Manufacturing_products::where('user_id',$source->id)->get();
        $source_product_addons = Manufacturing_addons::where('user_id',$source->id)->get();

        dd($source_product_addons->toArray());
    }
}
