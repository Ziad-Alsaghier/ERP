@include('report.includes.header')
@section('content')
<div class="row">
    <div class="col-6">
        <span class="left">Notes:</span><span class="right">الملاحظات</span>
        <textarea name="" style="overflow-y: hidden;" id="textarea_note"></textarea>
    </div>
    <div class="col-6 information" style="height: 77px">
        <table class="table table-borderd">
            <tr>
                <td>Est Date:</td>
                @if (isset($invoice))
                <td>{{ date('Y-m-d', strtotime($invoice->created_at)) }}</td>
                @else
                <td>{{ date('Y-m-d') }}</td>
                @endif
                <td>تاريخ الاصدار</td>
            </tr>
            @if (isset($invoice->order_id))
            <tr>
                <td>REF. No.:</td>
                <td>{{ $utility->ordersNumberFormat($invoice->order_id) }}</td>
                <td>الرقم المرجعي</td>
            </tr>
            @endif
            <tr>
                <td>User</td>
                <td>{{ $invoice->user_id != null ? $invoice->user->name : '-' }}</td>
                <td>المستخدم</td>
            </tr>
            <tr>
                <td>bracnh</td>
                <td>{{ App\Models\Employee::where('user_id', $invoice->user_id)->first()->branch->name ?? '-' }}</td>
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
                <td>{{ !empty($customer->name) ? $customer->name : '' }}</td>
                <td>اسم العميل</td>
            </tr>
            <tr>
                <td>Mobile No.</td>
                <td>{{ !empty($customer->shipping_phone) ? $customer->shipping_phone : '' }}</td>
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
                <td>{{ !empty($customer->billing_city) ? $customer->billing_city : '' . ', ' }}</td>
                <td>المدينة</td>
            </tr>
            <tr>
                <td>Country</td>
                <td>{{ !empty($customer->billing_country) ? $customer->billing_country : '' . ', ' }}</td>
                <td>الدولة</td>
            </tr>
            <tr>
                <td>P.O.Box</td>
                <td>{{ !empty($customer->billing_zip) ? $customer->billing_zip : '' . ', ' }}</td>
                <td>الرمز البريدي</td>
            </tr>
            <tr>
                <td>C.R.</td>
                <td>{{ !empty($customer->tax_number) ? $customer->tax_number : '' }}</td>
                <td>السجل التجاري</td>
            </tr>
            <tr>
                <td>Vat No.</td>
                <td>{{ !empty($customer->vat_number) ? $customer->vat_number : '' }}</td>
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
<div class="invoice-detailes">
    <table class="table" style="direction: rtl;margin-top: -50px;">
        <thead>
            <tr class="titles">
                <th>البند</th>
                <th style="width: 230px;">البيـــان Description</th>
                <th>سعر الوحدة</th>
                <th>الكمية</th>
                <th>الضريبة</th>
                <th>المجموع</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($orders->itemData) && count($orders->itemData) > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="table-responsive mt-2">

                        @foreach ($orders->itemData as $key => $iteam)
                        @php
                        $description = json_decode($iteam->description, true);
                        @endphp
                        @if (is_array($description) && $description['info']['type'] == 'custom')
                        @php
                        $productName = $description['info']['name'];
                        $totalQuantity += $iteam->quantity;
                        $totalRate += $iteam->price;
                        $descrp = json_decode($iteam->description, true);
                        foreach ($description as $key_des => $value) {
                        if ($key_des == 'التسعيرة النهائية') {
                        foreach ($value as $ke => $val) {
                        if ($ke == 'الاجمالي') {
                        $val_numeric = floatval(str_replace(',', '', $val));
                        $total = $val_numeric;
                        }
                        if ($ke == 'الاجمالي بعد الضريبة') {
                        $val_numeric = floatval(str_replace(',', '', $val));
                        $total_with_tax = $val_numeric;
                        }
                        }
                        }
                        }
                        @endphp
                        <tr>
                            <td>{{ !empty($productName) ? $productName : '' }}</td>
                            <td style="">
                                <p style="overflow:auto;direction:rtl;text-align:center;">
                                    @if (empty($descrp))
                                    لا يوجد
                                    @else
                                    <small style="display: inline;">
                                        @foreach ($descrp as $key_dd => $value)
                                        @if ($key_dd == 'المقاسات')
                                        المقاسات (
                                        @foreach ($value as $val)
                                        {{ $val }}{{ !$loop->last ? 'x' : '' }}
                                        @endforeach
                                        )
                                        @endif
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
                                    </small>
                                    @endif
                                </p>
                            </td>
                            <td>
                                @foreach ($descrp as $key_dd => $value)
                                @if ($key_dd == 'التسعيرة النهائية')
                                @foreach ($value as $ke => $val)
                                @if ($ke == 'سعر الافرادي')
                                {{ $utility->priceFormat($settings_data, $val, 3) }}
                                @endif
                                @endforeach
                                @endif
                                @endforeach
                            </td>
                            <td>{{ $iteam->quantity }}</td>
                            <td>{{ __('VAT COM (15%)',[],'ar') }}</td>
                            <td class="text-end">
                                {{ $utility->priceFormat($settings_data, $total_with_tax) }}
                            </td>
                        </tr>
                        @php
                        $finaltotal += $total_with_tax;
                        @endphp
                        @else
                        @php
                        $productName = $iteam->name;
                        $totalQuantity += $iteam->quantity;
                        $totalRate += $iteam->price;
                        @endphp
                        <tr>
                            <td>{{ !empty($productName) ? $iteam->name : '' }}</td>
                            <td>
                                <p style="overflow:auto;">
                                    {{ !empty($iteam->description) && $iteam->description != 'null' ?
                                    $iteam->description : '____________' }}
                                </p>
                            </td>
                            <td>{{ $utility->priceFormat($settings_data, $iteam->price) }}</td>
                            <td>{{ $iteam->quantity . ' (' . $iteam->unit . ')' }}</td>
                            <td>
                                @if (!empty($iteam->tax))
                                <table>
                                    @php
                                    $itemTaxes = [];
                                    $getTaxData = $utility->getTaxData();
                                    if (!empty($iteam->tax)) {
                                    foreach (explode(',', $iteam->tax) as $tax) {
                                    $taxPrice = $utility->taxRate(
                                    $getTaxData[$tax]['rate'],
                                    $iteam->price,
                                    $iteam->quantity,
                                    );
                                    $totalTaxPrice += $taxPrice;
                                    $itemTax['name'] = $getTaxData[$tax]['name'];
                                    $itemTax['rate'] = $getTaxData[$tax]['rate'] . '%';
                                    $itemTax['price'] = $utility->priceFormat($settings_data, $taxPrice);
                                    $itemTaxes[] = $itemTax;
                                    if (array_key_exists($getTaxData[$tax]['name'], $taxesData)) {
                                    $taxesData[$getTaxData[$tax]['name']] = $taxesData[$getTaxData[$tax]['name']] +
                                    $taxPrice;
                                    } else {
                                    $taxesData[$getTaxData[$tax]['name']] = $taxPrice;
                                    }
                                    }
                                    $iteam->itemTax = $itemTaxes;
                                    } else {
                                    $iteam->itemTax = [];
                                    }
                                    @endphp
                                    @foreach ($iteam->itemTax as $tax)
                                    {{ $tax['name'] . ' (' . $tax['rate'] . ')' }}
                                    @endforeach
                                </table>
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-end">
                                {{ $utility->priceFormat($settings_data, $iteam->price * $iteam->quantity +
                                $totalTaxPrice) }}
                            </td>
                        </tr>
                        @php
                        $finaltotal += $iteam->price * $iteam->quantity + $totalTaxPrice;
                        @endphp
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </tbody>
        <tfoot>
            <tr class="item">
                <td colspan="9">
                    <span class="left" style="float: left; padding-left:5px;">Net Amount</span>
                    <span class="central-text"> {{ $utility->priceFormat($settings, $total_product) }} </span>
                    <span class="right" style=" padding-right:5px">الصافي</span>
                </td>
            </tr>
            <tr class="item">
                <td colspan="9">
                    <span class="left" style="float: left; padding-left:5px;">Vat Total</span>
                    @php
                    $vat_total = ($finaltotal - $total_product);
                    @endphp
                    <span class="central-text">{{ $utility->priceFormat($settings, $vat_total) }} </span>
                    <span class="right" style=" padding-right:5px">إجمالي ضريبة القيمة المضافة </span>
                </td>
            </tr>
            <tr class="item">
                <td colspan="9">
                    <span class="left" style="float: left; padding-left:5px;">Total</span>
                    {{ $utility->priceFormat($settings_data, $finaltotal) }}
                    <span class="central-text"></span>
                    <span class="right" style=" padding-right:5px">الاجمالي</span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@show
@include('report.includes.footer')
