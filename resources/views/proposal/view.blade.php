@extends('layouts.admin')
@section('page-title')
    {{__('Proposal Detail')}}
@endsection
@php
    $settings = Utility::settings();
@endphp
@push('script-page')
    <script>
        $(document).on('change', '.status_change', function () {
            var status = this.value;
            var url = $(this).data('url');
            $.ajax({
                url: url + '?status=' + status,
                type: 'GET',
                cache: false,
                success: function (data) {
                    show_toastr('success', '{{__('Proposal status changed successfully.')}}')
                },
            });
        });

        $('.cp_link').on('click', function () {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            show_toastr('success', '{{__('Link Copy on Clipboard')}}')
        });

        $(document).on('click', '#descriptionview', function (e) {
            e.preventDefault();

            // الإشارة إلى الزر الذي تم النقر عليه
            let description = $(this)
                .closest('td') // تحديد الخلية (أو العنصر الأب الأقرب) إذا كنت داخل جدول
                .find('.pro_description') // البحث عن textarea المتعلقة به
                .val(); // أخذ القيمة

            if (!description) {
                alert('Please enter a description.');
                return;
            }

            let form = $('<form>', {
                action: '{{ route("proposal.workorder") }}',
                method: 'POST',
                target: '_blank' // لفتح النتيجة في نافذة جديدة
            });

            form.append($('<input>', {
                type: 'hidden',
                name: 'description',
                value: description
            }));

            form.append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: '{{ csrf_token() }}'
            }));

            $('body').append(form);
            form.submit();
            form.remove();
        });
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
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('proposal.index')}}">{{__('Proposal')}}</a></li>
    <li class="breadcrumb-item">{{__('Proposal Details')}}</li>

@endsection


