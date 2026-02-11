<?php

namespace App\Http\Controllers;
use App\Models\WebOrders;
use App\Models\WebOrdersProducst;
use App\Models\Customer;
use App\Models\WebUsers;
use App\Models\Proposal;
use App\Models\ProposalProduct;
use Illuminate\Http\Request;
use App\Models\Notification;
class webOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $weborders = WebOrders::where('created_by', \Auth::user()->creatorId())->get();
        $webordersproducsts = WebOrdersProducst::where('created_by', \Auth::user()->creatorId())->get();
        return view('weborders.index',compact('weborders','webordersproducsts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = WebOrders::where('id',$id)->where('created_by', \Auth::user()->creatorId())->first();
        $orders_products = WebOrdersProducst::where('order_id',$id)->where('created_by', \Auth::user()->creatorId())->get();
        return view('weborders.view',compact('order','orders_products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = WebOrders::where('id', $id)->first();
        $orders_products = WebOrdersProducst::where('order_id', $order->id)->get();
        foreach ($orders_products as $product) {
            $product->delete();
        }
        $order->delete();
        return redirect()->route('web_orders.index')->with('success', __('Order deleted successfully!'));
    }

    public function productorderdelete($id){
        $orders_products = WebOrdersProducst::where('id', $id)->first()->delete();
        return redirect()->route('web_orders.index')->with('success', __('product deleted successfully!'));

    }

    public function createProposal($id){
        $order = WebOrders::where('id',$id)->first();
        $orders_products = WebOrdersProducst::where('order_id',$id)->get();
        $customer = Customer::where('id',$order->customer_id)->first();
        // dd($orders_products);
        $proposal                       = new Proposal();
        $proposal->proposal_id          = Proposal::max('proposal_id')+1;
        $proposal->customer_id          = $customer->id;
        $proposal->status               = 0;
        $proposal->issue_date           = $order->created_at;
        $proposal->order_id             = $order->id;
        $proposal->category_id          = 0;
        $proposal->created_by           = \Auth::user()->creatorId();
        // dd($proposal);
        $proposal->save();

        foreach ($orders_products as $product) {
            if($product->type == 'custom'){
                // dd($product);
                $description = json_decode($product->description, true);
                $description['info'] = [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'type' => $product->type,
                ];
                // dd($description);
                $proposal_product = new ProposalProduct();
                $proposal_product->proposal_id = $proposal->id;
                $proposal_product->product_id = 1;
                $proposal_product->quantity = $product->count;
                $description = json_encode($description);
                $proposal_product->description = $description;
                $description = json_decode($description, true);
                $proposal_product->price =$description['التسعيرة النهائية']['سعر الافرادي بعد الضريبة'];
                // dd($description['التسعيرة النهائية']['سعر الافرادي بعد الضريبة']);
                $proposal_product->save();
            }else{
                $proposal_product = new ProposalProduct();
                $proposal_product->proposal_id = $proposal->id;
                $proposal_product->product_id = $product->product_id;
                $proposal_product->quantity = $product->count;
                $proposal_product->description = $product->description;
                $proposal_product->price = $product->price;
                $proposal_product->save();
            }
        }
        $order->quotation_details = json_encode([
            "id" => $proposal->id,
            "token" => \Crypt::encrypt($proposal->id)
        ]);
        $order->save();
        // dd($order->quotation_details);
        // dd($proposal->proposal_id);

        return redirect()->route('proposal.show',\Crypt::encrypt($proposal->id))->with('success', __('Proposal created successfully!'));
    }
    public function statusChange(Request $request, $id)
    {
        $status           = $request->status;
        $proposal         = WebOrders::find($id);
        $proposal->status = $status;
        $proposal->save();

        Notification::create([
            'creator_id' => \Auth::user()->creatorId(),
            'user_id' => \Auth::user()->id,
            'type' => 'Order',
            'data' => json_encode([
                'action' => 'statusChange',
                'Proposal_id' => $proposal->proposal_id,
                'customer_id' => $proposal->customer_id,
                'status' => $proposal->status,
                'issue_date' => $proposal->issue_date,
                'due_date' => $proposal->due_date,
                'category_id' => $proposal->category_id,
                'proposal_details' => $proposal->proposal_details,
                'project' => $proposal->project,
            ]),
            'is_read' => 0,
        ]);

        return response()->json(['message' => __('Order status changed successfully.')]);
    }
    public function statusChangeProduct(Request $request, $id)
    {
        $status           = $request->status;
        $proposal         = WebOrdersProducst::find($id);
        $proposal->status = $status;
        $proposal->save();

        Notification::create([
            'creator_id' => \Auth::user()->creatorId(),
            'user_id' => \Auth::user()->id,
            'type' => 'product Order',
            'data' => json_encode([
                'action' => 'statusChange',
                'Proposal_id' => $proposal->proposal_id,
                'customer_id' => $proposal->customer_id,
                'status' => $proposal->status,
                'issue_date' => $proposal->issue_date,
                'due_date' => $proposal->due_date,
                'category_id' => $proposal->category_id,
                'proposal_details' => $proposal->proposal_details,
                'project' => $proposal->project,
            ]),
            'is_read' => 0,
        ]);

        return response()->json(['message' => __('Order status changed successfully.')]);
    }
}
