@php
    $setting = \App\Models\Utility::settings();
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_logo_dark = $setting['company_logo_dark'] ?? '';
    $company_logo_light = $setting['company_logo_light'] ?? '';
    $company_logo_small = $setting['company_small_logo'] ?? '';
    $logo_url = url($logo) . '/' . $company_logo_dark;
    $orders = $purchase;
    // dd($customer);
    // dd($settings);
    // dd($purchase);

    if (isset($purchase->itemData) && count($purchase->itemData) > 0) {
    $total_product = 0;
    $total_discount = 0;
    $total_vat = 0;
    $total_one = 0;
    foreach ($purchase->itemData as $key => $item){
                if(!empty($item->description)){
                        $description = json_decode($item->description, true);
                        if($description != Null){
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

                }
        }

            }

    }
}

@endphp
<html>

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>Invoice</title>
    <link rel="stylesheet" href="{{ asset('assets/invoice/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/invoice/style.css') }}">

    <style>
        @media print {
            body {
                height: 100%;
                background: none;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .invoice-box {
                background: none;
                max-width: none;
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
                font-size: none;
                line-height: none;
                color: none;
                min-height: none;
            }

            .container {
                max-width: 100%;
            }

            .printbutton {
                display: none;
            }

            html,
            body {
                background: white
            }
        }

        .invoice-detailes {
            margin-bottom: -25px;
        }

        table.table.mb-0.invoice-body {
            margin-top: -16px;
        }
    </style>
</head>

<body>

    <div class="invoice-box">
        <div class="container">

            <div class="row">
                <div class="col-2">
                    @if (!empty($settings['social_link_bill']))
                        {!! DNS2D::getBarcodeHTML($settings['social_link_bill'], 'QRCODE', 2, 2) !!}
                    @else
                                        {!! DNS2D::getBarcodeHTML(
                            route('purchase.link.copy', \Crypt::encrypt($purchase->purchase_id)),
                            'QRCODE',
                            1.5,
                            1.5,
                        ) !!}
                    @endif
                </div>
                <div class="col-5">
                    @if ($settings['header_bill'])
                        {!! $settings['header_bill'] !!}
                    @endif
                </div>
                <div class="col-3">
                    <div class="title-of-invoce_con">
                        <p class="title-of-invoce">{{__("Purchase Order", [], 'ar')}}  <br> {{ __('Purchase Order',[],'en')}}</p>
                    </div>
                </div>
                <div class="col-2">
                    <img class="float-end" width="70px" src="{{ $logo_url }}">
                </div>

            </div>
            <div class="row">
                <div class="col-6">
                    <span class="left">Notes:</span><span class="right">الملاحظات</span>
                    <textarea name="" style="overflow-y: hidden;" id="textarea_note"></textarea>
                </div>

                <div class="col-6 information" style="height: 77px">
                    <table class="table table-borderd">
                        <tr>
                            <td>Est Date:</td>
                            @if (isset($purchase))
                                <td>{{ date('Y-m-d', strtotime($purchase->created_at)) }}</td>
                            @else
                                <td><?php    echo date('Y-m-d'); ?></td>
                            @endif
                            <td>تاريخ الاصدار</td>
                        </tr>
                        @if (isset($purchase->order_id))
                            <tr>
                                <td>REF. No.:</td>
                                <td>{{ App\Models\Utility::ordersNumberFormat($purchase->order_id) }}</td>
                                <td>الرقم المرجعي</td>
                            </tr>
                        @endif
                        <tr>
                            <td>User</td>
                            <td>{{ $purchase->user_id != null ? $purchase->user->name : '-' }}</td>
                            <td>المستخدم</td>
                        </tr>
                        <tr>
                            <td>bracnh</td>
                            <td>{{ App\Models\Employee::where('user_id', $purchase->user_id)->first()->branch->name ?? '-' }}
                            </td>
                            <td>الفرع</td>
                        </tr>
                    </table>
                </div>

            </div>
            <div class="row information m-0">
                <div class="col-6 p-0">
                    <table class="table">
                        <tr>
                            <td colspan="3">Customer Info - بيانات العميل</td>
                        </tr>
                        <tr>
                            <td>Customer Name</td>
                            <td>
                                {{ !empty($customer->name) ? $customer->name : '' }}
                            </td>
                            <td>اسم العميل</td>
                        </tr>
                        <tr>
                            <td>Mobile No.</td>
                            <td>
                                {{ !empty($customer->shipping_phone) ? $customer->shipping_phone : '' }}
                            </td>
                            <td>رقم الجوال</td>
                        </tr>
                        <tr>
                            <td>address</td>
                            <td>
                                {{ !empty($customer->billing_name) ? $customer->billing_name : '' }} -
                                {{ !empty($customer->billing_state) ? $customer->billing_state : '' . ', ' }} -
                                {{ !empty($customer->billing_address) ? $customer->billing_address : '' }}
                            </td>
                            <td>العنوان</td>
                        </tr>
                        <tr>
                            <td>City</td>
                            <td>
                                {{ !empty($customer->billing_city) ? $customer->billing_city : '' . ', ' }}
                            </td>
                            <td>المدينة</td>
                        </tr>
                        <tr>
                            <td>Country</td>
                            <td>
                                {{ !empty($customer->billing_country) ? $customer->billing_country : '' . ', ' }}
                            </td>
                            <td>الدولة</td>
                        </tr>
                        <tr>
                            <td>P.O.Box</td>
                            <td>
                                {{ !empty($customer->billing_zip) ? $customer->billing_zip : '' . ', ' }}
                            </td>
                            <td>الرمز البريدي</td>
                        </tr>
                        <tr>
                            <td>C.R.</td>
                            <td>
                                {{ !empty($customer->tax_number) ? $customer->tax_number : '' }}
                            </td>
                            <td>السجل التجاري</td>
                        </tr>
                        <tr>
                            <td>Vat No.</td>
                            <td>
                                {{ !empty($customer->vat_number) ? $customer->vat_number : '' }}
                            </td>
                            <td>الرقم الضريبي</td>
                        </tr>
                    </table>
                </div>
                <div class="col-6 p-0">
                    <table class="table">
                        <tr>
                            <td colspan="3">Seller Info - بيانات البائع</td>
                        </tr>
                        <tr>
                            <td>Co. Name</td>
                            <td>
                                @if ($settings['company_name_bill'])
                                    {{ $settings['company_name_bill'] }}
                                @endif
                            </td>
                            <td>اسم المؤسسة / الشركة</td>
                        </tr>
                        <tr>
                            <td>Co. Phone</td>
                            <td>
                                @if ($settings['company_telephone'])
                                    {{ $settings['company_telephone'] }}
                                @endif
                            </td>
                            <td>رقم الشركة</td>
                        </tr>

                        <tr>
                            <td>St. Name</td>
                            <td>
                                @if ($settings['company_address_bill'])
                                    {{ $settings['company_address_bill'] }}
                                @endif
                            </td>
                            <td>اسم الشارع</td>
                        </tr>
                        <tr>
                            <td>City</td>
                            <td>
                                @if ($settings['company_city_bill'])
                                    {{ $settings['company_city_bill'] }},
                                @endif
                            </td>
                            <td>المدينة</td>
                        </tr>
                        <tr>
                            <td>State</td>
                            <td>
                                @if ($settings['company_state_bill'])
                                    {{ $settings['company_state_bill'] }},
                                @endif
                            </td>
                            <td>المقاطعة</td>
                        </tr>
                        <tr>
                            <td>Country</td>
                            <td>
                                @if ($settings['company_country_bill'])
                                    {{ $settings['company_country_bill'] }}
                                @endif
                            </td>
                            <td>الدولة</td>
                        </tr>
                        <tr>
                            <td>P.O.Box</td>
                            <td>
                                @if ($settings['company_zipcode_bill'])
                                    {{ $settings['company_zipcode_bill'] }}
                                @endif
                            </td>
                            <td>الرمز البريدي</td>
                        </tr>
                        <tr>
                            <td>C.R.</td>
                            <td>
                                @if (!empty($settings['registration_number']))
                                    {{ $settings['registration_number'] }}
                                @endif
                            </td>
                            <td>السجل التجاري</td>
                        </tr>
                    </table>
                </div>

            </div>

            <!-- Blade template -->
            <table class="table" style="direction: rtl;margin-top: -50px;">
                <thead>
                    <tr class="titles">

                        {{-- <th>#</th> --}}
                        <th>البند</th>
                        <th style="width: 230px;">البيـــان Description</th>
                        <th>سعر الوحدة</th>
                        <th>الكمية</th>
                        {{-- <th>الخصم</th> --}}
                        <th>الضريبة</th>
                        <th>المجموع</th>

                    </tr>
                </thead>
                <tbody>


                    @if (isset($orders->itemData) && count($orders->itemData) > 0)
                                        <div class="row mt-4" style="margin-bottom:-40px ">
                                            <div class="col-md-12">

                                                <div class="table-responsive mt-2">

                                                    @php
                                                        $totalQuantity = 0;
                                                        $totalRate = 0;
                                                        $totalTaxPrice = 0;
                                                        // $totalDiscount=0;
                                                        $taxesData = [];
                                                        $subtotal = 0;
                                                        $finaltotal = 0;
                                                    @endphp
                                                    @foreach ($orders->itemData as $key => $iteam)
                                                                                <!-- this is the loop that you need to change !-->
                                                                                @php
                                                                                  $description = is_object($iteam) && property_exists($iteam, 'description') ? json_decode($iteam->description, true) : null;
                                                                                @endphp
                                                                                @if (!empty($description) && is_array($description) && $description['info']['type'] == 'custom')
                                                                                                        <!-- This Condition If Item Custom !-->
                                                                                                        @php
                                                                                                            $productName = $description['info']['name'];
                                                                                                            $totalQuantity += $iteam->quantity;
                                                                                                            $totalRate += $iteam->price;
                                                                                                            // $totalDiscount += $iteam->discount;
                                                                                                            $descrp = json_decode($iteam?->description, true);

                                                                                                            foreach ($description as $key_des => $value) {
                                                                                                                if ($key_des == 'التسعيرة النهائية') {
                                                                                                                    foreach ($value as $ke => $val) {
                                                                                                                        if ($ke == 'الاجمالي') {
                                                                                                                            $val_numeric = floatval(str_replace(',', '', $val)); // تحويل النص إلى رقم
                                                                                                                            $total = $val_numeric;
                                                                                                                        }

                                                                                                                        if ($ke == 'الاجمالي بعد الضريبة') {
                                                                                                                            $val_numeric = floatval(str_replace(',', '', $val)); // تحويل النص إلى رقم
                                                                                                                            $total_with_tax = $val_numeric;
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        @endphp
                                                                                                        <tr>
                                                                                                            {{-- <td>{{ $key + 1 }}</td> --}}

                                                                                                            <td>{{ !empty($productName) ? $productName : '' }}</td>
                                                                                                            <td style="">
                                                                                                                <p style="overflow:auto;direction:rtl;text-align:center;">
                                                                                                                    @if (empty($descrp))
                                                                                                                        لا يوجد
                                                                                                                    @else
                                                                                                                                                    @php
                                                                                                                                                        $workorder = json_encode($iteam?->description, true);
                                                                                                                                                    @endphp


                                                                                                                                                    {{-- <a href="#" class="btn btn-primary btn-sm" id="descriptionview">{{
                                                                                                                                                        __('view workorder') }}<i class="ti ti-link"></i></a> --}}
                                                                                                                                                    {{-- <textarea class="form-control pro_description d-none" placeholder="الوصف"
                                                                                                                                                        name="items[][description]">{{ $workorder }}</textarea> --}}
                                                                                                                                                    <small style="display: inline;">


                                                                                                                                                        @foreach ($descrp as $key_dd => $value)
                                                                                                                                                            {{-- @if ($key_dd == 'attachment')
                                                                                                                                                                <a target="__blank" class="btn btn-primary btn-sm"
                                                                                                                                                                    href="{{ url('storage/') . $value }}">{{ __('view attachment') }}<i
                                                                                                                                                                        class="ti ti-eye"></i></a>
                                                                                                                                                            @endif

                                                                                                                                                            @if ($key_dd == 'design_note')
                                                                                                                                                                <textarea style="width: 100%" readonly>{{ $value }}</textarea>
                                                                                                                                                            @endif --}}
                                                                                                                                                            @if ($key_dd == 'المقاسات')
                                                                                                                                                                المقاسات (
                                                                                                                                                                @foreach ($value as $val)
                                                                                                                                                                    {{ $val }}{{ !$loop->last ? 'x' : '' }}
                                                                                                                                                                @endforeach
                                                                                                                                                                )
                                                                                                                                                            @endif

                                                                                                                                                            <!-- If Key Has لاضافات !-->
                                                                                                                                                            @if ($key_dd == 'الاضافات')
                                                                                                                                                                الاضافات =
                                                                                                                                                                @foreach ($value as $val)
                                                                                                                                                                    (@foreach ($val as $k => $v)
                                                                                                                                                                        @if ($k == 'اسم المادة')
                                                                                                                                                                            {{ $v }}
                                                                                                                                                                        @endif
                                                                                                                                                                    @endforeach)
                                                                                                                                                                @endforeach
                                                                                                                                                            @endif

                                                                                                                                                            @if ($key_dd == 'الاختيارات')
                                                                                                                                                                الاختيارات =
                                                                                                                                                                @foreach ($value as $val)
                                                                                                                                                                    (@foreach ($val as $k => $v)
                                                                                                                                                                        @if ($k == 'اسم الاختيار')
                                                                                                                                                                            {{ $v }} =
                                                                                                                                                                        @endif
                                                                                                                                                                        @if ($k == 'الاختيار')
                                                                                                                                                                            {{ $v }}
                                                                                                                                                                        @endif
                                                                                                                                                                    @endforeach)
                                                                                                                                                                @endforeach
                                                                                                                                                            @endif
                                                                                                                                                        @endforeach
                                                                                                                    @endif
                                                                                                                    </small>
                                                                                                                </p>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                @foreach ($descrp as $key_dd => $value)
                                                                                                                @if ($key_dd == 'التسعيرة النهائية')
                                                                                                                    @foreach ($value as $ke => $val)
                                                                                                                        @if ($ke == 'سعر الافرادي')
                                                                                                                            {{\Auth::user()->priceFormat($val,3) }}
                                                                                                                        @endif
                                                                                                                    @endforeach
                                                                                                                @endif
                                                                                                                @endforeach
                                                                                                            </td>
                                                                                                            <td>{{ $iteam->quantity }}</td>
                                                                                                            {{-- <td>{{\Auth::user()->priceFormat($iteam->discount)}}</td> --}}
                                                                                                            <td>{{ __('VAT COM (15%)',[],'ar') }}</td>
                                                                                                            {{-- <td colspan="2">
                                                                                                                <p style="width: 220px; overflow:auto;">{{!empty($descrp)?$descrp:'-'}}</p>
                                                                                                            </td> --}}

                                                                                                            <td class="text-end">
                                                                                                                {{ \Auth::user()->priceFormat($total_with_tax) }}
                                                                                                            </td>
                                                                                                        </tr>
                                                                                     @php
                                                                                        // $subtotal += $totalTaxPrice;
                                                                                        $finaltotal += $total_with_tax;
                                                                                    @endphp
                                                                                @else
                                                                                                        {{-- If Product Not Custome --}}
                                                                                                        @php
                                                                                                            $productName = $iteam->name;
                                                                                                            $totalQuantity += $iteam->quantity;
                                                                                                            $totalRate += $iteam->price;
                                                                                                        @endphp
                                                                                                        <tr>
                                                                                                            {{-- <td>{{ $key + 1 }}</td> --}}

                                                                                                            <td>{{ !empty($productName) ? $iteam->name : '' }}</td>
                                                                                                            <td>
                                                                                                                <p style=" overflow:auto;">
                                                                                                                    {{ !empty($iteam?->description) && $iteam?->description != 'null' ? $iteam?->description : '____________' }}
                                                                                                                </p>
                                                                                                            </td>

                                                                                                            <td>{{ \Auth::user()->priceFormat($iteam->price) }}</td>

                                                                                                            {{-- <td>{{\Auth::user()->priceFormat($iteam->discount)}}</td> --}}
                                                                                                            <td>
                                                                                                                @if (!empty($iteam->tax))
                                                                                                                                            <table>
                                                                                                                                                @php
                                                                                                                                                    $itemTaxes = [];
                                                                                                                                                    $getTaxData = Utility::getTaxData();

                                                                                                                                                    if (!empty($iteam->tax)) {
                                                                                                                                                        foreach (explode(',', $iteam->tax) as $tax) {
                                                                                                                                                            $taxPrice = \Utility::taxRate(
                                                                                                                                                                $getTaxData[$tax]['rate'],
                                                                                                                                                                $iteam->price,
                                                                                                                                                                $iteam->quantity,
                                                                                                                                                            );
                                                                                                                                                            $totalTaxPrice += $taxPrice;
                                                                                                                                                            $itemTax['name'] = $getTaxData[$tax]['name'];
                                                                                                                                                            $itemTax['rate'] =
                                                                                                                                                                $getTaxData[$tax]['rate'] . '';
                                                                                                                                                            $itemTax['price'] = \Auth::user()->priceFormat(
                                                                                                                                                                $taxPrice,
                                                                                                                                                            );

                                                                                                                                                            $itemTaxes[] = $itemTax;
                                                                                                                                                            if (
                                                                                                                                                                array_key_exists(
                                                                                                                                                                    $getTaxData[$tax]['name'],
                                                                                                                                                                    $taxesData,
                                                                                                                                                                )
                                                                                                                                                            ) {
                                                                                                                                                                $taxesData[$getTaxData[$tax]['name']] =
                                                                                                                                                                    $taxesData[$getTaxData[$tax]['name']] +
                                                                                                                                                                    $taxPrice;
                                                                                                                                                            } else {
                                                                                                                                                                $taxesData[
                                                                                                                                                                    $getTaxData[$tax]['name']
                                                                                                                                                                ] = $taxPrice;
                                                                                                                                                            }
                                                                                                                                                        }
                                                                                                                                                        $iteam->itemTax = $itemTaxes;
                                                                                                                                                    } else {
                                                                                                                                                        $iteam->itemTax = [];
                                                                                                                                                    }
                                                                                                                                                @endphp
                                                                                                                                                @foreach ($iteam->itemTax as $tax)
                                                                                                                                                    {{ $tax['name'] . ' (' . $tax['rate'] . '%)' }}
                                                                                                                                                @endforeach
                                                                                                                                            </table>
                                                                                                                @else
                                                                                                                    -
                                                                                                                @endif

                                                                                                            </td>


                                                                                                            <td class="text-end">
                                                                                                                {{ \Auth::user()->priceFormat($iteam->price * $iteam->quantity + $totalTaxPrice) }}
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        @php
                                                                                                            // $subtotal += $totalTaxPrice;
                                                                                                            $finaltotal += $iteam->price * $iteam->quantity + $totalTaxPrice;
                                                                                                        @endphp
                                                                                @endif
                                                    @endforeach
                    @endif
                </tbody>

                <tfoot>
                    <tr class="item">
                        <td colspan="9"><span class="left" style="float: left; padding-left:5px;">Net Amount</span>
                            <span class="central-text"> {{ Utility::priceFormat($settings, $total_product) }} </span>
                            <span class="right" style=" padding-right:5px">الصافي</span>
                        </td>
                    </tr>

                    {{-- <tr class="item">
                        <td colspan="9"><span class="left" style="float: left; padding-left:5px;">Discount</span>
                            <span class="central-text">{{ Utility::priceFormat($settings, $total_discount) }} </span>
                            <span class="right" style=" padding-right:5px">الخصم </span>
                        </td>
                    </tr> --}}
                    <tr class="item">
                        <td colspan="9"><span class="left" style="float: left; padding-left:5px;">Vat Total</span>
                            @php
                            $vat_total =  ($finaltotal - $total_product);
                            @endphp
                            <span class="central-text">{{ Utility::priceFormat($settings, $vat_total) }} </span> <span class="right"
                            style=" padding-right:5px">إجمالي ضريبة القيمة المضافة </span>
                        </td>
                    </tr>

                    <tr class="item">
                        <td colspan="9"><span class="left" style="float: left; padding-left:5px;">Total</span>

                            {{ \Auth::user()->priceFormat($finaltotal) }}
                            <span class="central-text"></span> <span class="right" style=" padding-right:5px">الاجمالي
                            </span>
                        </td>
                    </tr>

                </tfoot>
            </table>
            <div class="termsandcond" style="direction: rtl">
                @if (isset($settings['purchase_footer']))
                    {!! $settings['purchase_footer'] !!}
                @endif
            </div>

            <div class="printbutton">
                <a onclick="history.back()" type="button" class="btn btn-secondary"
                    style="position: fixed; top: 20px; left: 20px;width: 150px;">{{ __('Back') }}</a>
                <button onclick="window.print()" type="button" class="btn btn-danger"
                    style="position: fixed; top: 60px; left: 20px;width: 150px;">{{ __('Print') }}</button>
            </div>


        </div>

</body>

</html>
