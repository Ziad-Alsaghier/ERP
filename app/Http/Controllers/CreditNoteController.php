<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Utility;
use App\Models\Customer;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\InvoiceProduct;
use App\Models\ProductService;
use Illuminate\Support\Facades\Crypt;
use App\Models\TransactionLines;

class CreditNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        if(\Auth::user()->can('manage credit note'))
        {
            $invoices = Invoice::where('created_by', \Auth::user()->creatorId())->get();

            return view('creditNote.index', compact('invoices'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function show( $invoice_id)
    {

        if(\Auth::user()->can('manage credit note'))
        {       
             $invoice_id  = Crypt::decrypt($invoice_id);
             $invoice = Invoice::
                where('created_by', \Auth::user()->creatorId())
                ->where('id', $invoice_id)
            ->first();


            return view('creditNote.show', compact('invoice'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create($invoice_id)
    {

        if(\Auth::user()->can('create credit note'))
        {

            $invoiceDue = Invoice::where('id', $invoice_id)->first();

            return view('creditNote.create', compact('invoiceDue', 'invoice_id'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request, $invoice_id)
    {

        if(\Auth::user()->can('create credit note'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'amount' => 'required|numeric',
                                   'date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $invoiceDue = Invoice::where('id', $invoice_id)->first();
            if($request->amount > $invoiceDue->getDue())
            {
                return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($invoiceDue->getDue()) . ' credit limit of this invoice.');
            }
            $invoice = Invoice::where('id', $invoice_id)->first();

            $credit              = new CreditNote();
            $credit->invoice     = $invoice_id;
            $credit->customer    = $invoice->customer_id;
            $credit->date        = $request->date;
            $credit->amount      = $request->amount;
            $credit->description = $request->description;
            $credit->save();

            $customer = Customer::find($invoice->customer_id);
            $balance = 0;
            if($customer->credit_balance != 0)
            {
                $balance = $customer->credit_balance + $request->amount;
            }

            $customer->credit_balance = $balance;
            $customer->save();

            Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

            return redirect()->back()->with('success', __('Credit Note successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($invoice_id, $creditNote_id)
    {
        if(\Auth::user()->can('edit credit note'))
        {

            $creditNote = CreditNote::find($creditNote_id);

            return view('creditNote.edit', compact('creditNote'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $invoice_id, $creditNote_id)
    {

        if(\Auth::user()->can('edit credit note'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'amount' => 'required|numeric',
                                   'date' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoiceDue = Invoice::where('id', $invoice_id)->first();
            $credit = CreditNote::find($creditNote_id);
            if($request->amount > $invoiceDue->getDue()+$credit->amount)
            {
                return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($invoiceDue->getDue()) . ' credit limit of this invoice.');
            }


            Utility::updateUserBalance('customer', $invoiceDue->customer_id, $credit->amount, 'credit');

            $credit->date        = $request->date;
            $credit->amount      = $request->amount;
            $credit->description = $request->description;
            $credit->save();

            Utility::updateUserBalance('customer', $invoiceDue->customer_id, $request->amount, 'debit');

            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'credit note',
                'data' => json_encode([
                    'action' => 'edit',
                    'invoice' => $invoice_id,
                    'customer' => $invoiceDue->customer_id,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'date' => $request->date
                ]),
                'is_read' => 0,
            ]);


            return redirect()->back()->with('success', __('Credit Note successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy($invoice_id, $creditNote_id)
    {
        if(\Auth::user()->can('delete credit note'))
        {

            $creditNote = CreditNote::find($creditNote_id);
            $creditNote->delete();

            Utility::updateUserBalance('customer', $creditNote->customer, $creditNote->amount, 'credit');
            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'credit note',
                'data' => json_encode([
                    'action' => 'delete',
                    'invoice' => $invoice_id,
                    'customer' => $creditNote->customer,
                    'amount' => $creditNote->amount
                ]),
                'is_read' => 0,
            ]);
            return redirect()->back()->with('success', __('Credit Note successfully deleted.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customCreate()
    {
        if(\Auth::user()->can('create credit note'))
        {

            $invoices = Invoice::where('created_by', \Auth::user()->creatorId())->get()->pluck('invoice_id', 'id');

            return view('creditNote.custom_create', compact('invoices'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customStore(Request $request)
    {

        if(\Auth::user()->can('create credit note'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                'invoice' => 'required|numeric',
                                'amount' => 'required|numeric',
                                'date' => 'required',
                            ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $invoice_id = $request->invoice;
            $invoiceDue = Invoice::where('id', $invoice_id)->first();

            if($request->amount > $invoiceDue->getDue())
            {
                return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($invoiceDue->getDue()) . ' credit limit of this invoice.');
            }
            $invoice             = Invoice::where('id', $invoice_id)->first();
            $credit              = new CreditNote();
            $credit->invoice     = $invoice_id;
            $credit->customer    = $invoice->customer_id;
            $credit->date        = $request->date;
            $credit->amount      = $request->amount;
            $credit->description = $request->description;
            $credit->save();

            Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'credit note',
                'data' => json_encode([
                    'action' => 'create',
                    'invoice' => $invoice_id,
                    'customer' => $invoice->customer_id,
                    'description' => $request->description,
                    'amount' => $request->amount,
                    'date' => $request->date
                ]),
                'is_read' => 0,
            ]);
            


            return redirect()->back()->with('success', __('Credit Note successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getinvoice(Request $request)
    {
        // edited muhammed 26/9
        $invoice = Invoice::with(['products.product:id,name'])
        ->where('id', $request->id)
        ->first();

        $newArr = [];
        foreach($invoice->products as $item){
            \Log::info($item->getSubTotal() + $item->getTotalTax() + $item->getTotalDiscount());
            $newArr[] = [
                'id'=> $item->id,
                'name' => $item->product->name,
                'price' => $item->getSubTotal() + $item->getTotalTax() + $item->getTotalDiscount(),
                'quantity'=>$item->quantity
            ];
        }
        \Log::info($newArr);
        $amount = $invoice->getDue();
        return response()->json([ 
            'amount' => $amount,
            'invoice' => $newArr,
        ]);
    }



















    // created by muhammed 28/9

    public function custom_Store(Request $request)
{
    // dd($request->all());
    if (\Auth::user()->can('create credit note')) {
        $validator = \Validator::make(
            $request->all(),
            [
                'invoice' => 'required|numeric',
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

        $invoice_id = $request->invoice;
        $invoiceDue = Invoice::where('id', $invoice_id)->first();

        if ($request->amount > $invoiceDue->getDue()) {
            return redirect()->back()->with('error', 'Maximum ' . \Auth::user()->priceFormat($invoiceDue->getDue()) . ' credit limit of this invoice.');
        }

        $invoice = Invoice::where('id', $invoice_id)->first();
        
        $deletedProductTotal = 0;
        if ($request->has('product_ids')) {
            
            \Log::info('products : ' . $request->product_ids[0]);
            foreach ($request->product_ids as $productId) {
                
                $invoiceProduct = InvoiceProduct::find($productId);
                \Log::info('invoiceProduct : ' . $invoiceProduct);

                if ($invoiceProduct) {
                    $productService = ProductService::find($invoiceProduct->product_id);

                    Utility::updateUserBalance('customer', $invoice->customer_id, $invoiceProduct->price, 'debit');
                    TransactionLines::where('reference_sub_id', $productService->id)
                        ->where('reference', 'Invoice')
                        ->delete();

                    $deletedProductTotal += $invoiceProduct->price;

                    InvoiceProduct::where('id', '=', $productId)->delete();
                    \Log::info('invoiceProduct : ' . $invoiceProduct);
                }
            }
        }
        \Log::info('deletedProductTotal : ' . $deletedProductTotal);
        // Create the credit 
        $credit = new CreditNote();
        $credit->invoice = $invoice_id;
        $credit->customer = $invoice->customer_id;
        $credit->date = $request->date;
        $credit->amount = $request->amount + $deletedProductTotal; 
        $credit->description = $request->description;
        $credit->save();

        Utility::updateUserBalance('customer', $invoice->customer_id, $credit->amount, 'debit');

        Notification::create([
            'creator_id' => \Auth::user()->creatorId(),
            'user_id' => \Auth::user()->id,
            'type' => 'credit note',
            'data' => json_encode([
                'action' => 'create',
                'invoice' => $invoice_id,
                'customer' => $invoice->customer_id,
                'description' => $request->description,
                'amount' => $credit->amount,
                'date' => $request->date
            ]),
            'is_read' => 0,
        ]);

        return redirect()->back()->with('success', __('Credit Note successfully created.'));
    } else {
        return redirect()->back()->with('error', __('Permission denied.'));
    }
}

}
