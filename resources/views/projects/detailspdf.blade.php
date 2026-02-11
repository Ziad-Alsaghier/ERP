@php

    $settings_data = \App\Models\Utility::settingsById(\Auth::user()->creatorId());
    $logo = \App\Models\Utility::get_file('uploads/logo/');

    $description_details = $descriptionArray['description'];
    $descriptionArray_details = json_decode($description_details, true);
@endphp
    <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <title>invoice</title>
  <style>

    @import url(https://fonts.googleapis.com/css?family=Lato:400,300,300italic,400italic,700,700italic);
    /** GLOBAL **/

    html, body {
    height: 100%;
    background:#0f3866;
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
    border: solid 1px black;
    }
    .table th, .table td {
    text-align: left;
    padding: 0.25em;
    border: solid 1px black;

    }
    .table tr {
    border-bottom: 1px solid #DDD;
    }
    .table , .table tr , .table td , .table th{
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
        <div class="eqWrap top-header">
          @if (!empty($settings_data['social_link_bill']))
            <div class="eq">
              {!! DNS2D::getBarcodeHTML($settings_data['social_link_bill'], "QRCODE",2,2) !!}
            </div>
          @endif
          <div class="eq" style="width:100%; max-width:115px;">
            @if(isset($settings_data['header_bill'])){!! $settings_data['header_bill'] !!}@endif
        </div>
          <div class="equalHW eq image-of-header">
            <img src="{{url('/').'/'.$logo.$settings_data['company_logo_light']}}" >
          </div>
        </div>

        <div class="title-of-invoce_con" >
          <p class="title-of-invoce" style="background-color:#0f3866; color:white;">{{__('work order',[],'ar')}} <br> {{__('work order',[],'en')}}  </p>
        </div>
        <div class="row">
          <div class="row">
          <div class="eqWrap" >
            <div class="eq infoblock from-block central-col" style="width: 100%;">
              @php
              $namearr = json_decode($descriptionArray['description'],true)
              @endphp
              <div class="border"><span class="left">{{__('Name',[],'en')}}</span> <span class="central-text"> {{$namearr['info']['name']}} </span> <span class="right">{{__('Name',[],'ar')}}</span></div>
                <div class="border"><span class="left">{{__('Proposal',[],'en')}}</span> <span class="central-text"> {{$descriptionArray['proposal_id']}}  </span> <span class="right">{{__('Proposal',[],'ar')}}</span></div>
                <div class="border"><span class="left">{{__('Quantity',[],'en')}}</span> <span class="central-text"> {{$descriptionArray['quantity']}} </span> <span class="right">{{__('Quantity',[],'ar')}}</span></div>
            </div>
            </div>
          </div>


          <style>
            .table-details td {
                padding: 8px;
                vertical-align: top;
            }
            .table-details .data-row td {
                border-bottom: 1px solid #ddd;
                padding: 0px;
            }
            .table-details th {
                background-color: #f9f9f9;
                font-weight: bold;
            }
            .table-details .separator-row td {
                border: none;
                padding: 0;
            }
            .table-details .separator {
                border-bottom: 2px solid #ccc;
                height: 1px;
                margin: 0;
            }
        </style>

        <table class="table table-details">
          @foreach ($descriptionArray_details as $details_head => $details)
              @if ($details_head !== 'التسعيرة النهائية')
                  <tr>
                      <th scope="col" colspan="2">{{$details_head}}</th>
                  </tr>
                  @if (is_array($details))
                      @foreach ($details as $sub_index => $sub_details)
                          @if ($sub_index > 0)
                              <tr class="separator-row">
                                  <td colspan="2" class="separator"></td>
                              </tr>
                          @endif
                          @if (is_array($sub_details))
                              @foreach ($sub_details as $key => $value)
                                  @if (!preg_match('/سعر|اجمالي المبلغ/', $key) && !preg_match('/سعر|اجمالي المبلغ/', $value))
                                      <tr class="data-row">
                                          <td>{{$key}}</td>
                                          <td>{{$value}}</td>
                                      </tr>
                                  @endif
                              @endforeach
                          @else
                              @if (!preg_match('/سعر|اجمالي المبلغ/', $sub_index) && !preg_match('/سعر|اجمالي المبلغ/', $sub_details))
                                  <tr class="data-row">
                                      <td>{{$sub_index}}</td>
                                      <td>{{$sub_details}}</td>
                                  </tr>
                              @endif
                          @endif
                      @endforeach
                  @else
                      @if (!preg_match('/سعر|اجمالي المبلغ/', $details))
                          <tr class="data-row">
                              <td colspan="2">{{$details}}</td>
                          </tr>
                      @endif
                  @endif
              @endif
          @endforeach
      </table>



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
                    scale: 4,
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
</body>

</html>
