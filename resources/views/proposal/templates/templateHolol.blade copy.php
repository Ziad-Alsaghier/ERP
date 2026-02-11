@php
// dd($customer);
    $settings_data = \App\Models\Utility::settingsById($proposal->created_by);
@endphp



    <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <title>{{__('Quotation')}}</title>
  <style>


    @import url(https://fonts.googleapis.com/css?family=Lato:400,300,300italic,400italic,700,700italic);
    /** GLOBAL **/

    html, body {
    height: 100%;
    background:{{$color}};
    width: 100%;
    margin: 0;
    padding: 0;
    left: 0;
    top: 0;
    font-size: 100%;
    }
    * {
    font-family: "Lato", "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
    color: #333447;
    line-height: 1.5;
    }
    /* TYPOGRAPHY */

    h1 {
    font-size: 2.5rem;
    }
    h2 {
    font-size: 2rem;
    }
    h3 {
    font-size: 1.375rem;
    }
    h4 {
    font-size: 1.125rem;
    }
    h5 {
    font-size: 1rem;
    }
    h6 {
    font-size: 0.875rem;
    }
    p {
    font-size: 1.125rem;
    font-weight: 200;
    line-height: 1.8;
    }
    .font-light {
    font-weight: 300;
    }
    .font-regular {
    font-weight: 400;
    }
    .font-heavy {
    font-weight: 700;
    }
    /* POSITIONING */

    .left {
    text-align: left;
    }
    .right {
    float: right;
    text-align: right;
    }
    .center {
    text-align: center;
    margin-left: auto;
    margin-right: auto;
    }
    .justify {
    text-align: justify;
    }
    /** standard padding**/

    .no-padding {
    padding: 0px;
    }
    .standard-padding {
    padding: 20px;
    }
    .standard-padding-right {
    padding-right: 20px;
    }
    .standard-padding-left {
    padding-left: 20px;
    }
    .standard-padding-right {
    padding-left: 20px;
    }
    .standard-padding-top {
    padding-top: 20px;
    }
    .standard-padding-bottom {
    padding-bottom: 20px;
    }
    .container {
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    }
    .row {
    position: relative;
    width: 100%;
    }
    .row [class^="col"] {
    float: left;
    margin: 0.5rem 2%;
    min-height: 0.125rem;
    }
    .row::after {
    content: "";
    display: table;
    clear: both;
    }
    .hidden-sm {
    display: none;
    }
    .invoice-box {
    background: #ffffff;
    max-width: 700px;
    margin: 60px auto;
    padding: 30px;
    border: 1px solid #002336;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
    font-size: 16px;
    line-height: 24px;
    color: #002336;
    min-height: 1070px;
    }
    .title {
    margin-bottom: 0px;
    padding-bottom: 0px;
    margin-left: 10px;
    margin-right: 10px;
    font-weight: bold;
    border-bottom: 1px solid #8B8B8B;
    margin-bottom: 4px;
    }
    .infoblock {
    margin-left: 10px;
    margin-right: 10px;
    margin-top: 0px;
    padding-top: 0px;
    }
    .titles {
    padding-top: 4px;
    margin-top: 20px;
    background: #DCDCDC;
    font-weight: bold;
    }

    /** RTL **/

    .rtl {
    direction: rtl;
    font-family: "Lato", Tahoma, "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
    }
    .rtl table {
    text-align: right;
    }
    .rtl table tr td:nth-child(2) {
    text-align: left;
    }
    .eqWrap {
    display: flex;
    }
    .eq {
    padding: 10px;
    }
    .item:nth-of-type(odd) {
    background: #F9F9F9;
    }
    .item:nth-of-type(even) {
    background: #fff;
    }
    .equalHW {
    flex: 1;
    }
    .equalHM {
    width: 32%;
    }
    .equalHMR {
    width: 32%;
    margin-bottom: 2%;
    }
    table.table {
    width: 100%;
    margin-top: 5px;
    border-collapse: collapse;
    }
    .table th, .table td {
    text-align: left;
    padding: 0.25em;
    }
    .table tr {
    border-bottom: 1px solid #DDD;
    }
    .table , .table tr , .table td , .table th{
        /* border: 1px black solid; */
        text-align: center;
    }
    button:hover {
    box-shadow: 0 0 4px rgba(3, 3, 3, 0.8);
    opacity: 0.9;
    }
    .central-col{
        position: relative;
    }
    .central-text{
        position: absolute;
        left: 0;
        right: 0;
        text-align: center;
    }
    .top-header{
      height: auto;
    }
    .image-of-header img{
    height: 70px;
    float: right;
    }
    .title-of-invoce{
    text-align: center;
    margin:0px;
    font-weight: 900;
    font-size: 16px;
    width: 150px;
    border: 3px solid;
    border-radius: 9px;
    line-height: 20px;
    }

    .title-of-invoce_con{
    padding-left: 39%;
    padding-right: 50%;
    }
    .border{
    border: 1px solid black;
    padding: 6px;
    margin: 6px;
    }
    .border-no-marg{
    border: 1px solid black;
    padding: 0px;
    padding-bottom: 4px;
    padding-top: 4px;
    margin: 6px;
    }
    .table-div{
    margin-top: 6px;
    border-top: 1px solid black;
    height: max-content;

    }
    .border-no-mg{
    border-bottom: 1px solid black;
    border-left: 1px solid black;
    border-right: 1px solid black;
    padding-left: 10px;
    padding-right: 10px;
    }
    .border-no-mg-no-br{
    padding-left: 10px;
    padding-right: 10px;
    font-size: 15px;
    }
    .border-no-mg-no-br span{
    line-height: 15px;
    font-weight: 800;
    }
    .note-title{
    padding-top: 10px;
    padding-bottom: 10px;
    direction: rtl;
    font-size: 25px;
    font-weight: 800;
    }
    .note{
    direction: rtl;
    font-size: 18px;
    font-weight: 800;
    }
    .copywrite-text{
    position: fixed;
    bottom: 25;
    left: 25;
    background-color: rgb(255, 255, 255);
    padding: 10px;
    border-radius: 10px;

    }
    .back-forword button{
    position: fixed;
    bottom: 80;
    left: 25;
    background-color: rgb(138, 221, 99);
    padding: 10px;
    border-radius: 10px;
    font-weight: 900;
    font-size: 25px;
    cursor: pointer;
    }
    .back-forword button:hover{

    background-color: rgb(44, 133, 3);

    }
    .print-text{
    position: fixed;
    bottom: 25;
    right: 25;
    background-color: white;
    padding: 10px;
    border-radius: 10px;
    font-size: 25px;
    }
    .icon{
    height: 15px;
    }
    .d-flex{
    display: flex;
    align-items: center;
    }
    .d-flex img{
    padding-right: 4px;
    }
    .d-flex span{
    font-size: 11.5px;
    }
    .top-header .eq span{
    font-weight: 800;
    }
    span , p , td,th,div{
    font-size: 12px;
    }
    .print-text-hide{
    display: none;
    }
    #User_data{
    font-size: 9px;
    text-align:center;
    line-height: normal;
    }
    #textarea_note{
    max-width: 100%;
    min-width: 100%;
    text-align: center;
    font-size: 9px;
    }

    @media print {
      body{
        width:100%!important;
        padding: 0px;
        margin:0px;
      }
      .invoice-box{
        box-shadow :none;
        border :none;
        padding: 0;
        margin: 0;
      }
      .div-to-hide{
        display: none;
      }
      #User_data{
        border: none;
      }
      #textarea_note{
        border: none;
      }
    }
  </style>

