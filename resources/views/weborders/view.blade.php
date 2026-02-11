@extends('layouts.admin')
@section('page-title')
    {{ __('Order') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('web_orders.index') }}">{{ __('Orders') }}</a></li>
    <li class="breadcrumb-item">{{ AUth::user()->ordersNumberFormat($order->id) }}</li>
@endsection
@push('script-page')
<script>
    function copyText(element) {
        // احصل على النص الداخلي للعنصر
        const textToCopy = element.textContent || element.innerText;

        // استخدم Clipboard API لنسخ النص إلى الحافظة
        navigator.clipboard.writeText(textToCopy)
            .then(() => {
                // غيّر النص بعد النسخ لتوضيح أن النص تم نسخه
                element.setAttribute('title', 'Copied!');
                element.setAttribute('data-original-title', 'Copied!');
                element.classList.add('btn-success');
                element.classList.remove('btn-outline-primary');
                alert('{{ __("Copied to clipboard") }}');
                // إعادة النص الأصلي بعد وقت قصير
                setTimeout(() => {
                    element.setAttribute('title', '{{ __("Click to copy") }}');
                    element.setAttribute('data-original-title', '{{ __("Click to copy") }}');
                    element.classList.add('btn-outline-primary');
                    element.classList.remove('btn-success');
                }, 2000); // 2 ثانية
            })
            .catch(err => {
                console.error('Failed to copy text: ', err);
            });
    }

    $(document).on('change', '.status_change', function () {
            var status = this.value;
            var url = $(this).data('url');
            $.ajax({
                url: url + '?status=' + status,
                type: 'GET',
                cache: false,
                success: function (data) {
                    show_toastr('success', '{{__('Order status changed successfully.')}}')
                },
            });
        });
</script>

