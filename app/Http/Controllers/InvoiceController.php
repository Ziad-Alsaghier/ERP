<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Exports\InvoiceExport;
use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\CustomField;
use App\Models\Invoice;
use App\Models\InvoiceBankTransfer;
use App\Models\InvoicePayment;
use App\Models\Revenue;
use App\Models\InvoiceProduct;
use App\Models\Plan;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\StockReport;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Utility;
use App\Models\TransactionLines;
use App\Models\Notification;
use App\Models\Unit;
use App\Services\AccountsService;
use App\Traits\ZatcaServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
// use Packages\Zatca\InvoiceServices;
// use Packages\Zatca\Sandbox;
// use Packages\Zatca\ZatcaQRCodePhaseOne;
// use Packages\Zatca\ZatcaQRCodePhaseTwo;
// use Packages\Zatca\InvoiceXML;
// use Packages\Zatca\InvoiceServices;
// use Packages\Zatca\Sandbox;
// use Packages\Zatca\ZatcaQRCodePhaseOne;
// use Packages\Zatca\ZatcaQRCodePhaseTwo;
// use Packages\Zatca\InvoiceXML;
use Prgayman\Zatca\Facades\Zatca;
use DateTime;
use DateTimeZone;
use App\Models\WebOrders;
use App\Models\WebOrdersProducst;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use ZATCA\GenerateQrCode;
use ZATCA\Tags\InvoiceDate;
use ZATCA\Tags\InvoiceTaxAmount;
use ZATCA\Tags\InvoiceTotalAmount;
use ZATCA\Tags\Seller;
use ZATCA\Tags\TaxNumber;
use ZATCA\EGS;
// use ZATCA\GenerateQrCode;
// use ZATCA\Tags\InvoiceDate;
// use ZATCA\Tags\InvoiceTaxAmount;
// use ZATCA\Tags\InvoiceTotalAmount;
// use ZATCA\Tags\Seller;
// use ZATCA\Tags\TaxNumber;

class InvoiceController extends Controller
{
    use ZatcaServices;
    public function __construct(AccountsService $accountsService)
    {
        $this->accountsService = $accountsService;
    }


