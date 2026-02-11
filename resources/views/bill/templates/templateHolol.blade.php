@php
    $setting = \App\Models\Utility::settings();
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_logo_dark = $setting['company_logo_dark'] ?? '';
    $company_logo_light = $setting['company_logo_light'] ?? '';
    $company_logo_small = $setting['company_small_logo'] ?? '';
    $logo_url = url($logo) . '/' . $company_logo_dark;
    $orders = $bill;
    // dd($customer);
    // dd($settings);
    // dd($bill);

    if (isset($bill->itemData) && count($bill->itemData) > 0) {
    $total_product = 0;
    $total_discount = 0;
    $total_vat = 0;
    $total_one = 0;
    foreach ($bill->itemData as $key => $item){
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


    <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link rel="stylesheet" href="{{ asset('assets/invoice/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/invoice/style.css') }}">
  <title>{{__('Bill')}}</title>
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
    <div class="row">
      <div class="col-2">
          @if (!empty($settings['social_link_bill']))
              {!! DNS2D::getBarcodeHTML($settings['social_link_bill'], 'QRCODE', 2, 2) !!}
          @else
                              {!! DNS2D::getBarcodeHTML(
                  route('bill.link.copy', \Crypt::encrypt($bill->bill_id)),
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
              <p class="title-of-invoce">{{__("Bill", [], 'ar')}}  <br> {{ __('Bill',[],'en')}}</p>
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
                  @if (isset($bill))
                      <td>{{ date('Y-m-d', strtotime($bill->created_at)) }}</td>
                  @else
                      <td><?php    echo date('Y-m-d'); ?></td>
                  @endif
                  <td>تاريخ الاصدار</td>
              </tr>
              @if (isset($bill->order_id))
                  <tr>
                      <td>REF. No.:</td>
                      <td>{{ App\Models\Utility::ordersNumberFormat($bill->order_id) }}</td>
                      <td>الرقم المرجعي</td>
                  </tr>
              @endif
              <tr>
                  <td>User</td>
                  <td>{{ $bill->user_id != null ? $bill->user->name : '-' }}</td>
                  <td>المستخدم</td>
              </tr>
              <tr>
                  <td>bracnh</td>
                  <td>{{ App\Models\Employee::where('user_id', $bill->user_id)->first()->branch->name ?? '-' }}
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
          <div class="invoice-detailes">
          <table class="table" style="direction: rtl;margin-top: -17px;">
          <thead>
          <tr>
              <th>البند</th>
              <th style="width: 230px;">البيـــان Description</th>
              <th>سعر الوحدة</th>
              <th>الكمية</th>
              <th>الخصم</th>
              <th>الضريبة</th>
              <th>المجموع</th>


            </tr>
          </thead>
          <tbody>
            @php
            $total_product = 0;
            $total_discount = 0 ;
            $total_vat = 0 ;
            $total_one = 0 ;
          @endphp


            @if(isset($bill->itemData) && count($bill->itemData) > 0)

            @foreach($bill->itemData as $key => $item)
            <tr class="item">
              <td><span id="ProuductName">{{$item->name}}<span></span></span></td>


              @if(!empty($item->description))
              @php
                $description_array = json_decode($item->description, true);
                // dd($description_array); // Uncomment this for debugging if needed
              @endphp
              <td>
                @foreach($description_array as $key => $value)
                  @if($key == 'المقاسات' || $key == 'الاضافات' || $key == 'الاختيارات')
                    {!! $key . '<br>' !!}
                    @if(is_array($value))
                      @foreach($value as $v_product)
                        @if(is_array($v_product))
                          @foreach($v_product as $data_key => $data_value)
                            @if(is_string($data_value) || is_numeric($data_value))
                            @if($data_key == 'اسم المادة' || $data_key == 'اسم المادة' ||  $data_key == "اسم الاختيار"  || $data_key ==  "الاختيار" )
                              {!! $data_key . ': ' . $data_value . '<br>' !!}
                              @endif
                            @endif
                          @endforeach
                          <hr>
                        @else
                          {!! $v_product . '<br>' !!}
                        @endif
                      @endforeach
                    @endif
                  @endif
                @endforeach
              </td>
            @else
              <td><span id="ProductDescript">--</span></td>
            @endif




                <td><span id="ProductPrice">{{Utility::priceFormat($settings,$item->price)}}</span></td>
                @php
                $unitName = App\Models\ProductServiceUnit::find($item->unit);
                @endphp
                <td><span id="ProductQty">{{$item->quantity}} {{ ($unitName != null) ?  '('. $unitName->name .')' : ''}}</span></td>
                <td><span id="ProductDiscount">{{($item->discount!=0)?Utility::priceFormat($settings,$item->discount):'-'}}</span></td>
                @php
                $itemtax = 0;
                @endphp
                    @if(!empty($item->itemTax))
                    <td><span id="ProductTax">
                      @foreach($item->itemTax as $taxes)
                          @php
                              $itemtax += $taxes['tax_price'];
                          @endphp
                          {{$taxes['name']}} ({{$taxes['rate']}}) {{$taxes['price']}}
                      @endforeach
                    </span></td>
                    @else
                          <td><span id="ProductTax">--</span></td>
                    @endif

                    @php

                    $total_item = ($item->price * $item->quantity) + $itemtax - $item->discount;
                    $total_discount += ($item->discount * $item->quantity);
                    $total_vat += $itemtax;
                    $total_one += ($item->price * $item->quantity);
                    $total_product += $total_item;
                    @endphp
                    <td><span id="ProductCost">{{Utility::priceFormat($settings,$total_item) }}</span></td>




            </tr>
            @endforeach
            @endif




          </tbody>


          <tr class="item">
            <td colspan="9"><span class="left" style="float: left; padding-left:5px;">Total</span>{{Utility::priceFormat($settings,$total_one)}}  <span class="central-text"></span> <span class="right" style=" padding-right:5px">الاجمالي </span></td>
          </tr>
          <tr class="item">
            <td colspan="9"><span class="left" style="float: left; padding-left:5px;">Discount</span> <span class="central-text">{{Utility::priceFormat($settings,$total_discount)}} </span> <span class="right" style=" padding-right:5px">الخصم </span></td>
          </tr>
          <tr class="item">
            <td colspan="9"><span class="left" style="float: left; padding-left:5px;">Vat Total</span> <span class="central-text">{{Utility::priceFormat($settings,$total_vat)}} </span> <span class="right" style=" padding-right:5px">إجمالي ضريبة القيمة المضافة </span></td>
          </tr>
          <tr class="item">
            <td colspan="9"><span class="left" style="float: left; padding-left:5px;">Net Amount</span> <span class="central-text"> {{Utility::priceFormat($settings,$total_product)}} </span> <span class="right" style=" padding-right:5px">الصافي</span></td>
          </tr>
          </table>
          <div class="row">
            @if(isset($settings['proposal_footer'])){!! $settings['proposal_footer'] !!}@endif
          </div>
        </div>

      </div>


    </div>

  </div>

  <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
  <script>

          var filename = 'bill';
          function saveAsPDF() {
          var element = document.getElementById('invoice_body');
          var opt = {
              margin: 0.3,
              filename: filename,
              image: {
                  type: 'pdf',
                  quality: 1
              },
              html2canvas: {
                  scale: 2,
                  dpi: 72,
                  letterRendering: true
              },
              jsPDF: {
                  unit: 'in',
                  format: 'A4'
              }
          };
          html2pdf().set(opt).from(element).save();
      }
    //  saveAsPDF();


      document.addEventListener("keydown", function(event) {
    if (event.ctrlKey && event.key === "p") {
        event.preventDefault(); // لمنع فتح نافذة الطباعة الافتراضية
        saveAsPDF();
    }
});
  </script>

<div class="printbutton">
    <a onclick="history.back()" type="button" class="btn btn-secondary"
        style="position: fixed; top: 20px; left: 20px;width: 150px;">{{ __('Back') }}</a>
    <button onclick="window.print()" type="button" class="btn btn-danger"
        style="position: fixed; top: 60px; left: 20px;width: 150px;">{{ __('Print') }}</button>
</div>
</body>

</html>
