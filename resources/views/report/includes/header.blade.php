<html>

<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1" name="viewport">
<title>Invoice</title>
<link rel="stylesheet" href="{{ asset('assets/invoice/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/invoice/style.css') }}">
</head>

<body>
<div class="invoice-box">
    <div class="container">
        <style>
        @media (max-width: 768px) {
            .invoice-box .row > [class^="col-"] {
                flex: 0 0 100%;
                max-width: 100%;
                text-align: center;
                margin-bottom: 10px;
            }
            .title-of-invoce_con {
                float: none !important;
                text-align: center;
            }
            .float-end {
                float: none !important;
                display: block;
                margin: 0 auto;
            }
        }
        </style>
        <div class="row">


            <div class="col-4">
                <div class="title-of-invoce_con" style="float: right; margin-bottom: 10px;">
                    <p class="title-of-invoce">
                        فاتورة
                        <br>
                        Invoice
                    </p>
                </div>
            </div>
            <div class="col-4">
                @if ($settings['header_bill'])
                {!! $settings['header_bill'] !!}
                @endif
            </div>
            <div class="col-4">
                <img class="float-end" width="70px" src="{{ $logo_url }}">
            </div>

            <div class="row">
                <div class="col-4">
                    @if (isset($qrCode))
                    {!! $qrCode !!}
                    @endif
                    </div>

            </div>
        </div>

    </div>