    public function index(Request $request)
    {
        if (\Auth::user()->can('manage invoice')) {
            $customer = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customer->prepend('Select Customer', '');
            $status = Invoice::$statues;
            $query = Invoice::where('created_by', '=', \Auth::user()->creatorId());

            if (!empty($request->customer)) {
                $query->where('customer_id', '=', $request->customer);
            }
            if (count(explode('to', $request->issue_date)) > 1) {
                $date_range = explode(' to ', $request->issue_date);
                $query->whereBetween('issue_date', $date_range);
            } elseif (!empty($request->issue_date)) {
                $date_range = [$request->issue_date, $request->issue_date];
                $query->whereBetween('issue_date', $date_range);
            }
            if (!empty($request->status)) {
                $query->where('status', '=', $request->status);
            }

            // إضافة الترتيب بناءً على تاريخ الإنشاء من الأحدث إلى الأقدم
            $query->orderBy('created_at', 'desc');

            $invoices = $query->get();

            return view('invoice.index', compact('invoices', 'customer', 'status'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
    public function create($customerId = 0)
    {
        if (\Auth::user()->can('create invoice')) {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();
            $invoice_number = \Auth::user()->invoiceNumberFormat($this->invoiceNumber());
            $customers = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $customers->prepend('Select Customer', '');
            if (\Auth::user()->creatorId() == 6) {
                $orders =  WebOrders::latest()->get();
            } else {
                $orders = [];
            }
            $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->where('type', 'income')->get()->pluck('name', 'id');
            $category->prepend('Select Category', '');
            $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $product_services->prepend('--', '');

            return view('invoice.create', compact('customers', 'invoice_number', 'product_services', 'category', 'customFields', 'customerId', 'orders'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function customer(Request $request)
    {
        $customer = Customer::where('id', '=', $request->id)->first();
        return view('invoice.customer_detail', compact('customer'));
    }

    public function product(Request $request)
    {
        $data['product'] = $product = ProductService::find($request->product_id);
        $data['unit'] = (!empty($product->unit)) ? $product->unit->name : '';
        $data['taxRate'] = $taxRate = !empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0;
        $data['taxes'] = !empty($product->tax_id) ? $product->tax($product->tax_id) : 0;
        $salePrice = $product->sale_price;
        $quantity = 1;
        $taxPrice = ($taxRate / 100) * ($salePrice * $quantity);
        $data['totalAmount'] = ($salePrice * $quantity);

        return json_encode($data);
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create invoice')) {


            $validator = \Validator::make(
                $request->all(),
                [
                    'customer_id' => 'required',
                    'issue_date' => 'required',
                    'due_date' => 'required',
                    // 'category_id' => 'required',
                    'items' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if (empty($request->invoice_details)) {
                $request->invoice_details = "";
            }
            if (empty($request->due_date)) {
                $request->due_date = 0;
            }
            if (isset($request->make_project)) {
                $request->make_project = 1;
            } else {
                $request->make_project = 0;
            }
            $status = Invoice::$statues;
            $invoice = new Invoice();
            $invoice->invoice_id            = $this->invoiceNumber();
            $invoice->customer_id           = $request->customer_id;
            $invoice->status = 0;
            $invoice->issue_date            = $request->issue_date;
            $invoice->due_date              = $request->due_date;
            $invoice->category_id           = $request->category_id;
            $invoice->order_id            = $request->order_id;
            $invoice->ref_number            = $request->ref_number;
            $invoice->invoice_details       = $request->invoice_details;
            $invoice->project               = $request->make_project;
            //            $invoice->discount_apply = isset($request->discount_apply) ? 1 : 0;
            $invoice->user_id = \Auth::user()->id;
            $invoice->created_by = \Auth::user()->creatorId();

            $invoice->save();
            CustomField::saveData($invoice, $request->customField);
            $products = $request->items;
            // dd($products);
            for ($i = 0; $i < count($products); $i++) {

                $invoiceProduct = new InvoiceProduct();
                $invoiceProduct->invoice_id = $invoice->id;
                $invoiceProduct->product_id = $products[$i]['item'];
                $invoiceProduct->quantity = $products[$i]['quantity'];
                $invoiceProduct->tax = $products[$i]['tax'];
                //                $invoiceProduct->discount    = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                $invoiceProduct->discount = $products[$i]['discount'];
                $invoiceProduct->price = $products[$i]['price'];
                $invoiceProduct->description = $products[$i]['description'];
                $invoiceProduct->save();

                //inventory management (Quantity)
                if (isset($products[$i]['type']) && $products[$i]['type'] != 'custom') {
                    Utility::total_quantity('minus', $invoiceProduct->quantity, $invoiceProduct->product_id);
                }
                //For Notification
                $setting = Utility::settings(\Auth::user()->creatorId());
                $customer = Customer::find($request->customer_id);
                $invoiceNotificationArr = [
                    'invoice_number' => \Auth::user()->invoiceNumberFormat($invoice->invoice_id),
                    'user_name' => \Auth::user()->name,
                    'invoice_issue_date' => $invoice->issue_date,
                    'invoice_due_date' => $invoice->due_date,
                    'customer_name' => $customer->name,
                ];
                //Slack Notification
                if (isset($setting['invoice_notification']) && $setting['invoice_notification'] == 1) {
                    Utility::send_slack_msg('new_invoice', $invoiceNotificationArr);
                }
                //Telegram Notification
                if (isset($setting['telegram_invoice_notification']) && $setting['telegram_invoice_notification'] == 1) {
                    Utility::send_telegram_msg('new_invoice', $invoiceNotificationArr);
                }
                //Twilio Notification
                if (isset($setting['twilio_invoice_notification']) && $setting['twilio_invoice_notification'] == 1) {
                    Utility::send_twilio_msg($customer->contact, 'new_invoice', $invoiceNotificationArr);
                }

                $product = ProductService::where('id', '=', $products[$i]['item'])->pluck('quantity', 'name');
                if ($product->first() <= 0) {
                    $product_quty_alrt[] = $product;
                }
            }


            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'invoice',
                'data' => json_encode([
                    'action' => 'create',
                    'invoice_id' => $invoice->invoice_id,
                    'customer_id' => $invoice->customer_id,
                    'status' => $invoice->status,
                    'issue_date' => $invoice->issue_date,
                    'due_date' => $invoice->due_date,
                    'category_id' => $invoice->category_id,
                    'ref_number' => $invoice->ref_number,
                    'invoice_details' => $invoice->invoice_details,
                    'project' => $invoice->project,

                ]),
                'is_read' => 0,
            ]);
            // dd($product_quty_alrt);

            //Product Stock Report
            $type = 'invoice';
            $type_id = $invoice->id;
            StockReport::where('type', '=', 'invoice')->where('type_id', '=', $invoice->id)->delete();
            $description = $invoiceProduct->quantity . '  ' . __(' quantity sold in invoice') . ' ' . \Auth::user()->invoiceNumberFormat($invoice->invoice_id);
            Utility::addProductStock($invoiceProduct->product_id, $invoiceProduct->quantity, $type, $description, $type_id);

            //webhook
            $module = 'New Invoice';
            $webhook = Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($invoice);
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    return redirect()->route('invoice.index', $invoice->id)->with('success', __('Invoice successfully created.'));
                } else {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }

            if (isset($product_quty_alrt)) {
                return redirect()->route('invoice.index', $invoice->id)->with('success', __('Invoice successfully created.'))->with('alert', $product_quty_alrt);
            } else {
                return redirect()->route('invoice.index', $invoice->id)->with('success', __('Invoice successfully created.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($ids)
    {
        if (\Auth::user()->can('edit invoice')) {
            $id = Crypt::decrypt($ids);
            $invoice = Invoice::find($id);
            $invoice_number = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);
            $customers = Customer::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->where('type', 'income')->get()->pluck('name', 'id');
            $category->prepend('Select Category', '');
            $product_services = ProductService::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $invoice->customField = CustomField::getData($invoice, 'invoice');
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();

            return view('invoice.edit', compact('customers', 'product_services', 'invoice', 'invoice_number', 'category', 'customFields'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, Invoice $invoice)
    {

        if (\Auth::user()->can('edit invoice')) {
            if ($invoice->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'customer_id' => 'required',
                        'issue_date' => 'required',
                        'due_date' => 'required',
                        'category_id' => 'required',
                        'items' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('invoice.index')->with('error', $messages->first());
                }

                if (empty($request->invoice_details)) {
                    $request->invoice_details = "";
                }
                if (empty($request->due_date)) {
                    $request->due_date = 0;
                }
                if (isset($request->make_project)) {
                    $request->make_project = 1;
                } else {
                    $request->make_project = 0;
                }
                $invoice->customer_id = $request->customer_id;
                $invoice->issue_date = $request->issue_date;
                $invoice->due_date = $request->due_date;
                $invoice->ref_number = $request->ref_number;
                $invoice->invoice_details     = $request->invoice_details;
                $invoice->project              = $request->make_project;
                //                $invoice->discount_apply = isset($request->discount_apply) ? 1 : 0;
                $invoice->category_id = $request->category_id;
                $invoice->save();

                Utility::starting_number($invoice->invoice_id + 1, 'invoice');
                CustomField::saveData($invoice, $request->customField);
                $products = $request->items;

                for ($i = 0; $i < count($products); $i++) {
                    $invoiceProduct = InvoiceProduct::find($products[$i]['id']);

                    if ($invoiceProduct == null) {
                        $invoiceProduct = new InvoiceProduct();
                        $invoiceProduct->invoice_id = $invoice->id;

                        Utility::total_quantity('minus', $products[$i]['quantity'], $products[$i]['item']);

                        $updatePrice = ($products[$i]['price'] * $products[$i]['quantity']) + ($products[$i]['itemTaxPrice']) - ($products[$i]['discount']);
                        Utility::updateUserBalance('customer', $request->customer_id, $updatePrice, 'credit');
                    } else {
                        Utility::total_quantity('plus', $invoiceProduct->quantity, $invoiceProduct->product_id);
                    }

                    if (isset($products[$i]['item'])) {
                        $invoiceProduct->product_id = $products[$i]['item'];
                    }

                    $invoiceProduct->quantity = $products[$i]['quantity'];
                    $invoiceProduct->tax = $products[$i]['tax'];
                    $invoiceProduct->discount = $products[$i]['discount'];
                    $invoiceProduct->price = $products[$i]['price'];
                    $invoiceProduct->description = $products[$i]['description'];
                    $invoiceProduct->save();

                    if ($products[$i]['id'] > 0) {
                        Utility::total_quantity('minus', $products[$i]['quantity'], $invoiceProduct->product_id);
                    }

                    //Product Stock Report
                    $type = 'invoice';
                    $type_id = $invoice->id;
                    StockReport::where('type', '=', 'invoice')->where('type_id', '=', $invoice->id)->delete();
                    $description = $products[$i]['quantity'] . '  ' . __(' quantity sold in invoice') . ' ' . \Auth::user()->invoiceNumberFormat($invoice->invoice_id);
                    if (empty($products[$i]['id'])) {
                        Utility::addProductStock($products[$i]['item'], $products[$i]['quantity'], $type, $description, $type_id);
                    }
                }

                TransactionLines::where('reference_id', $invoice->id)->where('reference', 'Invoice')->delete();

                $invoice_products = InvoiceProduct::where('invoice_id', $invoice->id)->get();
                foreach ($invoice_products as $invoice_product) {
                    $product = ProductService::find($invoice_product->product_id);
                    $totalTaxPrice = 0;
                    if ($invoice_product->tax != null) {
                        $taxes = \App\Models\Utility::tax($invoice_product->tax);
                        foreach ($taxes as $tax) {
                            $taxPrice = \App\Models\Utility::taxRate($tax->rate, $invoice_product->price, $invoice_product->quantity, $invoice_product->discount);
                            $totalTaxPrice += $taxPrice;
                        }
                    }

                    $itemAmount = ($invoice_product->price * $invoice_product->quantity) - ($invoice_product->discount) + $totalTaxPrice;

                    $data = [
                        'account_id' => $product->sale_chartaccount_id,
                        'transaction_type' => 'Credit',
                        'transaction_amount' => $itemAmount,
                        'reference' => 'Invoice',
                        'reference_id' => $invoice->id,
                        'reference_sub_id' => $product->id,
                        'date' => $invoice->issue_date,
                    ];
                    Utility::addTransactionLines($data);
                }

                Notification::create([
                    'creator_id' => \Auth::user()->creatorId(),
                    'user_id' => \Auth::user()->id,
                    'type' => 'invoice',
                    'data' => json_encode([
                        'action' => 'edit',
                        'invoice_id' => $invoice->invoice_id,
                        'customer_id' => $invoice->customer_id,
                        'status' => $invoice->status,
                        'issue_date' => $invoice->issue_date,
                        'due_date' => $invoice->due_date,
                        'category_id' => $invoice->category_id,
                        'ref_number' => $invoice->ref_number,
                        'invoice_details' => $invoice->invoice_details,
                        'project' => $invoice->project,

                    ]),
                    'is_read' => 0,
                ]);

                return redirect()->route('invoice.index')->with('success', __('Invoice successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function invoiceNumber()
    {
        $latest = Invoice::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->invoice_id + 1;
    }

    public function show($ids)
    {

        if (\Auth::user()->can('show invoice')) {
            try {
                $id = Crypt::decrypt($ids);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Invoice Not Found.'));
            }
            $id = Crypt::decrypt($ids);
            $invoice = Invoice::with(['creditNote', 'payments.bankAccount', 'items.product.unit'])->find($id);

            if (!empty($invoice->created_by) == \Auth::user()->creatorId()) {
                $invoicePayment = InvoicePayment::where('invoice_id', $invoice->id)->first();

                $customer = $invoice->customer;
                $iteams = $invoice->items;
                $user = \Auth::user();

                // start for storage limit note
                $invoice_user = User::find($invoice->created_by);
                $user_plan = Plan::getPlan($invoice_user->plan);
                // end for storage limit note

                $invoice->customField = CustomField::getData($invoice, 'invoice');
                $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();

                $creditnote = CreditNote::where('invoice', $invoice->id)->first();


                $dateTime = new DateTime($invoice->issue_date, new DateTimeZone('UTC'));
                $formattedDate = $dateTime->format('Y-m-d\TH:i:s\Z');

                $total_price = \Auth::user()->priceFormat($invoice->getSubTotal());
                $total_price_with_tax = \Auth::user()->priceFormat($invoice->getTotal());


                $total_price_numeric_with_tax = preg_replace('/[^0-9.]/', '', $total_price_with_tax);
                $total_price_numeric = preg_replace('/[^0-9.]/', '', $total_price);

                $total_price_numeric_tax = ($total_price_numeric * 0.15);

                // dd($total_price_numeric_with_tax);

                $settings_data = Utility::settingsById(\Auth::user()->creatorId());

            }
                $data = $this->data($customer, $settings_data, $invoice->items, $invoice);
                    $qrCode = qrCode($data['information'], $data['items'], $data['invoice'], $width = 300);


                return view('invoice.view', compact('invoice', 'customer', 'iteams', 'invoicePayment', 'customFields', 'user', 'invoice_user', 'user_plan', 'creditnote', 'qrCode'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }

    }

    public function destroy(Invoice $invoice, Request $request)
    {
        if (\Auth::user()->can('delete invoice')) {
            if ($invoice->created_by == \Auth::user()->creatorId()) {

                Notification::create([
                    'creator_id' => \Auth::user()->creatorId(),
                    'user_id' => \Auth::user()->id,
                    'type' => 'invoice',
                    'data' => json_encode([
                        'action' => 'delete',
                        'invoice_id' => $invoice->invoice_id,
                        'customer_id' => $invoice->customer_id,
                        'status' => $invoice->status,
                        'issue_date' => $invoice->issue_date,
                        'due_date' => $invoice->due_date,
                        'category_id' => $invoice->category_id,
                        'ref_number' => $invoice->ref_number,
                        'invoice_details' => $invoice->invoice_details,
                        'project' => $invoice->project
                    ]),
                    'is_read' => 0,
                ]);

                foreach ($invoice->payments as $invoices) {
                    Utility::bankAccountBalance($invoices->account_id, $invoices->amount, 'debit');

                    $invoicepayment = InvoicePayment::find($invoices->id);
                    $invoices->delete();
                    $invoicepayment->delete();
                }

                if ($invoice->customer_id != 0 && $invoice->status != 0) {
                    Utility::updateUserBalance('customer', $invoice->customer_id, $invoice->getDue(), 'debit');
                }


                TransactionLines::where('reference_id', $invoice->id)->where('reference', 'Invoice')->delete();
                TransactionLines::where('reference_id', $invoice->id)->Where('reference', 'Invoice Payment')->delete();

                CreditNote::where('invoice', '=', $invoice->id)->delete();

                InvoiceProduct::where('invoice_id', '=', $invoice->id)->delete();
                $invoice->delete();
                return redirect()->route('invoice.index')->with('success', __('Invoice successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function productDestroy(Request $request)
    {

        if (\Auth::user()->can('delete invoice product')) {
            $invoiceProduct = InvoiceProduct::find($request->id);

            if ($invoiceProduct) {
                $invoice = Invoice::find($invoiceProduct->invoice_id);
                $productService = ProductService::find($invoiceProduct->product_id);

                Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

                TransactionLines::where('reference_sub_id', $productService->id)->where('reference', 'Invoice')->delete();

                InvoiceProduct::where('id', '=', $request->id)->delete();
            }



            return redirect()->back()->with('success', __('Invoice product successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function productDestroyJson(Request $request)
    {

        if (\Auth::user()->can('delete invoice product')) {
            $invoiceProduct = InvoiceProduct::find($request->id);

            if ($invoiceProduct) {
                $invoice = Invoice::find($invoiceProduct->invoice_id);
                $productService = ProductService::find($invoiceProduct->product_id);

                Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

                TransactionLines::where('reference_sub_id', $productService->id)->where('reference', 'Invoice')->delete();

                InvoiceProduct::where('id', '=', $request->id)->delete();
            }



            return redirect()->back()->with('success', __('Invoice product successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customerInvoice(Request $request)
    {
        if (\Auth::user()->can('manage customer invoice')) {

            $status = Invoice::$statues;
            $query = Invoice::where('customer_id', '=', \Auth::user()->id)->where('status', '!=', '0')->where('created_by', \Auth::user()->creatorId());

            if (!empty($request->issue_date)) {
                $date_range = explode(' - ', $request->issue_date);
                $query->whereBetween('issue_date', $date_range);
            }

            if (!empty($request->status)) {
                $query->where('status', '=', $request->status);
            }
            $invoices = $query->get();

            return view('invoice.index', compact('invoices', 'status'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function customerInvoiceShow($id)
    {

        $invoice = Invoice::with('payments.bankAccount')->find($id);

        $user = User::where('id', $invoice->created_by)->first();
        if ($invoice->created_by == $user->creatorId()) {
            $customer = $invoice->customer;
            $iteams = $invoice->items;

            if ($user->type == 'super admin') {
                return view('invoice.view', compact('invoice', 'customer', 'iteams', 'user'));
            } elseif ($user->type == 'company') {
                return view('invoice.customer_invoice', compact('invoice', 'customer', 'iteams', 'user'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function sent($id)
    {
        \DB::beginTransaction();
        try {
            if (\Auth::user()->can('send invoice')) {
                // Send Email
                $setings = Utility::settings();
                $invAccount = ChartOfAccount::getAnAcount('Inventory');
                if ($setings['customer_invoice_sent'] == 1) {
                    $invoice = Invoice::where('id', $id)->first();
                    $invoice->send_date = date('Y-m-d');
                    $invoice->status = 1;
                    $invoice->save();

                    $customer = Customer::where('id', $invoice->customer_id)->first();
                    $invoice->name = !empty($customer) ? $customer->name : '';
                    $invoice->invoice = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);

                    $invoiceId = Crypt::encrypt($invoice->id);
                    $invoice->url = route('invoice.pdf', $invoiceId);
                    // dd($customer);
                    Utility::updateUserBalance('customer', $customer->id, $invoice->getTotal(), 'credit');

                    $invoice_products = InvoiceProduct::where('invoice_id', $invoice->id)->get();
                    foreach ($invoice_products as $invoice_product) {
                        // dd($invoice_product->product_id);
                        $product = ProductService::find($invoice_product->product_id);
                        if (!$product->sale_chartaccount_id || !$product->expense_chartaccount_id) {
                            continue; // Skip this product if accounts are not set
                        }
                        $totalTaxPrice = 0;
                        if ($invoice_product->tax != null) {
                            $taxes = \App\Models\Utility::tax($invoice_product->tax);
                            foreach ($taxes as $tax) {
                                $taxPrice = \App\Models\Utility::taxRate($tax->rate, $invoice_product->price, $invoice_product->quantity, $invoice_product->discount);
                                $totalTaxPrice += $taxPrice;
                            }
                        }

                        $itemAmount = ($invoice_product->price * $invoice_product->quantity) - ($invoice_product->discount); //+ $totalTaxPrice !!old!!
                        $costAmount = $product->cost_rate * $invoice_product->quantity;
                        $incData = [
                            'account_id' => $product->sale_chartaccount_id,
                            'transaction_type' => 'Credit',
                            'transaction_amount' => $itemAmount,
                            'reference' => 'Invoice',
                            'reference_id' => $invoice->id,
                            'reference_sub_id' => $product->id,
                            'date' => $invoice->issue_date,
                        ];
                        $expData = [
                            'account_id' => $product->expense_chartaccount_id,
                            'transaction_type' => 'Debit',
                            'transaction_amount' => $costAmount,
                            'reference' => 'Invoice',
                            'reference_id' => $invoice->id,
                            'reference_sub_id' => $product->id,
                            'date' => $invoice->issue_date,
                        ];
                        if ($invAccount) {
                            $invData = [
                                'account_id' => $invAccount,
                                'transaction_type' => 'Credit',
                                'transaction_amount' => $costAmount,
                                'reference' => 'Invoice',
                                'reference_id' => $invoice->id,
                                'reference_sub_id' => $product->id,
                                'date' => $invoice->issue_date,
                            ];
                            Utility::addTransactionLines($invData);
                        }

                        Utility::addTransactionLines($incData);
                        Utility::addTransactionLines($expData);

                        // dump($data);
                    }
                    $this->transactionToAccountsReceivable($invoice);
                    if ($invoice->getTotalTax() > 0) {
                        $this->transactionToVatPayable($invoice);
                    }

                    $customerArr = [
                        'customer_name' => $customer->name,
                        'customer_email' => $customer->email,
                        'invoice_name' => $customer->name,
                        'invoice_number' => $invoice->invoice,
                        'invoice_url' => $invoice->url,

                    ];
                    Notification::create([
                        'creator_id' => \Auth::user()->creatorId(),
                        'user_id' => \Auth::user()->id,
                        'type' => 'invoice',
                        'data' => json_encode([
                            'action' => 'sent',
                            'invoice_id' => $invoice->invoice_id,
                            'customer_id' => $invoice->customer_id,
                            'status' => $invoice->status,
                            'issue_date' => $invoice->issue_date,
                            'due_date' => $invoice->due_date,
                            'category_id' => $invoice->category_id,
                            'ref_number' => $invoice->ref_number,
                            'invoice_details' => $invoice->invoice_details,
                            'project' => $invoice->project
                        ]),
                        'is_read' => 0,
                    ]);

                    $resp = Utility::sendEmailTemplate('customer_invoice_sent', [$customer->id => $customer->email], $customerArr);

                    return redirect()->back()->with('success', __('Invoice successfully sent.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
                }
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', __('Failed to send invoice.'));
        }
    }

    public function resent($id)
    {
        if (\Auth::user()->can('send invoice')) {
            $invoice = Invoice::where('id', $id)->first();

            $customer = Customer::where('id', $invoice->customer_id)->first();
            $invoice->name = !empty($customer) ? $customer->name : '';
            $invoice->invoice = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);

            $invoiceId = Crypt::encrypt($invoice->id);
            $invoice->url = route('invoice.pdf', $invoiceId);
            $customerArr = [

                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'invoice_name' => $customer->name,
                'invoice_number' => $invoice->invoice,
                'invoice_url' => $invoice->url,

            ];

            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'invoice',
                'data' => json_encode([
                    'action' => 'resent',
                    'invoice_id' => $invoice->invoice_id,
                    'customer_id' => $invoice->customer_id,
                    'status' => $invoice->status,
                    'issue_date' => $invoice->issue_date,
                    'due_date' => $invoice->due_date,
                    'category_id' => $invoice->category_id,
                    'ref_number' => $invoice->ref_number,
                    'invoice_details' => $invoice->invoice_details,
                    'project' => $invoice->project
                ]),
                'is_read' => 0,
            ]);

            $resp = Utility::sendEmailTemplate('customer_invoice_sent', [$customer->id => $customer->email], $customerArr);

            return redirect()->back()->with('success', __('Invoice successfully sent.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payment($invoice_id)
    {
        if (\Auth::user()->can('create payment invoice')) {
            $invoice = Invoice::where('id', $invoice_id)->first();

            $customers = Customer::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $categories = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $accounts = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('invoice.payment', compact('customers', 'categories', 'accounts', 'invoice'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function createPayment(Request $request, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        $totalTaxPrice = 0;
        $finaltotal = 0;
        foreach ($invoice->items as $key => $item) {
            $description = json_decode($item->description, true);
            if (is_array($description) && isset($description['info']['type']) && $description['info']['type'] == 'custom') {
                // حساب الإجمالي للـ custom
                $total_with_tax = 0; // قيمة افتراضية في حال عدم وجود "الاجمالي بعد الضريبة"
                foreach ($description as $key_desc => $value) {
                    if ($key_desc == 'التسعيرة النهائية') {
                        foreach ($value as $key_val => $val) {
                            if ($key_val == 'الاجمالي بعد الضريبة') {
                                $total_with_tax = floatval(str_replace(',', '', $val));
                            }
                        }
                    }
                }
                $finaltotal += $total_with_tax;
            } else {
                // حساب الإجمالي للعنصر العادي
                $totalTaxPrice = 0;
                if (!empty($item->tax)) {
                    $getTaxData = Utility::getTaxData();
                    foreach (explode(',', $item->tax) as $tax) {
                        $taxPrice = \Utility::taxRate($getTaxData[$tax]['rate'], $item->price, $item->quantity);
                        $totalTaxPrice += $taxPrice;
                    }
                }
                $finaltotal += ($item->price * $item->quantity) + $totalTaxPrice;
            }
        }
        // dd($finaltotal);
        $finaltotal = $finaltotal + 0.0001;
        if ($request->amount > $finaltotal) {
            return redirect()->back()->with('error', __('Invoice payment amount should not greater than subtotal.'));
        }

        if (\Auth::user()->can('create payment invoice')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'date' => 'required',
                    'amount' => 'required',
                    'account_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }


            $invoiceCategory = ProductServiceCategory::where('id', $invoice->category_id)->first();
            // dd($invoiceCategory->chart_account_id);
            if (!isset($invoiceCategory->chart_account_id)) {
                $invoiceCategory = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->where('type', 'income')->where('def', 1)->first();
                if (empty($invoiceCategory)) {
                    return redirect()->back()->with('error', __('incoming should be set default at least for one.'));
                }
            }


            $invoicePayment = new InvoicePayment();
            $invoicePayment->invoice_id = $invoice_id;
            $invoicePayment->date = $request->date;
            $invoicePayment->amount = $request->amount;
            $invoicePayment->account_id = $request->account_id;
            $invoicePayment->customer_id = $invoice->customer_id;
            $invoicePayment->category_id = $invoice->category_id;
            $invoicePayment->payment_method = 0;
            $invoicePayment->reference = $request->reference;
            $invoicePayment->description = $request->description;
            $invoicePayment->created_by = \Auth::user()->creatorId();
            if (!empty($request->add_receipt)) {
                //storage limit
                $image_size = $request->file('add_receipt')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                if ($result == 1) {
                    $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                    $request->add_receipt->storeAs('uploads/payment', $fileName);
                    $invoicePayment->add_receipt = $fileName;
                }
            }

            $invoicePayment->save();
            if ($invoicePayment) {
                $account = ChartOfAccount::getAnAcount('Accounts receivable');

                $data = [
                    'account_id' => $account,
                    'transaction_type' => 'Credit',
                    'transaction_amount' => $invoicePayment->amount,
                    'reference' => 'Invoice Payment',
                    'reference_id' => $invoicePayment->id,
                    'reference_sub_id' => $invoicePayment->invoice_id,
                    'date' => $invoicePayment->date,
                ];
                $dataMoney = [
                    'account_id' => $invoicePayment->account_id,
                    'transaction_type' => 'Debit',
                    'transaction_amount' => $invoicePayment->amount,
                    'reference' => 'Invoice Payment',
                    'reference_id' => $invoicePayment->id,
                    'reference_sub_id' => $invoicePayment->invoice_id,
                    'date' => $invoicePayment->date,
                ];
                Utility::addTransactionLines($data);
                Utility::addTransactionLines($dataMoney);
            }

            $revenue                 = new Revenue();
            $revenue->revenue_id     = $this->revenueNumber();
            $revenue->date           = $request->date;
            $revenue->amount         = $request->amount;
            $revenue->account_id     = $request->account_id;
            $revenue->customer_id    = $invoice->customer_id;
            $revenue->category_id    = $invoice->category_id;
            $revenue->payment_method = 0;
            $revenue->reference      = $request->reference;
            $revenue->description    = $request->description;
            if (!empty($request->add_receipt)) {
                //storage limit
                $image_size = $request->file('add_receipt')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if ($result == 1) {
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
            $revenue->save();





            $invoice = Invoice::where('id', $invoice_id)->first();
            $due = $invoice->getDue();
            $total = $invoice->getTotal();
            if ($invoice->status == 0) {
                $invoice->send_date = date('Y-m-d');
                $invoice->save();
            }

            if ($due <= 0) {
                $invoice->status = 4;
                $invoice->save();
            } else {
                $invoice->status = 3;
                $invoice->save();
            }
            $invoicePayment->user_id = $invoice->customer_id;
            $invoicePayment->user_type = 'Customer';
            $invoicePayment->type = 'Partial';
            $invoicePayment->created_by = \Auth::user()->id;
            $invoicePayment->payment_id = $invoicePayment->id;
            $invoicePayment->category = 'Invoice';
            $invoicePayment->account = $request->account_id;

            Transaction::addTransaction($invoicePayment);
            $customer = Customer::where('id', $invoice->customer_id)->first();

            $payment = new InvoicePayment();
            $payment->name = $customer['name'];
            $payment->date = \Auth::user()->dateFormat($request->date);
            $payment->amount = \Auth::user()->priceFormat($request->amount);
            $payment->invoice = 'invoice ' . \Auth::user()->invoiceNumberFormat($invoice->invoice_id);
            $payment->dueAmount = \Auth::user()->priceFormat($invoice->getDue());

            Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

            // Utility::bankAccountBalance($request->account_id, $request->amount, 'credit');

            // $invoicePayments = InvoicePayment::where('invoice_id', $invoice->id)->get();

            // // #momen Add to the bank the balance
            // foreach ($invoicePayments as $invoicePayment) {
            //     $accountId = BankAccount::find($invoicePayment->account_id);
            //     $data = [
            //         'account_id' => $accountId->chart_account_id,
            //         'transaction_type' => 'Credit',
            //         'transaction_amount' => $invoicePayment->amount,
            //         'reference' => 'Invoice Payment',
            //         'reference_id' => $invoice->id,
            //         'reference_sub_id' => $invoicePayment->id,
            //         'date' => $invoicePayment->date,
            //     ];
            //     $data2 = [
            //         'account_id' => $invoiceCategory->chart_account_id,
            //         'transaction_type' => 'dibt',
            //         'transaction_amount' => $invoicePayment->amount,
            //         'reference' => 'Invoice Payment',
            //         'reference_id' => $invoice->id,
            //         'reference_sub_id' => $invoicePayment->id,
            //         'date' => $invoicePayment->date,
            //     ];
            //     Utility::addTransactionLines($data);
            //     Utility::addTransactionLines($data2);
            // }




            // Send Email
            $setings = Utility::settings();
            if ($setings['new_invoice_payment'] == 1) {
                $customer = Customer::where('id', $invoice->customer_id)->first();
                $invoicePaymentArr = [
                    'invoice_payment_name' => $customer->name,
                    'invoice_payment_amount' => $payment->amount,
                    'invoice_payment_date' => $payment->date,
                    'payment_dueAmount' => $payment->dueAmount,

                ];

                $resp = Utility::sendEmailTemplate('new_invoice_payment', [$customer->id => $customer->email], $invoicePaymentArr);
            }

            //webhook
            $module = 'New Invoice Payment';
            $webhook = Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($invoice);
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    return redirect()->back()->with('success', __('Payment successfully added.') . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : '') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
                } else {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }
            return redirect()->back()->with('success', __('Payment successfully added.') . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : '') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }
    }

    public function paymentDestroy(Request $request, $invoice_id, $payment_id)
    {
        //        dd($invoice_id,$payment_id);

        if (\Auth::user()->can('delete payment invoice')) {
            $payment = InvoicePayment::find($payment_id);

            InvoicePayment::where('id', '=', $payment_id)->delete();

            InvoiceBankTransfer::where('id', '=', $payment_id)->delete();

            TransactionLines::where('reference_sub_id', $payment_id)->where('reference', 'Invoice Payment')->delete();

            $invoice = Invoice::where('id', $invoice_id)->first();
            $due = $invoice->getDue();
            $total = $invoice->getTotal();

            if ($due > 0 && $total != $due) {
                $invoice->status = 3;
            } else {
                $invoice->status = 2;
            }

            if (!empty($payment->add_receipt)) {
                //storage limit
                $file_path = '/uploads/payment/' . $payment->add_receipt;
                $result = Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
            }

            $invoice->save();
            $type = 'Partial';
            $user = 'Customer';
            Transaction::destroyTransaction($payment_id, $type, $user);

            Utility::updateUserBalance('customer', $invoice->customer_id, $payment->amount, 'credit');

            Utility::bankAccountBalance($payment->account_id, $payment->amount, 'debit');

            return redirect()->back()->with('success', __('Payment successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function paymentReminder($invoice_id)
    {

        //        dd($invoice_id);
        $invoice = Invoice::find($invoice_id);
        $customer = Customer::where('id', $invoice->customer_id)->first();
        $invoice->dueAmount = \Auth::user()->priceFormat($invoice->getDue());
        $invoice->name = $customer['name'];
        $invoice->date = \Auth::user()->dateFormat($invoice->send_date);
        $invoice->invoice = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);

        //For Notification
        $setting = Utility::settings(\Auth::user()->creatorId());
        $customer = Customer::find($invoice->customer_id);
        $reminderNotificationArr = [
            'invoice_number' => \Auth::user()->invoiceNumberFormat($invoice->invoice_id),
            'customer_name' => $customer->name,
            'user_name' => \Auth::user()->name,
        ];

        //Twilio Notification
        if (isset($setting['twilio_reminder_notification']) && $setting['twilio_reminder_notification'] == 1) {
            Utility::send_twilio_msg($customer->contact, 'invoice_payment_reminder', $reminderNotificationArr);
        }

        // Send Email
        $setings = Utility::settings();
        if ($setings['new_payment_reminder'] == 1) {
            $invoice = Invoice::find($invoice_id);
            $customer = Customer::where('id', $invoice->customer_id)->first();
            $invoice->dueAmount = \Auth::user()->priceFormat($invoice->getDue());
            $invoice->name = $customer['name'];
            $invoice->date = \Auth::user()->dateFormat($invoice->send_date);
            $invoice->invoice = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);

            $reminderArr = [

                'payment_reminder_name' => $invoice->name,
                'invoice_payment_number' => $invoice->invoice,
                'invoice_payment_dueAmount' => $invoice->dueAmount,
                'payment_reminder_date' => $invoice->date,

            ];

            $resp = Utility::sendEmailTemplate('new_payment_reminder', [$customer->id => $customer->email], $reminderArr);
        }

        return redirect()->back()->with('success', __('Payment reminder successfully send.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
    }

    public function customerInvoiceSend($invoice_id)
    {
        return view('customer.invoice_send', compact('invoice_id'));
    }

    public function customerInvoiceSendMail(Request $request, $invoice_id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $email = $request->email;
        $invoice = Invoice::where('id', $invoice_id)->first();

        $customer = Customer::where('id', $invoice->customer_id)->first();
        $invoice->name = !empty($customer) ? $customer->name : '';
        $invoice->invoice = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);

        $invoiceId = Crypt::encrypt($invoice->id);
        $invoice->url = route('invoice.pdf', $invoiceId);

        try {
            Mail::to($email)->send(new CustomerInvoiceSend($invoice));
        } catch (\Exception $e) {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }

        return redirect()->back()->with('success', __('Invoice successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function shippingDisplay(Request $request, $id)
    {
        $invoice = Invoice::find($id);

        if ($request->is_display == 'true') {
            $invoice->shipping_display = 1;
        } else {
            $invoice->shipping_display = 0;
        }
        $invoice->save();

        return redirect()->back()->with('success', __('Shipping address status successfully changed.'));
    }

    public function duplicate($invoice_id)
    {
        if (\Auth::user()->can('duplicate invoice')) {
            $invoice = Invoice::where('id', $invoice_id)->first();
            $duplicateInvoice = new Invoice();
            $duplicateInvoice->invoice_id = $this->invoiceNumber();
            $duplicateInvoice->customer_id = $invoice['customer_id'];
            $duplicateInvoice->issue_date = date('Y-m-d');
            $duplicateInvoice->due_date = $invoice['due_date'];
            $duplicateInvoice->send_date = null;
            $duplicateInvoice->category_id = $invoice['category_id'];
            $duplicateInvoice->ref_number = $invoice['ref_number'];
            $duplicateInvoice->status = 0;
            $duplicateInvoice->shipping_display = $invoice['shipping_display'];
            $duplicateInvoice->created_by = $invoice['created_by'];
            $duplicateInvoice->save();

            if ($duplicateInvoice) {
                $invoiceProduct = InvoiceProduct::where('invoice_id', $invoice_id)->get();
                foreach ($invoiceProduct as $product) {
                    $duplicateProduct = new InvoiceProduct();
                    $duplicateProduct->invoice_id = $duplicateInvoice->id;
                    $duplicateProduct->product_id = $product->product_id;
                    $duplicateProduct->quantity = $product->quantity;
                    $duplicateProduct->tax = $product->tax;
                    $duplicateProduct->discount = $product->discount;
                    $duplicateProduct->price = $product->price;
                    $duplicateProduct->save();
                }
            }
            Notification::create([
                'creator_id' => \Auth::user()->creatorId(),
                'user_id' => \Auth::user()->id,
                'type' => 'invoice',
                'data' => json_encode([
                    'action' => 'duplicate',
                    'invoice_id' => $invoice->invoice_id,
                    'customer_id' => $invoice->customer_id,
                    'status' => $invoice->status,
                    'issue_date' => $invoice->issue_date,
                    'due_date' => $invoice->due_date,
                    'category_id' => $invoice->category_id,
                    'ref_number' => $invoice->ref_number,
                    'invoice_details' => $invoice->invoice_details,
                    'project' => $invoice->project
                ]),
                'is_read' => 0,
            ]);

            return redirect()->back()->with('success', __('Invoice duplicate successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function previewInvoice($template, $color)
    {
        $objUser = \Auth::user();
        $settings = Utility::settings();
        $invoice = new Invoice();

        $customer = new \stdClass();
        $customer->email = '<Email>';
        $customer->shipping_name = '<Customer Name>';
        $customer->shipping_country = '<Country>';
        $customer->shipping_state = '<State>';
        $customer->shipping_city = '<City>';
        $customer->shipping_phone = '<Customer Phone Number>';
        $customer->shipping_zip = '<Zip>';
        $customer->shipping_address = '<Address>';
        $customer->billing_name = '<Customer Name>';
        $customer->billing_country = '<Country>';
        $customer->billing_state = '<State>';
        $customer->billing_city = '<City>';
        $customer->billing_phone = '<Customer Phone Number>';
        $customer->billing_zip = '<Zip>';
        $customer->billing_address = '<Address>';

        $totalTaxPrice = 0;
        $taxesData = [];

        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $item = new \stdClass();
            $item->name = 'Item ' . $i;
            $item->quantity = 1;
            $item->tax = 5;
            $item->discount = 50;
            $item->price = 100;
            $item->unit = 1;
            $item->description = 'XYZ';

            $taxes = [
                'Tax 1',
                'Tax 2',
            ];

            $itemTaxes = [];
            foreach ($taxes as $k => $tax) {
                $taxPrice = 10;
                $totalTaxPrice += $taxPrice;
                $itemTax['name'] = 'Tax ' . $k;
                $itemTax['rate'] = '10 %';
                $itemTax['price'] = '$10';
                $itemTax['tax_price'] = 10;
                $itemTaxes[] = $itemTax;
                if (array_key_exists('Tax ' . $k, $taxesData)) {
                    $taxesData['Tax ' . $k] = $taxesData['Tax 1'] + $taxPrice;
                } else {
                    $taxesData['Tax ' . $k] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $items[] = $item;
        }

        $invoice->invoice_id = 1;
        $invoice->issue_date = date('Y-m-d H:i:s');
        $invoice->due_date = date('Y-m-d H:i:s');
        $invoice->itemData = $items;
        $invoice->status = 0;
        $invoice->totalTaxPrice = 60;
        $invoice->totalQuantity = 3;
        $invoice->totalRate = 300;
        $invoice->totalDiscount = 10;
        $invoice->taxesData = $taxesData;
        $invoice->created_by = $objUser->creatorId();

        $invoice->customField = [];
        $customFields = [];

        $preview = 1;
        $color = '#' . $color;
        $font_color = Utility::getFontColor($color);

        $logo = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $invoice_logo = Utility::getValByName('invoice_logo');
        if (isset($invoice_logo) && !empty($invoice_logo)) {
            $img = Utility::get_file('invoice_logo/') . $invoice_logo;
        } else {
            $img = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));
        }

        return view('invoice.templates.' . $template, compact('invoice', 'preview', 'color', 'img', 'settings', 'customer', 'font_color', 'customFields'));
    }

    public function invoice($invoice_id)
    {
        $utility = new Utility;

        $settings = Utility::settings();

        $invoiceId = Crypt::decrypt($invoice_id);
        $invoice = Invoice::where('id', $invoiceId)->first();

        $data = DB::table('settings');
        $data = $data->where('created_by', '=', $invoice->created_by);
        $data1 = $data->get();

        foreach ($data1 as $row) {
            $settings[$row->name] = $row->value;
        }

        $customer = $invoice->customer;
        $items = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate = 0;
        $totalDiscount = 0;
        $finaltotal = 0;
        $taxesData = [];
        foreach ($invoice->items as $product) {
            $item = new \stdClass();
            $item->name = !empty($product->product) ? $product->product->name : '';
            $item->quantity = $product->quantity;
            $item->tax = $product->tax;
            $item->unit = !empty($product->product) ? $product->product->unit_id : '';
            $item->discount = $product->discount;
            $item->price = $product->price;
            $item->description = $product->description;

            $totalQuantity += $item->quantity;
            $totalRate += $item->price;
            $totalDiscount += $item->discount;

            $taxes = Utility::tax($product->tax);

            $itemTaxes = [];
            if (!empty($item->tax)) {
                foreach ($taxes as $tax) {
                    $taxPrice = Utility::taxRate($tax->rate, $item->price, $item->quantity, $item->discount);
                    $totalTaxPrice += $taxPrice;

                    $itemTax['name'] = $tax->name;
                    $itemTax['rate'] = $tax->rate . '%';
                    $itemTax['price'] = Utility::priceFormat($settings, $taxPrice);
                    $itemTax['tax_price'] = $taxPrice;
                    $itemTaxes[] = $itemTax;

                    if (array_key_exists($tax->name, $taxesData)) {
                        $taxesData[$tax->name] = $taxesData[$tax->name] + $taxPrice;
                    } else {
                        $taxesData[$tax->name] = $taxPrice;
                    }
                }
                $item->itemTax = $itemTaxes;
            } else {
                $item->itemTax = [];
            }
            $items[] = $item;
        }

        $invoice->itemData = $items;
        $invoice->totalTaxPrice = $totalTaxPrice;
        $invoice->totalQuantity = $totalQuantity;
        $invoice->totalRate = $totalRate;
        $invoice->totalDiscount = $totalDiscount;
        $invoice->taxesData = $taxesData;
        $invoice->customField = CustomField::getData($invoice, 'invoice');
        $customFields = [];
        if (!empty(\Auth::user())) {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();
        }


        $logo = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $settings_data = \App\Models\Utility::settingsById($invoice->created_by);
        $invoice_logo = $settings_data['invoice_logo'];
        if (isset($invoice_logo) && !empty($invoice_logo)) {
            $img = Utility::get_file('invoice_logo/') . $invoice_logo;
        } else {
            $img = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));
        }

            // Start Setting Data
            $settings_data = Utility::settingsById($invoice->created_by); // Setting Data
            $dateTime = new DateTime($invoice->issue_date, new DateTimeZone('UTC'));// Format Date
            $formattedDate = $dateTime->format('Y-m-d\TH:i:s\Z');

            $total_price = Utility::priceFormat($settings_data, $invoice->getSubTotal()); // Sup Total
            $total_price_with_tax = Utility::priceFormat($settings_data, $invoice->getTotal()); // Get Total Tax

            $total_price_numeric_with_tax = preg_replace('/[^0-9.]/', '', $total_price_with_tax);
            $total_price_numeric = preg_replace('/[^0-9.]/', '', $total_price);

            $total_price_numeric_tax = $total_price_numeric * 0.15;
            // End Setting Data

            /** This Section Get
             * total_item
             * total_discount
             * total_vat
             * total_one
             * total_product
             */
            if (isset($invoice->itemData) && count($invoice->itemData) > 0) {
                $total_product = 0;
                $total_discount = 0;
                $total_vat = 0;
                $total_one = 0;
                foreach ($invoice->itemData as $key => $item){
                    $description = json_decode($item->description, true);
                    if(is_array($description) && $description['info']['type'] == 'custom'){
                        foreach ($description as $key_des => $value) {
                            if ($key_des == 'التسعيرة النهائية') {
                                foreach ($value as $ke => $val) {
                                    if($ke == 'سعر الافرادي'){
                                        $total_product += $val *$item->quantity;
                                    }
                                }
                            }
                        }

                    }else{
                    $unitName = Unit::find($item->unit);
                    $itemtax = 0;

                    if (!empty($item->itemTax)) {
                        foreach ($item->itemTax as $taxes) {
                            $itemtax += $taxes['tax_price'];
                        }
                    }
                    $total_item = $item->price * $item->quantity    ;
                    $total_discount += $item->discount * $item->quantity;
                    $total_vat += $itemtax;
                    $total_one += $item->price * $item->quantity;
                    $total_product += $total_item;
                    }
                }
            }
            // Ene Section



        $data = $this->data($customer, $settings_data, $invoice->items, $invoice);
        $qrCode = qrCode($data['information'], $data['items'], $data['invoice'], $width = 200);

        $setting = Utility::settings();
        $logo = Utility::get_file('uploads/logo/');
        $company_logo_dark = $setting['company_logo_dark'] ?? '';
        $company_logo_light = $setting['company_logo_light'] ?? '';
        $company_logo_small = $setting['company_small_logo'] ?? '';
        $logo_url = url($logo) . '/' . $company_logo_dark;
        $orders = $invoice;




        if ($invoice) {
            $color = '#' . $settings['invoice_color'];
            $font_color = Utility::getFontColor($color);

            return view('report.crm.sales_invoice' ,
            get_defined_vars()); // We Need Settings Latter Using Templates
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveTemplateSettings(Request $request)
    {

        $post = $request->all();
        unset($post['_token']);

        if (isset($post['invoice_template']) && (!isset($post['invoice_color']) || empty($post['invoice_color']))) {
            $post['invoice_color'] = "ffffff";
        }

        if ($request->invoice_logo) {
            $dir = 'invoice_logo/';
            $invoice_logo = \Auth::user()->id . '_invoice_logo.png';
            $validation = [
                'mimes:' . 'png',
                'max:' . '20480',
            ];
            $path = Utility::upload_file($request, 'invoice_logo', $invoice_logo, $dir, $validation);

            if ($path['flag'] == 0) {
                return redirect()->back()->with('error', __($path['msg']));
            }
            $post['invoice_logo'] = $invoice_logo;
        }

        foreach ($post as $key => $data) {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                [
                    $data,
                    $key,
                    \Auth::user()->creatorId(),
                ]
            );
        }

        return redirect()->back()->with('success', __('Invoice Setting updated successfully'));
    }

    public function items(Request $request)
    {
        $items = InvoiceProduct::where('invoice_id', $request->invoice_id)->where('product_id', $request->product_id)->first();

        return json_encode($items);
    }

    public function invoiceLink($invoiceId)
    {
        try {
            $id = Crypt::decrypt($invoiceId);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Invoice Not Found.'));
        }

        $id = Crypt::decrypt($invoiceId);
        $invoice = Invoice::with(['creditNote', 'payments.bankAccount', 'items.product.unit'])->find($id);

        $settings = Utility::settingsById($invoice->created_by);

        if (!empty($invoice)) {

            $user_id = $invoice->created_by;
            $user = User::find($user_id);
            $invoicePayment = InvoicePayment::where('invoice_id', $invoice->id)->get();
            $customer = $invoice->customer;
            $iteams = $invoice->items;
            $invoice->customField = CustomField::getData($invoice, 'invoice');
            $customFields = CustomField::where('module', '=', 'invoice')->get();
            $company_payment_setting = Utility::getCompanyPaymentSetting($user_id);

            // start for storage limit note
            $user_plan = Plan::find($user->plan);
            // end for storage limit note

            return view('invoice.customer_invoice', compact('settings', 'invoice', 'customer', 'iteams', 'invoicePayment', 'customFields', 'user', 'company_payment_setting', 'user_plan'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function export()
    {
        $name = 'invoice_' . date('Y-m-d i:h:s');
        $data = Excel::download(new InvoiceExport(), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }

    private function transactionToAccountsReceivable($invoice)
    {
        $name = 'Accounts receivable'; // Account name
        $type = 'Debit'; // Receivables are debited (asset)
        $amount = $invoice->getTotal() ?? 0; // Total amount excluding VAT
        $ref = 'Invoice'; // Reference
        $refId = $invoice->id ?? 0; // Invoice ID
        $refSubId = 0; // Optional
        $date = $invoice->issue_date ?? now()->toDateString(); // Invoice date

        $this->accountsService->addTransaction(
            $name,
            $type,
            $amount,
            $ref,
            $refId,
            $refSubId,
            $date
        );
    }

    private function transactionToVatPayable($invoice)
    {
        $name = 'VAT payable'; // Account name
        $type = 'Credit'; // VAT is credited (liability)
        $amount = $invoice->getTotalTax() ?? 0; // Extract the tax amount
        $ref = 'Invoice'; // Reference
        $refId = $invoice->id ?? 0; // Invoice ID
        $refSubId = 0; // Optional
        $date = $invoice->issue_date ?? now()->toDateString(); // Invoice date

        $this->accountsService->addTransaction(
            $name,
            $type,
            $amount,
            $ref,
            $refId,
            $refSubId,
            $date
        );
    }

    // private function transactionToRevenueOfProductsAndServicesSales($invoice)
    // {
    //     $name = 'Revenue from Products and Services Sales'; // Account name
    //     $type = 'credit'; // Revenue is credited
    //     $amount = ($invoice->getTotal() - $invoice->getTotalTax()) ?? 0; // Revenue excluding VAT
    //     $ref = 'Invoice'; // Reference
    //     $refId = $invoice->id ?? 0; // Invoice ID
    //     $refSubId = 0; // Optional
    //     $date = $invoice->issue_date ?? now()->toDateString(); // Invoice date

    //     $this->accountsService->addTransaction(
    //         $name,
    //         $type,
    //         $amount,
    //         $ref,
    //         $refId,
    //         $refSubId,
    //         $date
    //     );
    // }

    // private function transactionToCostOfGoodsSold($invoice, $invProduct)
    // {
    //     $name = 'Cost of goods sold'; // Account name
    //     $type = 'Debit'; // COGS is debited (expense)
    //     $amount = $invProduct->product->cost_rate * $invProduct->quantity ?? 0; // Extract the total cost
    //     $ref = 'Invoice'; // Reference
    //     $refId = $invoice->id ?? 0; // Invoice ID
    //     $refSubId = $invProduct->id; // Optional
    //     $date = $invoice->issue_date ?? now()->toDateString(); // Invoice date

    //     $this->accountsService->addTransaction(
    //         $name,
    //         $type,
    //         $amount,
    //         $ref,
    //         $refId,
    //         $refSubId,
    //         $date
    //     );
    // }


    // private function transactionToInventory($invProduct, $invoice)
    // {
    //     $name = 'Inventory';
    //     $type = 'credit';
    //     $amount = $invProduct->product->cost_rate * $invProduct->quantity ?? 0;
    //     $ref = 'Invoice';
    //     $refId = $invoice->id ?? 0;
    //     $refSubId = $invProduct->id;
    //     $date = $invoice->issue_date ?? now()->toDateString();

    //     $this->accountsService->addTransaction(
    //         $name,
    //         $type,
    //         $amount,
    //         $ref,
    //         $refId,
    //         $refSubId,
    //         $date
    //     );
    // }
}
