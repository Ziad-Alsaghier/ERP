<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillProduct;
use App\Models\BillAccount;
use App\Models\ProductService;
use App\Models\DebitNote;
use App\Models\TransactionLines;
use App\Models\Utility;
use App\Models\Notification;

use Illuminate\Http\Request;

class DebitNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (\Auth::user()->can('manage debit note')) {
            $bills = Bill::where('created_by', \Auth::user()->creatorId())->get();

            return view('debitNote.index', compact('bills'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create($bill_id)
    {
        if (\Auth::user()->can('create debit note')) {

            $billDue = Bill::where('id', $bill_id)->first();

            return view('debitNote.create', compact('billDue', 'bill_id'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request, $bill_id)
    {

        if (\Auth::user()->can('create debit note')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'amount' => 'required|numeric',
                    'date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $billDue = Bill::where('id', $bill_id)->first();

            if ($request->amount > $billDue->getDue()) {
                return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($billDue->getDue()) . ' credit limit of this bill.');
            }
            $bill               = Bill::where('id', $bill_id)->first();
            $debit              = new DebitNote();
            $debit->bill        = $bill_id;
            $debit->vendor      = $bill->vender_id;
            $debit->date        = $request->date;
            $debit->amount      = $request->amount;
            $debit->description = $request->description;
            $debit->save();

            Utility::updateUserBalance('vendor', $bill->vender_id, $request->amount, 'credit');


            return redirect()->back()->with('success', __('Debit Note successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($bill_id, $debitNote_id)
    {
        if (\Auth::user()->can('edit debit note')) {

            $debitNote = DebitNote::find($debitNote_id);

            return view('debitNote.edit', compact('debitNote'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $bill_id, $debitNote_id)
    {

        if (\Auth::user()->can('edit debit note')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'amount' => 'required|numeric',
                    'date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $billDue = Bill::where('id', $bill_id)->first();
            if ($request->amount > $billDue->getDue()) {
                return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($billDue->getDue()) . ' credit limit of this bill.');
            }


            $debit = DebitNote::find($debitNote_id);
            //            Utility::userBalance('vendor', $billDue->vender_id, $debit->amount, 'credit');
            Utility::updateUserBalance('vendor', $billDue->vender_id, $debit->amount, 'debit');



            $debit->date        = $request->date;
            $debit->amount      = $request->amount;
            $debit->description = $request->description;
            $debit->save();
            //            Utility::userBalance('vendor', $billDue->vender_id, $request->amount, 'debit');
            Utility::updateUserBalance('vendor', $billDue->vender_id, $request->amount, 'credit');

            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'debit note',
                'data' => json_encode([
                    'action' => 'edit',
                    'bill' => $debit->bill,
                    'vendor' => $debit->vendor,
                    'date' => $debit->date,
                    'amount' => $debit->amount,
                    'description' => $debit->description
                ]),
                'is_read' => 0,
            ]);


            return redirect()->back()->with('success', __('Debit Note successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy($bill_id, $debitNote_id)
    {
        if (\Auth::user()->can('delete debit note')) {
            $debitNote = DebitNote::find($debitNote_id);
            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'debit note',
                'data' => json_encode([
                    'action' => 'delete',
                    'bill' => $debitNote->bill,
                    'vendor' => $debitNote->vendor,
                    'date' => $debitNote->date,
                    'amount' => $debitNote->amount,
                    'description' => $debitNote->description
                ]),
                'is_read' => 0,
            ]);
            $debitNote->delete();
            Utility::updateUserBalance('vendor', $debitNote->vendor, $debitNote->amount, 'debit');
            return redirect()->back()->with('success', __('Debit Note successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customCreate()
    {
        if (\Auth::user()->can('create debit note')) {
            $bills = Bill::where('created_by', \Auth::user()->creatorId())->where('type', 'Bill')->get()->pluck('bill_id', 'id');

            return view('debitNote.custom_create', compact('bills'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customStore(Request $request)
    {
        if (\Auth::user()->can('create debit note')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'bill' => 'required|numeric',
                    'amount' => 'required|numeric',
                    'date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $bill_id = $request->bill;
            $billDue = Bill::where('id', $bill_id)->first();

            if ($request->amount > $billDue->getDue()) {
                return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($billDue->getDue()) . ' credit limit of this bill.');
            }
            $bill               = Bill::where('id', $bill_id)->first();
            $debit              = new DebitNote();
            $debit->bill        = $bill_id;
            $debit->vendor      = $bill->vender_id;
            $debit->date        = $request->date;
            $debit->amount      = $request->amount;
            $debit->description = $request->description;
            dd($debit);
            $debit->save();
            //            Utility::userBalance('vendor', $bill->vender_id, $request->amount, 'debit');
            Utility::updateUserBalance('vendor', $bill->vender_id, $request->amount, 'credit');
            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'debit note',
                'data' => json_encode([
                    'action' => 'create',
                    'bill' => $debit->bill,
                    'vendor' => $debit->vendor,
                    'date' => $debit->date,
                    'amount' => $debit->amount,
                    'description' => $debit->description
                ]),
                'is_read' => 0,
            ]);

            return redirect()->back()->with('success', __('Debit Note successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function custom_Store(Request $request)
    {
        if (\Auth::user()->can('create debit note')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'bill' => 'required|numeric',
                    'amount' => 'required|numeric',
                    'date' => 'required',
                    'product_ids' => 'nullable|array',
                    'product_ids.*' => 'sometimes',
                ]
            );
    
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
    
            $bill_id = $request->bill;
            $billDue = Bill::where('id', $bill_id)->first();
    
            if ($request->amount > $billDue->getDue()) {
                return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($billDue->getDue()) . ' credit limit of this bill.');
            }
    
            // Step 1: Create Debit Note
            $bill = Bill::where('id', $bill_id)->first();
            $debit = new DebitNote();
            $debit->bill = $bill_id;
            $debit->vendor = $bill->vender_id;
            $debit->date = $request->date;
    
            // Step 2: Initialize total product amount
            $totalProductAmount = 0;
    
            // Step 3: Delete Multiple Bill Products
            if ($request->has('product_ids')) {
                $product_ids = $request->product_ids;
    
                foreach ($product_ids as $product_id) {
                    // Fetch the corresponding bill product
                    $billProduct = BillProduct::find($product_id);
    
                    if ($billProduct) {
                        // Add the product's amount to the total
                        $totalProductAmount += $billProduct->getTotal();
    
                        // Update vendor balance for the deleted product
                        Utility::updateUserBalance('vendor', $bill->vender_id, $billProduct->getTotal(), 'credit');
    
                        // Find and delete the product's transaction lines
                        $productService = ProductService::find($billProduct->product_id);
                        TransactionLines::where('reference_sub_id', $productService->id)
                                        ->where('reference', 'Bill')
                                        ->delete();
    
                        // Delete the bill product
                        BillProduct::where('id', $billProduct->id)->delete();
                    }
                }
            }
    
            // Step 4: Calculate the amount to be set in the debit note
            $debit->amount = $request->amount - $totalProductAmount; // Subtract product amounts from the total amount
            $debit->description = $request->description;
            $debit->save();
    
            // Step 5: Update vendor balance after processing
            Utility::updateUserBalance('vendor', $bill->vender_id, $request->amount, 'credit');
    
            // Step 6: Send Notification
            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'debit note',
                'data' => json_encode([
                    'action' => 'create',
                    'bill' => $debit->bill,
                    'vendor' => $debit->vendor,
                    'date' => $debit->date,
                    'amount' => $debit->amount,
                    'description' => $debit->description
                ]),
                'is_read' => 0,
            ]);
    
            return redirect()->back()->with('success', __('Debit Note successfully created and bill products deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    


    public function getbill(Request $request)
    {

        $bill = Bill::with('items.product')->with('accounts.chartAccount')->where('id', $request->bill_id)->first();
        $totalAmount = 0;
        $itemsData = [];
        $accountsData =$bill->accounts;

        foreach ($bill->items as $item) {
            $itemTotal = $item->getTotal();
            $totalAmount += $bill->getDue();

            $itemsData[] = [
                'id' =>$item->id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'chartAcc' => $item->chart_account_id,
                'account' => $item->account,
                'item_total' => number_format($itemTotal, 2),
            ];
        }


        return response()->json([
            'amount' => number_format($totalAmount, 2),
            'items' => $itemsData,
            'accounts' => $accountsData,
        ]);
        // echo json_encode($bill->getDue());
    }
}