@endpush
@section('content')
{{-- @dd($order) --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                @php
                    $profile = json_decode($order->profile_details, true);
                    $payment = json_decode($order->payment_details, true);
                    $shipping = json_decode($order->shipping_details, true);
                    $billing = json_decode($order->billing_details, true);
                @endphp

<div class="row">
    {{-- <div class="col-md-12 my-3">
        <a class="btn btn-primary btn-sm" href="{{route('web_users.proposal',$order->id)}}">{{__('convert into proposal')}}</a>
    </div> --}}
    <div class="col-md-6">
        <h3>{{__('Order Details')}}</h3>
        <table class="table table-bordered">
            <tr>
                <td>{{__('Order Date')}} :</td>
                <td>{{$order->created_at}}</td>
            </tr>
            <tr>
                <td>{{__('Order Status')}} :</td>
                <td>
                    @php
                    $status = array(
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    )
                @endphp
                    <select class="form-control status_change mx-3" name="status" data-url="{{route('web_orders.status.change',$order->id)}}">
                        @foreach($status as $k=>$val)
                            <option value="{{$k}}" {{($order->status==$k)?'selected':''}}>{{__($val)}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{__('Reference Number')}} :</td>
                <td><a class="btn btn-outline-primary" data-bs-toggle="tooltip" title="{{__('Click to copy')}}" data-original-title="{{__('Click to copy')}}" onclick="copyText(this)">{{ AUth::user()->ordersNumberFormat($order->id) }}</a></td>
            </tr>
            <tr>
                <td>{{__('Proposal')}} :</td>
                @php
                if($order->quotation_details){
                    $proposal = App\Models\Proposal::where('order_id', $order->id)->get();
                }
                @endphp
                @if(isset($proposal))
                <td>
                    @foreach ($proposal as $proposals)
                    <a class="btn btn-outline-primary my-1" data-bs-toggle="tooltip" title="{{__('Show Proposal')}}" data-original-title="{{__('Show Proposal')}}" href="{{route('proposal.show',\Crypt::encrypt($proposals->id))}}">{{ Auth::user()->proposalNumberFormat($proposals->proposal_id)}} </a><br>
                    @endforeach
                </td>
                @else
                <td><a class="btn btn-primary btn-sm" href="{{route('web_users.proposal',$order->id)}}">{{__('convert into proposal')}}</a></td>

                @endif
            </tr>
            {{-- <tr>
                <td>{{__('Invoice')}} :</td>
                @php
                if(isset($proposal)){
                    $invoice = App\Models\Invoice::where('id', $proposal->converted_invoice_id)->first();
                }
                // dd($invoice)
                @endphp
                @if(isset($proposal) && $proposal->is_convert == 1)
                <td><a class="btn btn-outline-primary" title="{{__('Show Invoice')}}" data-original-title="{{__('Show Invoice')}}"  href="{{route('invoice.show',\Crypt::encrypt($proposal->converted_invoice_id))}}">{{ Auth::user()->invoiceNumberFormat($invoice->invoice_id)}} </a></td>
                @endif
            </tr> --}}

        </table>
    </div>
    <div class="col-md-6">
        <h3>{{__('Payment Details')}}</h3>
        <table class="table table-bordered">
            <tr>
                <td>{{__('Payment Method')}} :</td>
                <td>{{$payment['method']}}</td>
            </tr>
            <tr>
                <td>{{__('Bank Name')}} :</td>
                <td>{{$payment['bank name']}}</td>
            </tr>
            <tr>
                <td>{{__('Account Name')}} :</td>
                <td>{{$payment['account name']}}</td>
            </tr>
            <tr>
                <td>{{__('Transfer Date')}} :</td>
                <td>{{$payment['transfer date']}}</td>
            </tr>
            <tr>
                <td>{{__('Account Number')}} :</td>
                <td>{{$payment['account number']}}</td>
            </tr>
            <tr>
                <td>{{__('Transfer Number')}} :</td>
                <td>{{$payment['transfer number']}}</td>
            </tr>
            <tr>
                <td>{{__('Total')}} :</td>
                <td>{{Auth::user()->priceFormat($payment['total'])}}</td>
            </tr>
            <tr>
                <td>{{__('Payed Amount')}} :</td>
                <td>{{Auth::user()->priceFormat($payment['payed'])}}</td>
            </tr>
            <tr>
                <td>{{__('Total Balance')}} :</td>
                <td>{{Auth::user()->priceFormat($payment['rest'])}}</td>
            </tr>
        </table>
    </div>

    <div class="col-md-6">
        <h3>{{__('Shipping Details')}}</h3>
        <table class="table table-bordered">
            <tr>
                <td>{{__('Country')}} :</td>
                <td>{{$shipping['country']}}</td>
            </tr>
            <tr>
                <td>{{__('City')}} :</td>
                <td>{{$shipping['city']}}</td>
            </tr>
            <tr>
                <td>{{__('State')}} :</td>
                <td>{{$shipping['state']}}</td>
            </tr>
            <tr>
                <td>{{__('Zip Code')}} :</td>
                <td>{{$shipping['zip_code']}}</td>
            </tr>
            <tr>
                <td>{{__('Nearest Location')}} :</td>
                <td>{{$shipping['nearest_location']}}</td>
            </tr>
            <tr>
                <td>{{__('Address')}} :</td>
                <td>{{$shipping['address']}}</td>
            </tr>

            <tr>
                <td>{{__('Name')}} :</td>
                <td>{{$shipping['first_name'] . ' ' . $shipping['last_name']}}</td>
            </tr>
            <tr>
                <td>{{__('Company')}} :</td>
                <td>{{$shipping['company_name'] }}</td>
            </tr>

            <tr>
                <td>{{__('Email')}} :</td>
                <td>{{$shipping['email']}}</td>
            </tr>
            <tr>
                <td>{{__('Contact Number')}} :</td>
                <td>{{$shipping['contact_num']}}</td>
            </tr>
        </table>
    </div>

    <div class="col-md-6">
        <h3>{{__('Invoice Details')}}</h3>
        <table class="table table-bordered">
            <tr>
                <td>{{__('Country')}} :</td>
                <td>{{$billing['country']}}</td>
            </tr>
            <tr>
                <td>{{__('City')}} :</td>
                <td>{{$billing['city']}}</td>
            </tr>
            <tr>
                <td>{{__('State')}} :</td>
                <td>{{$billing['state']}}</td>
            </tr>
            <tr>
                <td>{{__('Zip Code')}} :</td>
                <td>{{$billing['zip_code']}}</td>
            </tr>
            <tr>
                <td>{{__('Address')}} :</td>
                <td>{{$billing['address']}}</td>
            </tr>

            <tr>
                <td>{{__('Name')}} :</td>
                <td>{{$billing['first_name'] . ' ' . $billing['last_name']}}</td>
            </tr>
            <tr>
                <td>{{__('Company')}} :</td>
                <td>{{$billing['company_name'] }}</td>
            </tr>

            <tr>
                <td>{{__('Email')}} :</td>
                <td>{{$billing['email']}}</td>
            </tr>
            <tr>
                <td>{{__('Contact Number')}} :</td>
                <td>{{$billing['contact_num']}}</td>
            </tr>
            <tr>
                <td>{{__('Vat Number')}} :</td>
                <td>{{$billing['vat_num']}}</td>
            </tr>
            <tr>
                <td>{{__('CR number')}} :</td>
                <td>{{$billing['tax_num']}}</td>
            </tr>
        </table>
    </div>

</div>
<h3 class="text-center py-2">{{__('Order Products')}}</h3>
<table class="table table-bordered text-center">
    <thead>
        <tr>
            <th>{{__('Product')}}</th>
            <th>{{__('SKU')}}</th>
            <th>{{__('Category')}}</th>
            <th>{{__('Type')}}</th>
            <th>{{__('Status')}}</th>
            <th>{{__('Price')}}</th>
            <th>{{__('Quantity')}}</th>
            <th>{{__('Total')}}</th>
            <th>{{__('Tax')}}</th>
            <th>{{__('Total with Tax')}}</th>
            {{-- <th width="100px">{{__('Action')}}</th> --}}
        </tr>
    </thead>
    <tbody>
        @foreach($orders_products as $product)
        @if($product->type == 'custom')
        @php
        $description = json_decode($product->description, true);
                                    foreach($description as $key => $value){
                                    if($key == 'التسعيرة النهائية'){
                                        // dd($value);
                                        foreach($value as $ke => $val){
                                            if($ke == 'الاجمالي'){
                                            $val_numeric = floatval(str_replace(',', '', $val)); // تحويل النص إلى رقم
                                            $total = $val_numeric;
                                            }

                                            if($ke == 'الاجمالي بعد الضريبة'){
                                            $val_numeric = floatval(str_replace(',', '', $val)); // تحويل النص إلى رقم
                                            $total_with_tax = $val_numeric;
                                            }


                                            if($ke == 'الدفعة الاولي بعد الضريبة'){
                                            $val_numeric = floatval(str_replace(',', '', $val)); // تحويل النص إلى رقم
                                            $first_payment = $val_numeric;
                                            }
                                        }
                                    }
                                }

        @endphp
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->sku}}</td>
            <td>{{ $product->category}}</td>
            <td>{{ $product->type}}</td>
            <td>
                <select class="form-control status_change mx-3" name="status" data-url="{{route('web_orders_product.status.change',$product->id)}}">
                    @foreach($status as $k=>$val)
                        <option value="{{$k}}" {{($product->status==$k)?'selected':''}}>{{__($val)}}</option>
                    @endforeach
                </select>
            </td>
            <td>{{ Auth::user()->priceFormat($total/$product->count,5) }}</td>
            <td>{{ $product->count }}</td>
            <td>{{ Auth::user()->priceFormat($total) }}</td>
            <td>{{ Auth::user()->priceFormat(($total)*0.15) }}</td>
            <td>{{ Auth::user()->priceFormat(($total)*1.15) }}</td>
            {{-- <td> --}}
                {{-- <a class="btn btn-primary btn-sm" href="#" role="button"><i class="bi bi-info"></i></a> --}}
                {{-- <a class="btn btn-danger btn-sm" href="{{route('web_orders.productorderdelete', $product->id)}}" role="button"><i class="ti ti-trash text-white"></i></a> --}}
            {{-- </td> --}}
        </tr>
        @else
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->sku}}</td>
            <td>{{ $product->category}}</td>
            <td>{{ $product->type}}</td>
            <td>
                <select class="form-control status_change mx-3" name="status" data-url="{{route('web_orders_product.status.change',$product->id)}}">
                    @foreach($status as $k=>$val)
                        <option value="{{$k}}" {{($product->status==$k)?'selected':''}}>{{__($val)}}</option>
                    @endforeach
                </select>
            </td>
            <td>{{ Auth::user()->priceFormat(($product->price)-($product->price*0.15)) }}</td>
            <td>{{ $product->count }}</td>
            <td>{{ Auth::user()->priceFormat(($product->price * $product->count) - (($product->price * $product->count)*0.15)) }}</td>
            <td>{{ Auth::user()->priceFormat(($product->price * $product->count)*0.15) }}</td>
            <td>{{Auth::user()->priceFormat($product->price * $product->count)}}</td>
            {{-- <td> --}}
                {{-- <a class="btn btn-primary btn-sm" href="#" role="button"><i class="bi bi-info"></i></a> --}}
                {{-- <a class="btn btn-danger btn-sm" href="{{route('web_orders.productorderdelete', $product->id)}}" role="button"><i class="ti ti-trash text-white"></i></a> --}}
            {{-- </td> --}}
        </tr>

        @endif
        @endforeach
</table>


            </div>
        </div>
    </div>
</div>
@endsection