@section('content')
{{-- @dd($proposal) --}}
    @can('send proposal')
        @if($proposal->status!=4)
            <div class="row">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row timeline-wrapper">
                                <div class="col-md-6 col-lg-4 col-xl-4 create_invoice">
                                    <div class="timeline-icons"><span class="timeline-dots"></span>
                                        <i class="ti ti-plus text-primary"></i>
                                    </div>
                                    <h6 class="text-primary my-3">{{__('Create Proposal')}}</h6>
                                    <p class="text-muted text-sm mb-3"><i class="ti ti-clock mr-2"></i>{{__('Created on ')}}{{$proposal->issue_date}}</p>
                                    @can('edit proposal')
                                        <a href="{{ route('proposal.edit',\Crypt::encrypt($proposal->id)) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-original-title="{{__('Edit')}}"><i class="ti ti-pencil mr-2"></i>{{__('Edit')}}</a>
                                    @endcan
                                </div>
                                <div class="col-md-6 col-lg-4 col-xl-4 send_invoice">
                                    <div class="timeline-icons"><span class="timeline-dots"></span>
                                        <i class="ti ti-mail text-warning"></i>
                                    </div>
                                    <h6 class="text-warning my-3">{{__('Send Proposal')}}</h6>
                                    <p class="text-muted text-sm mb-3">
                                        @if($proposal->status!=0)
                                            <i class="ti ti-clock mr-2"></i>{{__('Sent on')}} {{ $proposal->send_date }}
                                        @else
                                            @can('send proposal')
                                                <small>{{__('Status')}} : {{__('Not Sent')}}</small>
                                            @endcan
                                        @endif
                                    </p>

                                    @if($proposal->status==0)
                                        @can('send proposal')
                                            <a href="{{ route('proposal.sent',$proposal->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-original-title="{{__('Mark Sent')}}"><i class="ti ti-send mr-2"></i>{{__('Send')}}</a>
                                        @endcan
                                    @endif
                                </div>
                                <div class="col-md-6 col-lg-4 col-xl-4 create_invoice">
                                    <div class="timeline-icons"><span class="timeline-dots"></span>
                                        <i class="ti ti-report-money text-info"></i>
                                    </div>
                                    <h6 class="text-info my-3">{{__('Proposal Status')}}</h6>
                                    <small>
                                        @if($proposal->status == 0)
                                            <span class="badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                        @elseif($proposal->status == 1)
                                            <span class="badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                        @elseif($proposal->status == 2)
                                            <span class="badge bg-success p-2 px-3 rounded">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                        @elseif($proposal->status == 3)
                                            <span class="badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                        @elseif($proposal->status == 4)
                                            <span class="badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                        @endif
                                    </small>
                                    <br>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan


    <div class="row justify-content-between align-items-center mb-3">
        <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                    @if($proposal->status!=0)
                    <div class="all-button-box mx-2">
                        <a href="{{ route('proposal.resent',$proposal->id) }}" class="btn btn-primary">{{__('Resend Proposal')}}</a>
                    </div>
                    @endif
                    <div class="all-button-box mx-2">
                        <a href="{{ route('proposal.pdf', Crypt::encrypt($proposal->id))}}" class="btn btn-primary" target="_blank">{{__('Print')}} <i class="ti ti-printer"></i></a>
                    </div>
                    @if($proposal->project==0)
                    <div class="all-button-box mx-2">
                        <a href="{{ route('proposal.project',$proposal->id) }}" class="btn btn-primary " data-bs-toggle="tooltip" data-original-title="{{__('Create Project')}}"><i class="ti ti-send mr-2"></i>{{__('Create Project')}}</a>
                    </div>
                    @endif
                    <div class="all-button-box mx-2">
                        <select class="form-control status_change mx-3" name="status" data-url="{{route('proposal.status.change',$proposal->id)}}">
                            @foreach($status as $k=>$val)
                                <option value="{{$k}}" {{($proposal->status==$k)?'selected':''}}>{{$val}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row invoice-title mt-2">
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12">
                                    <h4>{{__('Proposal')}}</h4>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12 text-end">
                                    <h4 class="invoice-number" style="direction: ltr">{{ Auth::user()->proposalNumberFormat($proposal->proposal_id) }}</h4>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-center">
                                    <h6 class="text-center">{{__('Proposal Details')}}</h6><hr>
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <div class="row">
                                                @if ($proposal->order_id != null)
                                                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                                    <p>
                                                        <strong>{{__('Reference Number')}}</strong><br>
                                                        <a class="btn btn-outline-primary" data-bs-toggle="tooltip" title="{{__('Click to copy')}}" data-original-title="{{__('Click to copy')}}" style="direction: ltr" onclick="copyText(this)">{{ AUth::user()->ordersNumberFormat($proposal->order_id) }}</a>
                                                    </p>
                                                </div>
                                                @endif
                                                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                                    <p>
                                                        <strong>{{__('Issue Date')}}</strong><br>
                                                        {{ \Carbon\Carbon::parse($proposal->issue_date)->format('Y-m-d') }}<br>
                                                        {{ \Carbon\Carbon::parse($proposal->issue_date)->format('H:i:s') }}

                                                    </p>
                                                </div>
                                                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                                    <p>
                                                        <strong>{{__('Due Date')}}</strong><br>
                                                        {{ \Carbon\Carbon::parse($proposal->due_date)->format('Y-m-d') }}<br>
                                                        {{ \Carbon\Carbon::parse($proposal->due_date)->format('H:i:s') }}
                                                    </p>
                                                </div>
                                                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                                    <p>
                                                        <strong>{{__('Customer')}}</strong><br>
                                                        {{ $proposal->customer->name }}
                                                    </p>
                                                </div>
                                                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                                    <p>
                                                        <strong>{{__('Email')}}</strong><br>
                                                        {{ $proposal->customer->email ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                                    <p>
                                                        <strong>{{__('Contact')}}</strong><br>
                                                        {{ $proposal->customer->contact ?? '-' }}
                                                    </p>
                                                </div>

                                                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                                    <p>
                                                        <strong>{{__('By User')}}</strong><br>
                                                        {{ $proposal->user_id != null ? $proposal->user->name : '-' }}
                                                    </p>
                                                </div>

                                                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                                    <p>
                                                        <strong>{{__('Branch')}}</strong><br>
                                                        {{ App\Models\Employee::where('user_id', $proposal->user_id)->first()->branch->name ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                                    <p>
                                                        <strong>{{__('Department')}}</strong><br>
                                                        {{ App\Models\Employee::where('user_id', $proposal->user_id)->first()->department->name ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>







                                    <div class="d-flex align-items-center justify-content-end">
                                        {{$proposal->proposal_details}}
                                    </div>
                                    <div class="d-flex align-items-center justify-content-end">
                                        @if ($proposal->project == 1)
                                        {{ __('Projects Details') . ' : '}}
                                        <a href="#" class="btn btn-outline-secondary">{{$proposal->project_id}}</a>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col">

                                    <small class="font-style">
                                        <strong>{{__('Billed To')}} :</strong><br>
                                        @if(!empty($proposal->address))
                                        @php
                                            $address_billing = json_decode($proposal->address,true);
                                            echo $address_billing['billing_name'] . '<br>';
                                            echo $address_billing['billing_country'] . '<br>';
                                            echo $address_billing['billing_state'] . '<br>';
                                            echo $address_billing['billing_city'] . '<br>';
                                            echo $address_billing['billing_phone'] . '<br>';
                                            echo $address_billing['billing_zip'] . '<br>';
                                            echo $address_billing['billing_address'] . '<br>';
                                        @endphp
                                        @else
                                            @if(!empty($customer->billing_name))
                                                {{!empty($customer->billing_name)?  $customer->billing_name:''}}<br>
                                                {{!empty($customer->billing_address)?  $customer->billing_address:''}}<br>
                                                {{!empty($customer->billing_city)?  $customer->billing_city:''}}<br>
                                                {{!empty($customer->billing_state)?  $customer->billing_state:''}},
                                                {{!empty($customer->billing_zip)?  $customer->billing_zip:''}}<br>
                                                {{!empty($customer->billing_country)?  $customer->billing_country:''}}<br>
                                                {{!empty($customer->billing_phone)?  $customer->billing_phone:''}}

                                                @if($settings['vat_gst_number_switch'] == 'on')
                                                    <strong>{{__('Tax Number ')}} : </strong>{{!empty($customer->tax_number)?$customer->tax_number:''}}
                                                @endif
                                            @endif
                                        @endif
                                    </small>
                                </div>

                                @if(App\Models\Utility::getValByName('shipping_display')=='on')
                                    <div class="col">
                                        <small>
                                            <strong>{{__('Shipped To')}} :</strong><br>
                                            @if(!empty($proposal->address))
                                            @php
                                                $address_billing = json_decode($proposal->address,true);
                                                echo $address_billing['shipping_name'] . '<br>';
                                                echo $address_billing['shipping_country'] . '<br>';
                                                echo $address_billing['shipping_state'] . '<br>';
                                                echo $address_billing['shipping_city'] . '<br>';
                                                echo $address_billing['shipping_phone'] . '<br>';
                                                echo $address_billing['shipping_zip'] . '<br>';
                                                echo $address_billing['shipping_address'] . '<br>';
                                            @endphp
                                            @else
                                                @if(!empty($customer->shipping_name))
                                                    {{!empty($customer->shipping_name)?  $customer->shipping_name:''}}<br>
                                                    {{!empty($customer->shipping_address)?  $customer->shipping_address:''}}<br>
                                                    {{!empty($customer->shipping_city)?  $customer->shipping_city:''}}<br>
                                                    {{!empty($customer->shipping_state)?  $customer->shipping_state:''}}<br>
                                                    {{!empty($customer->shipping_zip)?  $customer->shipping_zip:''}}<br>
                                                    {{!empty($customer->shipping_country)?  $customer->shipping_country:''}}<br>
                                                    {{!empty($customer->shipping_phone)?  $customer->shipping_phone:''}}
                                                @endif
                                            @endif
                                        </small>
                                    </div>
                                @endif
                                    <div class="col">
                                        <div class="float-end mt-3">
                                        {!! DNS2D::getBarcodeHTML( route('proposal.link.copy',\Illuminate\Support\Facades\Crypt::encrypt($proposal->id)), "QRCODE",2,2) !!}
                                        </div>
                                    </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <small>
                                        <strong>{{__('Status')}} :</strong><br>
                                        @if($proposal->status == 0)
                                            <span class="badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                        @elseif($proposal->status == 1)
                                            <span class="badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                        @elseif($proposal->status == 2)
                                            <span class="badge bg-success p-2 px-3 rounded">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                        @elseif($proposal->status == 3)
                                            <span class="badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                        @elseif($proposal->status == 4)
                                            <span class="badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Proposal::$statues[$proposal->status]) }}</span>
                                        @endif
                                    </small>
                                </div>


                            </div>

                            @if(!empty($customFields) && count($proposal->customField)>0)
                                @foreach($customFields as $field)
                                    <div class="col text-end">
                                        <small>
                                            <strong>{{$field->name}} :</strong><br>
                                            {{!empty($proposal->customField)?$proposal->customField[$field->id]:'-'}}
                                            <br><br>
                                        </small>
                                    </div>
                                @endforeach
                            @endif
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="font-weight-bold">{{__('Product Summary')}}</div>
                                    <small>{{__('All items here cannot be deleted.')}}</small>
                                    <div class="table-responsive mt-2">
                                        <table class="table mb-0 invoice-body">
                                            <thead>
                                                <tr>
                                                <th class="text-dark" data-width="40">#</th>
                                                <th class="text-dark">{{__('Product')}}</th>
                                                <th class="text-dark">{{__('Quantity')}}</th>
                                                <th class="text-dark">{{__('Rate')}}</th>
                                                    {{-- <th class="text-dark"> {{__('Discount')}}</th> --}}
                                                <th class="text-dark">{{__('Tax')}}</th>

                                                <th colspan="2" class="text-dark">{{__('Description')}}</th>
                                                <th class="text-end text-dark" width="12%">{{__('Price')}}<br>
                                                    <small class="text-danger font-weight-bold">{{__('after tax')}}</small>
                                                </th>
                                            </tr>
                                            </thead>
                                            @php
                                                $totalQuantity=0;
                                                $totalRate=0;
                                                $totalTaxPrice=0;
                                                // $totalDiscount=0;
                                                $taxesData=[];
                                                $subtotal = 0;
                                                $finaltotal = 0;
                                            @endphp
                                            @foreach($iteams as $key =>$iteam)
                                                @php
                                                    $description = json_decode($iteam->description,true);
                                                @endphp
                                                    @if(is_array($description)&& $description['info']['type'] == 'custom')
                                                    @php
                                                        // dd($description['info']['name']);
                                                        $productName = $description['info']['name'];
                                                        $totalQuantity += $iteam->quantity;
                                                        $totalRate += $iteam->price;
                                                        // $totalDiscount += $iteam->discount;
                                                        $descrp = json_decode($iteam->description,true);

                                                        foreach($description as $key_des => $value){
                                                            if($key_des == 'التسعيرة النهائية'){
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
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ !empty($productName) ? $productName : '' }}</td>
                                                    <td>{{ $iteam->quantity }}</td>
                                                    <td>{{\Auth::user()->priceFormat($total/$iteam->quantity)}}</td>
                                                    {{-- <td>{{\Auth::user()->priceFormat($iteam->discount)}}</td> --}}
                                                    <td>15%</td>

                                                    {{-- <td colspan="2"><p style="width: 220px; overflow:auto;">{{!empty($descrp)?$descrp:'-'}}</p></td> --}}
                                                    <td colspan="2"><p style="overflow:auto;direction:rtl;text-align:center;">
                                                        @if(empty($descrp))
                                                        لا يوجد
                                                    @else
                                                    {{-- @dd($descrp) --}}
                                                    @php
                                                        $workorder = json_encode($iteam->description,true);
                                                    @endphp
                                                        <a href="#" class="btn btn-primary btn-sm" id="descriptionview">{{ __('view workorder') }}<i class="ti ti-link"></i></a>
                                                        <textarea class="form-control pro_description d-none" rows="1" placeholder="الوصف"  name="items[][description]">{{ $workorder }}</textarea><br>

                                                        @foreach($descrp as $key_dd => $value)
                                                        @if($key_dd == 'attachment')
                                                        <a target="__blank" class="btn btn-primary btn-sm" href="{{ url('storage/').$value}}">{{ __('view attachment') }}<i class="ti ti-eye"></i></a><br>
                                                        @endif

                                                        @if($key_dd == 'design_note')
                                                            <textarea style="width: 100%" readonly>{{ $value }}</textarea><br>
                                                        @endif
                                                            @if($key_dd == 'المقاسات')
                                                                المقاسات (
                                                                @foreach($value as $val)
                                                                    {{ $val }}{{ !$loop->last ? 'x' : '' }}
                                                                @endforeach
                                                                ) <br>
                                                            @endif

                                                            @if($key_dd == 'الاضافات')
                                                                الاضافات =
                                                                @foreach($value as $val)
                                                                    (
                                                                    @foreach($val as $k => $v)
                                                                        @if($k == 'اسم المادة')
                                                                            {{ $v }}
                                                                        @endif
                                                                    @endforeach
                                                                    )
                                                                @endforeach
                                                                <br>
                                                            @endif

                                                            @if($key_dd == 'الاختيارات')
                                                                الاختيارات =
                                                                @foreach($value as $val)
                                                                    (
                                                                    @foreach($val as $k => $v)
                                                                        @if($k == 'اسم الاختيار')
                                                                            {{ $v }} =
                                                                        @endif
                                                                        @if($k == 'الاختيار')
                                                                            {{ $v }}
                                                                        @endif
                                                                    @endforeach
                                                                    ) <br>
                                                                @endforeach
                                                                <br>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    </p></td>
                                                    <td class="text-end">{{\Auth::user()->priceFormat($total_with_tax)}}</td>
                                                </tr>
                                                @php
                                                // $subtotal += $totalTaxPrice;
                                                $finaltotal += $total_with_tax;
                                                @endphp
                                                    @else
                                                    @php
                                                        $productName = $iteam->product;
                                                        $totalQuantity += $iteam->quantity;
                                                        $totalRate += $iteam->price;
                                                        // $totalDiscount += $iteam->discount;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                    <td>{{ !empty($productName) ? $productName->name : '' }}</td>
                                                    <td>{{ $iteam->quantity . ' (' . $productName->unit->name . ')' }}</td>
                                                    <td>{{\Auth::user()->priceFormat($iteam->price)}}</td>
                                                    {{-- <td>{{\Auth::user()->priceFormat($iteam->discount)}}</td> --}}
                                                    <td>
                                                        @if (!empty($iteam->tax))
                                                            <table>
                                                                @php
                                                                    $itemTaxes = [];
                                                                    $getTaxData = Utility::getTaxData();

                                                                    if (!empty($iteam->tax)) {
                                                                        foreach (explode(',', $iteam->tax) as $tax) {
                                                                            $taxPrice = \Utility::taxRate($getTaxData[$tax]['rate'], $iteam->price, $iteam->quantity);
                                                                            $totalTaxPrice += $taxPrice;
                                                                            $itemTax['name'] = $getTaxData[$tax]['name'];
                                                                            $itemTax['rate'] = $getTaxData[$tax]['rate'] . '%';
                                                                            $itemTax['price'] = \Auth::user()->priceFormat($taxPrice);

                                                                            $itemTaxes[] = $itemTax;
                                                                            if (array_key_exists($getTaxData[$tax]['name'], $taxesData)) {
                                                                                $taxesData[$getTaxData[$tax]['name']] = $taxesData[$getTaxData[$tax]['name']] + $taxPrice;
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
                                                                        <tr>
                                                                            <td>{{$tax['name'] .' ('.$tax['rate'] .'%)'}}</td>
                                                                            <td>{{ $tax['price']}}</td>
                                                                        </tr>
                                                                @endforeach
                                                            </table>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <td colspan="2"><p style="width: 220px; overflow:auto;">{{!empty($iteam->description) && $iteam->description != 'null'?$iteam->description:'-'}}</p></td>
                                                    <td class="text-end">{{\Auth::user()->priceFormat(($iteam->price * $iteam->quantity) + $totalTaxPrice)}}</td>
                                                </tr>
                                                    @php
                                                    // $subtotal += $totalTaxPrice;
                                                    $finaltotal += ($iteam->price * $iteam->quantity) + $totalTaxPrice;
                                                    @endphp
                                                @endif
                                            @endforeach
                                            <tfoot>


                                            <tr>
                                                <td colspan="6"></td>
                                                <td class="blue-text text-end"><b>{{__('Total')}}</b></td>
                                                <td class="blue-text text-end">{{\Auth::user()->priceFormat($finaltotal)}}</td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                        <div class="invoice-footer">
                                            <b>{{$settings['footer_title']}}</b> <br>
                                            {{-- {!! $settings['footer_notes'] !!} --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
