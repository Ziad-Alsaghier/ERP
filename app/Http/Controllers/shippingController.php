<?php

namespace App\Http\Controllers;

use App\Models\Shipping;
use Illuminate\Http\Request;

class shippingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shipping = Shipping::where('shippings.created_by', '=', \Auth::user()->creatorId())
                            ->leftJoin('shippings as parent_shipping', 'shippings.parent', '=', 'parent_shipping.id')
                            ->select('shippings.*', 'parent_shipping.name as parent_name')
                            ->get();
    
        return view('shipping.index')->with('shipping', $shipping);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $country = Shipping::where('created_by', '=', \Auth::user()->creatorId())->where('parent','=','0')->get()->pluck('name', 'id');
        $country->prepend('Make it Country', 0);
        return view('shipping.create')->with('country',$country);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
        // if($request->amount == null){
        //     $request->amount = 0;
        // }
        $shipping                 = new Shipping();
        $shipping->name           = $request->name;
        $shipping->amount         = $request->amount == null ? 0 : $request->amount;
        $shipping->parent         = $request->parent;
        $shipping->created_by     = \Auth::user()->creatorId();
        $shipping->save();

        return redirect()->route('shipping.index')->with('success', __('Shipping successfully created'));
        // return 'ss';
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipping $shipping)
    {

        if($shipping->created_by == \Auth::user()->creatorId())
        {
            $country = Shipping::where('created_by', '=', \Auth::user()->creatorId())->where('parent','=','0')->get()->pluck('name', 'id');
            $country->prepend('Make it Country', 0);
            return view('shipping.edit', compact('shipping','country'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipping $shipping)
    {
        if($shipping->created_by == \Auth::user()->creatorId())
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'amount' => 'required|numeric',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $shipping->name = $request->name;
            $shipping->amount = $request->amount;
            $shipping->save();

            return redirect()->route('shipping.index')->with('success', __('Shipping successfully Edit'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipping $shipping)
    {
        if ($shipping->created_by == \Auth::user()->creatorId())
        {
            $shipping_data = Shipping::where('id', '=', $shipping->id)
                ->orWhere('parent', '=', $shipping->id)
                ->get();
    
            if (!$shipping_data->isEmpty())
            {
                // حذف كل الشحنات المرتبطة
                foreach ($shipping_data as $data)
                {
                    $data->delete();
                }
    
                return redirect()->route('shipping.index')->with('success', __('Shipping successfully deleted along with related data'));
            }
            else
            {
                return redirect()->route('shipping.index')->with('error', __('Nothing to delete'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    
}
