<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\InvoicePayment;
use App\Models\ProductServiceCategory;
use App\Models\Revenue;
use App\Models\Transaction;
use App\Models\Utility;
use App\Models\TransactionLines;
use App\Models\Notification;
use App\Services\AccountsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RevenueController extends Controller
{

    protected $accountsService;
    public function __construct(AccountsService $accountsService)
    {
        $this->accountsService = $accountsService;
    }

    public function index(Request $request)
    {

        if(\Auth::user()->can('manage revenue'))
        {
            $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customer->prepend('Select Customer', '');

            $account = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('holder_name', 'id');
            $account->prepend('Select Account', '');

            $category = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'income')->get()->pluck('name', 'id');
            $category->prepend('Select Category', '');


            $query = Revenue::where('created_by', '=', \Auth::user()->creatorId())->orderBy('created_at', 'desc');
            // $query_2 = InvoicePayment::where('created_by', '=', \Auth::user()->creatorId());


            if(count(explode('to', $request->date)) > 1)
            {
                $date_range = explode(' to ', $request->date);
                $query->whereBetween('date', $date_range);

                $date_range = explode(' to ', $request->date);
                // $query_2->whereBetween('date', $date_range);
            }
            elseif(!empty($request->date))
            {
                $date_range = [$request->date , $request->date];
                $query->whereBetween('date', $date_range);

                $date_range = [$request->date , $request->date];
                // $query_2->whereBetween('date', $date_range);
            }

            if(!empty($request->customer))
            {
                $query->where('customer_id', '=', $request->customer);
                // $query_2->where('customer_id', '=', $request->customer);
            }
            if(!empty($request->account))
            {
                $query->where('account_id', '=', $request->account);
                // $query_2->where('account_id', '=', $request->account);
            }
            if(!empty($request->category))
            {
                $query->where('category_id', '=', $request->category);
                // $query_2->where('category_id', '=', $request->category);
            }

            if(!empty($request->payment))
            {
                $query->where('payment_method', '=', $request->payment);
                // $query_2->where('payment_method', '=', $request->payment);
            }

            $revenues = $query->with(['bankAccount','customer','category'])->get();
            // $revenues_all = $query_2->with(['bankAccount','customer','category'])->get();
            // dd($revenues,$revenues_all);

            return view('revenue.index', compact('revenues', 'customer', 'account', 'category'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function revenueNumber()
    {
        $latest = Revenue::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->revenue_id + 1;
    }
    public function create()
    {

        if(\Auth::user()->can('create revenue'))
        {
            $customers = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customers->prepend('--', 0);
            $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'income')->get()->pluck('name', 'id');
            $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('revenue.create', compact('customers', 'categories', 'accounts'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create revenue'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                'date' => 'required',
                                'amount' => 'required',
                                'account_id' => 'required',
                                // 'category_id' => 'required',
                            ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $revenue                 = new Revenue();
            $revenue->revenue_id     = $this->revenueNumber() ;
            $revenue->date           = $request->date;
            $revenue->amount         = $request->amount;
            $revenue->account_id     = $request->account_id;
            $revenue->customer_id    = $request->customer_id;
            $revenue->category_id    = $request->category_id;
            $revenue->payment_method = 0;
            $revenue->reference      = $request->reference;
            $revenue->description    = $request->description;
            if(!empty($request->add_receipt))
            {
                //storage limit
                $image_size = $request->file('add_receipt')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if($result==1)
                {
                    $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                    $revenue->add_receipt = $fileName;
                    $dir = 'uploads/revenue';
                    $url = '';
                    $path = Utility::upload_file($request, 'add_receipt', $fileName, $dir, []);
                    if ($path['flag'] == 0) {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
            }


            $revenue->created_by     = \Auth::user()->creatorId();
            // dd($revenue);
            $revenue->save();

            // accounting journal

            if($revenue){
                $account = ChartOfAccount::getAnAcount('Accounts receivable');

                $data = [
                    'account_id' => $account,
                    'transaction_type' => 'Credit',
                    'transaction_amount' => $revenue->amount,
                    'reference' => 'Revenue Payment',
                    'reference_id' => $revenue->id,
                    'reference_sub_id' => $revenue->invoice_id ?? 0,
                    'date' => $revenue->date,
                ];
                $dataMoney = [
                    'account_id' => $revenue->account_id,
                    'transaction_type' => 'Debit',
                    'transaction_amount' => $revenue->amount,
                    'reference' => 'Revenue Payment',
                    'reference_id' => $revenue->id,
                    'reference_sub_id' => $revenue->invoice_id ?? 0,
                    'date' => $revenue->date,
                ];
                Utility::addTransactionLines($data);
                Utility::addTransactionLines($dataMoney);

            }

            $category            = ProductServiceCategory::where('id', $request->category_id)->first();
            $revenue->payment_id = $revenue->id;
            $revenue->type       = 'Revenue';
            $revenue->category   = $category->name;
            $revenue->user_id    = $revenue->customer_id;
            $revenue->user_type  = 'Customer';
            $revenue->account    = $request->account_id;
            Transaction::addTransaction($revenue);

            $customer         = Customer::where('id', $request->customer_id)->first();
            $payment          = new InvoicePayment();
            $payment->name    = !empty($customer) ? $customer['name'] : '';
            $payment->date    = \Auth::user()->dateFormat($request->date);
            $payment->amount  = \Auth::user()->priceFormat($request->amount);
            $payment->invoice = '';

            if(!empty($customer))
            {
                Utility::userBalance('customer', $customer->id, $revenue->amount, 'credit');
            }

            // Utility::bankAccountBalance($request->account_id, $revenue->amount, 'credit');

            // $accountId = BankAccount::find($revenue->account_id);
            // $data = [
            //     'account_id' => $accountId->chart_account_id,
            //         'transaction_type' => 'Credit',
            //         'transaction_amount' => $revenue->amount,
            //         'reference' => 'Revenue',
            //         'reference_id' => $revenue->id,
            //         'reference_sub_id' => 0,
            //         'date' => $revenue->date,
            //     ];
            //     Utility::addTransactionLines($data);

            //For Notification
            $setting  = Utility::settings(\Auth::user()->creatorId());
            $revenueNotificationArr = [
                'revenue_amount' => \Auth::user()->priceFormat($request->amount),
                'customer_name' => !empty($customer)?$customer->name:'-',
                'user_name' => \Auth::user()->name,
                'revenue_date' => $request->date,
            ];
            //Slack Notification
            if(isset($setting['revenue_notification']) && $setting['revenue_notification'] ==1)
            {
                Utility::send_slack_msg('new_revenue', $revenueNotificationArr);
            }
            //Telegram Notification
            if(isset($setting['telegram_revenue_notification']) && $setting['telegram_revenue_notification'] ==1)
            {
                Utility::send_telegram_msg('new_revenue', $revenueNotificationArr);
            }
            //Twilio Notification
            if(isset($setting['twilio_revenue_notification']) && $setting['twilio_revenue_notification'] ==1)
            {
                Utility::send_twilio_msg(!empty($customer)?$customer->contact:'-','new_revenue', $revenueNotificationArr);
            }


            //webhook
            $module ='New Revenue';
            $webhook =  Utility::webhookSetting($module);
            if($webhook)
            {
                $parameter = json_encode($revenue);
                $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                if($status == true)
                {
                    return redirect()->route('revenue.index')->with('success', __('Revenue successfully created.'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }

            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'revenue',
                'data' => json_encode([
                    'action' => 'create',
                    'revenue_id' => $revenue->revenue_id,
                    'date' => $revenue->date,
                    'amount' => $revenue->amount,
                    'account_id' => $revenue->account_id,
                    'customer_id' => $revenue->customer_id,
                    'category_id' => $revenue->category_id,
                    'payment_method' => $revenue->payment_method,
                    'reference' => $revenue->reference,
                    'description' => $revenue->description,
                ]),
                'is_read' => 0,
            ]);

            return redirect()->route('revenue.index')->with('success', __('Revenue successfully created'). ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function show(Revenue $revenue)
    {
        if($revenue->created_by == \Auth::user()->creatorId())
        {
        $customers = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $customers->prepend('--', 0);
        $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'income')->get()->pluck('name', 'id');
        $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

        return view('revenue.show', compact('customers', 'categories', 'accounts', 'revenue'));
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit(Revenue $revenue)
    {
        if(\Auth::user()->can('edit revenue') && $revenue->created_by == \Auth::user()->creatorId())
        {

            $customers = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customers->prepend('--', 0);
            $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'income')->get()->pluck('name', 'id');
            $accounts   = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('revenue.edit', compact('customers', 'categories', 'accounts', 'revenue'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, Revenue $revenue)
    {

        if(\Auth::user()->can('edit revenue'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                'date' => 'required',
                                'amount' => 'required',
                                'account_id' => 'required',
                                'category_id' => 'required',
                            ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $customer = Customer::where('id', $request->customer_id)->first();
            if(!empty($customer))
            {
                Utility::userBalance('customer', $revenue->customer_id, $revenue->amount, 'debit');
            }

            Utility::bankAccountBalance($revenue->account_id, $revenue->amount, 'debit');


            if(!empty($customer))
            {
                Utility::userBalance('customer', $customer->id, $request->amount, 'credit');
            }

            Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

            $revenue->date           = $request->date;
            $revenue->amount         = $request->amount;
            $revenue->account_id     = $request->account_id;
            $revenue->customer_id    = $request->customer_id;
            $revenue->category_id    = $request->category_id;
            $revenue->payment_method = 0;
            $revenue->reference      = $request->reference;
            $revenue->description    = $request->description;
            if(!empty($request->add_receipt))
            {
                //storage limit
                $file_path = '/uploads/revenue/'.$revenue->add_receipt;
                $image_size = $request->file('add_receipt')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if($result==1)
                {
                    Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                    $path = storage_path('uploads/revenue/' . $revenue->add_receipt);

                    if(file_exists($path))
                    {
                        \File::delete($path);
                    }
                    $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                    $revenue->add_receipt = $fileName;
                    $dir        = 'uploads/revenue';
                    $url = '';
                    $path = Utility::upload_file($request,'add_receipt',$fileName,$dir,[]);
                    if($path['flag']==0){
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
            }

            $revenue->save();

            $category            = ProductServiceCategory::where('id', $request->category_id)->first();
            $revenue->category   = $category->name;
            $revenue->payment_id = $revenue->id;
            $revenue->type       = 'Revenue';
            $revenue->account    = $request->account_id;
            Transaction::editTransaction($revenue);

            $accountId = BankAccount::find($revenue->account_id);
            $data = [
                'account_id' => $accountId->chart_account_id,
                'transaction_type' => 'Credit',
                'transaction_amount' => $revenue->amount,
                'reference' => 'Revenue',
                'reference_id' => $revenue->id,
                'reference_sub_id' => 0,
                'date' => $revenue->date,
            ];
            Utility::addTransactionLines($data);

            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'revenue',
                'data' => json_encode([
                    'action' => 'edit',
                    'revenue_id' => $revenue->revenue_id,
                    'date' => $revenue->date,
                    'amount' => $revenue->amount,
                    'account_id' => $revenue->account_id,
                    'customer_id' => $revenue->customer_id,
                    'category_id' => $revenue->category_id,
                    'payment_method' => $revenue->payment_method,
                    'reference' => $revenue->reference,
                    'description' => $revenue->description,
                ]),
                'is_read' => 0,
            ]);

            return redirect()->route('revenue.index')->with('success', __('Revenue Updated Successfully'). ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function revenue($revenue_id){
        $id = decrypt($revenue_id);
        $revenue = Revenue::where('id', $id)->first();
        $customers = Customer::where('created_by', '=', $revenue->created_by)
        ->where('id','=',$revenue->customer_id)
        ->first();

        $settings = Utility::settings();
        if(!empty($revenue)){
            return view('revenue.pdf', compact('revenue','customers','settings'));
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function destroy(Revenue $revenue)
    {

        if(\Auth::user()->can('delete revenue'))
        {
            if($revenue->created_by == \Auth::user()->creatorId())
            {
                if(!empty($revenue->add_receipt))
                {
                    //storage limit
                    $file_path = '/uploads/revenue/'.$revenue->add_receipt;
                    $result = Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);

                }

                Notification::create([
                    'creator_id' => \Auth::user()->creatorId(),
                    'user_id' => \Auth::user()->id,
                    'type' => 'revenue',
                    'data' => json_encode([
                        'action' => 'delete',
                        'revenue_id' => $revenue->revenue_id,
                        'date' => $revenue->date,
                        'amount' => $revenue->amount,
                        'account_id' => $revenue->account_id,
                        'customer_id' => $revenue->customer_id,
                        'category_id' => $revenue->category_id,
                        'payment_method' => $revenue->payment_method,
                        'reference' => $revenue->reference,
                        'description' => $revenue->description,
                    ]),
                    'is_read' => 0,
                ]);
                TransactionLines::where('reference_id',$revenue->id)->where('reference','Revenue')->delete();
                $revenue->delete();
                $type = 'Revenue';
                $user = 'Customer';
                Transaction::destroyTransaction($revenue->id, $type, $user);

                if($revenue->customer_id != 0)
                {
                    Utility::userBalance('customer', $revenue->customer_id, $revenue->amount, 'debit');
                }


                Utility::bankAccountBalance($revenue->account_id, $revenue->amount, 'debit');

                return redirect()->route('revenue.index')->with('success', __('Revenue successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