</head>
<body>

  <div class="invoice-box">
    <div class="container" id="invoice_body">

      <div class="row">
        <div class=" eqWrap top-header">
          @if (!empty($settings['social_link_bill']))
            <div class="eq">
              {!! DNS2D::getBarcodeHTML($settings['social_link_bill'], "QRCODE",2,2) !!}
            </div>
          @endif
          <div class="eq" style="width:100%; max-width:115px;">
            @if($settings['header_bill']){!! $settings['header_bill'] !!}@endif
        </div>
          <div class="equalHW eq image-of-header">
            <img src="{{url('/')}}/{{$img}}" >
          </div>
        </div>
        <div class="title-of-invoce_con">
          <p class="title-of-invoce">{{__('Quotation',[],'ar')}} <br> {{__('Quotation',[],'en')}} </p>
        </div>
        <div class="row">
          <div class="row">
            <div class="equalHWrap eqWrap nomargin-nopadding to-block">
              <div class=" center logo-block" style="width:100%; max-width:115px;">
                {!! DNS2D::getBarcodeHTML(route('proposal.link.copy',\Crypt::encrypt($proposal->proposal_id)), "QRCODE",2,2) !!}
              </div>
              <div class="equalHW central-col border">
                <span class="left">Notes:</span><span class="right">الملاحظات</span>
                <br>
                <span class="central-text" style="overflow-wrap: break-word;">{{$proposal->proposal_details}}</span>
              </div>
              <div class="equalHW central-col table-div">
              <div class="border-no-mg"><span class="left">Est Date:</span> <span class="central-text">{{$proposal->issue_date}}</span> <span class="right">تاريخ الاصدار</span></div>
                <div class="border-no-mg"><span class="left">Est. No.:</span> <span class="central-text"> {{$proposal->proposal_id}} </span> <span class="right">رقم عرض السعر</span></div>
                <div class="border-no-mg"><span class="left">Supply Date:</span> <span class="central-text">{{$proposal->due_date}} </span> <span class="right">تاريخ التوريد</span></div>
              </div>
            </div>
          </div>
          <div class="row">
          <div class="eqWrap" >
              <div class="eq infoblock from-block central-col border-no-marg" style="width: 100%;">
                <div class="border-no-mg-no-br"><span class="left">Customer Info</span> <span class="central-text">  </span> <span class="right">بيانات العميل</span></div> <hr>
                <div class="border-no-mg-no-br"><span class="left">Customer Name</span> <span class="central-text">  {{!empty($customer->name)?$customer->name:''}} </span> <span class="right">اسم العميل</span></div>
                <div class="border-no-mg-no-br"><span class="left">Mobile No.</span> <span class="central-text"> {{!empty($customer->shipping_phone)?$customer->shipping_phone:''}} </span> <span class="right">رقم الجوال</span></div>
                <div class="border-no-mg-no-br"><span class="left">Address</span> <span class="central-text">  {{!empty($customer->billing_name)?$customer->billing_name:''}} - {{!empty($customer->billing_address)?$customer->billing_address:''}} </span> <span class="right">العنوان</span></div>
                <div class="border-no-mg-no-br"><span class="left">City</span> <span class="central-text"> {{!empty($customer->billing_city)?$customer->billing_city:'' .', '}} </span> <span class="right">المدينة</span></div>
                <div class="border-no-mg-no-br"><span class="left">Country</span> <span class="central-text">  {{!empty($customer->billing_state)?$customer->billing_state:''.', '}} - {{!empty($customer->billing_country)?$customer->billing_country:''}} </span> <span class="right">الدولة</span></div>
                <div class="border-no-mg-no-br"><span class="left">P.O.Box</span> <span class="central-text"> {{!empty($customer->billing_phone)?$customer->billing_phone:''}}  </span> <span class="right">الرمز البريدي</span></div>
                <div class="border-no-mg-no-br"><span class="left">C.R.</span> <span class="central-text"> {{!empty($customer->tax_number)?$customer->tax_number:''}} </span> <span class="right">السجل التجاري</span></div>
                <div class="border-no-mg-no-br"><span class="left">Vat No.</span> <span class="central-text"> {{!empty($customer->vat_number)?$customer->vat_number:''}} </span> <span class="right">الرقم الضريبي</span></div>
              </div>
              <div class="eq infoblock from-block central-col border-no-marg" style="width: 100%;">
                <div class="border-no-mg-no-br"><span class="left">Seller Info</span> <span class="central-text"></span> <span class="right">بيانات البائع</span></div><hr>
                <div class="border-no-mg-no-br"><span class="left">Co. Name</span> <span class="central-text"> @if($settings['company_name']){{$settings['company_name']}}@endif</span> <span class="right">اسم المؤسسة / الشركة</span></div>
                <div class="border-no-mg-no-br"><span class="left">Mobile No.</span> <span class="central-text">  @if($settings['company_telephone']){{$settings['company_telephone']}}@endif </span> <span class="right">رقم الجوال</span></div>
                <div class="border-no-mg-no-br"><span class="left">Address</span> <span class="central-text"> @if($settings['company_state']){{$settings['company_state']}}@endif - @if($settings['company_address']){{$settings['company_address']}}@endif </span> <span class="right">العنوان</span></div>
                <div class="border-no-mg-no-br"><span class="left">City</span> <span class="central-text">  @if($settings['company_city']){{$settings['company_city']}}, @endif </span> <span class="right">المدينة</span></div>
                <div class="border-no-mg-no-br"><span class="left">Country</span> <span class="central-text"> @if($settings['company_country']) {{$settings['company_country']}}@endif  </span> <span class="right">الدولة</span></div>
                <div class="border-no-mg-no-br"><span class="left">P.O.Box</span> <span class="central-text"> @if($settings['company_zipcode']) {{$settings['company_zipcode']}}@endif </span> <span class="right">الرمز البريدي</span></div>
                <div class="border-no-mg-no-br"><span class="left">C.R.</span> <span class="central-text"> @if(!empty($settings['registration_number'])){{$settings['registration_number']}} @endif </span> <span class="right">السجل التجاري</span></div>
                <div class="border-no-mg-no-br"><span class="left">Vat No.</span> <span class="central-text">
                  @if($settings['vat_gst_number_switch'] == 'on')
                      @if(!empty($settings['tax_type']) && !empty($settings['vat_number'])){{$settings['tax_type']}} : {{$settings['vat_number']}} @endif
                  @endif
             </span> <span class="right">الرقم الضريبي</span></div>
              </div>
            </div>
          </div>
          <table class="table" style="direction: rtl;">
          <thead>
          <tr class="titles" >
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



            @if(isset($proposal->itemData) && count($proposal->itemData) > 0)
            @php
              $total_product = 0;
              $total_discount = 0 ;
              $total_vat = 0 ;
              $total_one = 0 ;
            @endphp
            @foreach($proposal->itemData as $key => $item)
            <tr class="item">
              <td><span id="ProuductName">{{$item->name}}<span></span></span></td>

              @if(!empty($item->description) && $item->description !== "null")
              @php

                $description_array = json_decode($item->description, true);
              @endphp
              <td>
                @if(is_array($description_array))
                @foreach($description_array as $key => $value)
                  @if($key == 'المقاسات' || $key == 'الاضافات' || $key == 'الاختيارات')
                    {!! $key  !!}
                    @if(is_array($value))
                      @foreach($value as $v_product)
                        @if(is_array($v_product))
                          @foreach($v_product as $data_key => $data_value)
                            @if(is_string($data_value) || is_numeric($data_value))
                            @if($data_key == 'اسم المادة' || $data_key == 'اسم المادة' ||  $data_key == "اسم الاختيار"  || $data_key ==  "الاختيار" )
                              {!!  $data_value . '<br>' !!}
                              @endif
                            @endif
                          @endforeach
                          <hr>
                        @else
                          {!! $v_product !!}
                        @endif
                      @endforeach
                    @endif
                  @endif
                @endforeach
                @else
                    {{ $item->description }}
                @endif
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
        </div>
        <div class="row">
          @if(isset($settings['proposal_footer'])){!! $settings['proposal_footer'] !!}@endif
        </div>
      </div>


    </div>

  </div>

  <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
  <script>

          var filename = 'downnnnn';
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
      // saveAsPDF();


      document.addEventListener("keydown", function(event) {
    if (event.ctrlKey && event.key === "p") {
        event.preventDefault(); // لمنع فتح نافذة الطباعة الافتراضية
        saveAsPDF();
    }
});
  </script>


    @if(!isset($preview))
        @include('proposal.script');
    @endif

</body>

</html>
