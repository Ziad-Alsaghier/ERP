@extends('layouts.admin')
@section('page-title')
    {{ __('Credit Note Detail') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">{{ __('Invoice') }}</a></li>
    <li class="breadcrumb-item">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</li>
@endsection
@section('content')


    @can('send invoice')
        @if ($invoice->status != 4)
            <div class="row">
                <div class="col-12">
                    <div class="card ">
                        <div class="card-body">
                            <div class="row timeline-wrapper">
                                <div class="col-md-6 col-lg-4 col-xl-4 create_invoice">
                                    <div class="timeline-icons"><span class="timeline-dots"></span>
                                        <i class="ti ti-plus text-primary"></i>
                                    </div>
                                    <h6 class="text-primary my-3">{{ __('Create Invoice') }}</h6>
                                    <p class="text-muted text-sm mb-3"><i
                                            class="ti ti-clock mr-2"></i>{{ __('Created on ') }}{{ $invoice->issue_date }}
                                    </p>
                                    @can('edit invoice')
                                        <a href="{{ route('invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                                            class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                            data-original-title="{{ __('Edit') }}"><i
                                                class="ti ti-pencil mr-2"></i>{{ __('Edit') }}</a>
                                    @endcan
                                </div>
                                <div class="col-md-6 col-lg-4 col-xl-4 send_invoice">
                                    <div class="timeline-icons"><span class="timeline-dots"></span>
                                        <i class="ti ti-mail text-warning"></i>
                                    </div>
                                    <h6 class="text-warning my-3">{{ __('Send Invoice') }}</h6>
                                    <p class="text-muted text-sm mb-3">
                                        @if ($invoice->status != 0)
                                            <i class="ti ti-clock mr-2"></i>{{ __('Sent on') }}
                                            {{ $invoice->send_date }}
                                        @else
                                            @can('send invoice')
                                                <small>{{ __('Status') }} : {{ __('Not Sent') }}</small>
                                            @endcan
                                        @endif
                                    </p>

                                    @if ($invoice->status == 0)
                                        @can('send bill')
                                            <a href="{{ route('invoice.sent', $invoice->id) }}" class="btn btn-sm btn-warning"
                                                data-bs-toggle="tooltip" data-original-title="{{ __('Mark Sent') }}"><i
                                                    class="ti ti-send mr-2"></i>{{ __('Send') }}</a>
                                        @endcan
                                    @endif
                                </div>
                                <div class="col-md-6 col-lg-4 col-xl-4 create_invoice">
                                    <div class="timeline-icons"><span class="timeline-dots"></span>
                                        <i class="ti ti-report-money text-info"></i>
                                    </div>
                                    <h6 class="text-info my-3">{{ __('Get Paid') }}</h6>
                                    <p class="text-muted text-sm mb-3">{{ __('Status') }} : {{ __('Awaiting payment') }} </p>
                                    @if ($invoice->status != 0)
                                        @can('create payment invoice')
                                            <a href="#" data-url="{{ route('invoice.payment', $invoice->id) }}"
                                                data-ajax-popup="true" data-title="{{ __('Add Payment') }}"
                                                class="btn btn-sm btn-info" data-original-title="{{ __('Add Payment') }}"><i
                                                    class="ti ti-report-money mr-2"></i>{{ __('Receive Payment') }}</a> <br>
                                        @endcan
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (Gate::check('show invoice'))
            @if ($invoice->status != 0)
                <div class="row justify-content-between align-items-center mb-3">
                    <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                        @if (!empty($creditnote))
                            <div class="all-button-box mx-2 mr-2">
                                <a href="#" class="btn btn-sm btn-primary"
                                    data-url="{{ route('invoice.credit.note', $invoice->id) }}" data-ajax-popup="true"
                                    data-title="{{ __('Add Credit Note') }}">
                                    {{ __('Add Credit Note') }}
                                </a>
                            </div>
                        @endif
                        @if ($invoice->status != 4)
                            <div class="all-button-box mr-2">
                                <a href="{{ route('invoice.payment.reminder', $invoice->id) }}"
                                    class="btn btn-sm btn-primary me-2">{{ __('Receipt Reminder') }}</a>
                            </div>
                        @endif
                        <div class="all-button-box mr-2">
                            <a href="{{ route('invoice.resent', $invoice->id) }}"
                                class="btn btn-sm btn-primary me-2">{{ __('Resend Invoice') }}</a>
                        </div>
                        <div class="all-button-box">
                            <a href="{{ route('invoice.pdf', Crypt::encrypt($invoice->id)) }}" target="_blank"
                                class="btn btn-sm btn-primary">{{ __('Print') }}</a>
                        </div>
                        
                    </div>
                    
                </div>
            @endif
        @endif
    @endcan

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row invoice-title mt-2">
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12">
                                    <h4>Invoice</h4>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12 text-end">
                                    <h4 class="invoice-number">
                                        {{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</h4>
                                       
                                    </h4>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-center">Proposal Details</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                            <p>
                                                <strong>Issue Date</strong><br>
                                                {{ \Carbon\Carbon::parse($invoice->issue_date)->format('Y-m-d') }}<br>
                                                {{ \Carbon\Carbon::parse($invoice->issue_date)->format('H:i:s') }}

                                            </p>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                            <p>
                                                <strong>Due Date</strong><br>
                                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') }}<br>
                                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('H:i:s') }}
                                            </p>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                            <p>
                                                <strong>{{__('Customer')}}</strong><br>
                                                {{ $invoice->customer->name }}
                                            </p>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                            <p>
                                                <strong>{{__('Email')}}</strong><br>
                                                {{ $invoice->customer->email ?? '-' }}
                                            </p>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                            <p>
                                                <strong>{{__('Contact')}}</strong><br>
                                                {{ $invoice->customer->contact ?? '-' }}
                                            </p>
                                        </div>

                                        <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                            <p>
                                                <strong>{{__('By User')}}</strong><br>
                                                {{ $invoice->user_id != null ? $invoice->user->name : '-' }}
                                            </p>
                                        </div>

                                        <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                            <p>
                                                <strong>{{__('Branch')}}</strong><br>
                                                {{ App\Models\Employee::where('user_id', $invoice->user_id)->first()->branch->name ?? '-' }}
                                            </p>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                                            <p>
                                                <strong>{{__('Department')}}</strong><br>
                                                {{ App\Models\Employee::where('user_id', $invoice->user_id)->first()->department->name ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    @if (isset($customer->id))
                                    <h5> <strong>{{__('Customer Name')}} :</strong> {{$customer->name}}</h5>
                                    <h5> <strong>{{__('Customer email')}} :</strong> {{$customer->email}}</h5>
                                    <h5> <strong>{{__('Customer contact')}} :</strong> {{$customer->contact}}</h5>
                                    <h5> <strong>{{__('Customer ID')}} :</strong> {{$customer->id}}</h5>
                                    @endif
                                </div>
                                <hr>
                                <div class="col">
                                    <small class="font-style">
                                        <strong>{{ __('Billed To') }} :</strong><br>
                                        @if(!empty($invoice->address))
                                        @php
                                            $address_billing = json_decode($invoice->address,true);
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
                                                {{!empty($customer->billing_name)?$customer->billing_name:''}}<br>
                                                {{!empty($customer->billing_address)?$customer->billing_address:''}}<br>
                                                {{!empty($customer->billing_city)?$customer->billing_city:'' .', '}}<br>
                                                {{!empty($customer->billing_state)?$customer->billing_state:''.', '}},
                                                {{!empty($customer->billing_zip)?$customer->billing_zip:''}}<br>
                                                {{!empty($customer->billing_country)?$customer->billing_country:''}}<br>
                                                {{!empty($customer->billing_phone)?$customer->billing_phone:''}}<br>

                                                @if($settings['vat_gst_number_switch'] == 'on')
                                                    <strong>{{__('Tax Number ')}} : </strong>{{!empty($customer->tax_number)?$customer->tax_number:''}}
                                                @endif
                                            @endif
                                        @endif

                                    </small>
                                </div>

                                @if (App\Models\Utility::getValByName('shipping_display') == 'on')
                                    <div class="col ">
                                        <small>
                                            <strong>{{ __('Shipped To') }} :</strong><br>
                                            @if(!empty($invoice->address))
                                            @php
                                                $address_billing = json_decode($invoice->address,true);
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
                                                    {{!empty($customer->shipping_name)?$customer->shipping_name:''}}<br>
                                                    {{!empty($customer->shipping_address)?$customer->shipping_address:''}}<br>
                                                    {{!empty($customer->shipping_city)?$customer->shipping_city:'' . ', '}}<br>
                                                    {{!empty($customer->shipping_state)?$customer->shipping_state:'' .', '}},
                                                    {{!empty($customer->shipping_zip)?$customer->shipping_zip:''}}<br>
                                                    {{!empty($customer->shipping_country)?$customer->shipping_country:''}}<br>
                                                    {{!empty($customer->shipping_phone)?$customer->shipping_phone:''}}<br>
                                                @endif
                                            @endif
                                        </small>
                                    </div>
                                @endif
                                <div class="col">
                                    <div class="float-end mt-3">
                                        @if (!empty($settings['sellerName']) || !empty($settings['vatRegistrationNumber']))
                                            @php
                                                echo $qrCode;
                                            @endphp
                                        @else
                                        {!! DNS2D::getBarcodeHTML(
                                            route('invoice.link.copy', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)),
                                            'QRCODE',
                                            2,
                                            2,
                                        ) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <small>
                                        <strong>{{ __('Status') }} :</strong><br>
                                        @if ($invoice->status == 0)
                                            <span
                                                class="badge bg-primary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 1)
                                            <span
                                                class="badge bg-warning">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 2)
                                            <span
                                                class="badge bg-danger">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 3)
                                            <span
                                                class="badge bg-info">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 4)
                                            <span
                                                class="badge bg-primary">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @endif
                                    </small>
                                </div>

                                @if (!empty($customFields) && count($invoice->customField) > 0)
                                    @foreach ($customFields as $field)
                                        <div class="col text-md-right">
                                            <small>
                                                <strong>{{ $field->name }} :</strong><br>
                                                {{ !empty($invoice->customField) ? $invoice->customField[$field->id] : '-' }}
                                                <br><br>
                                            </small>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5 class="d-inline-block mb-5">Credit Note Summary</h5>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-dark"> Customer</th>
                                    <th class="text-dark"> Date</th>
                                    <th class="text-dark"> Amount</th>
                                    <th class="text-dark"> Description</th>
                                    <th class="text-dark">  Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @if(!empty($invoice->creditNote))
                                    @foreach ($invoice->creditNote as $creditNote)
                                            <tr>
                                                <td class="Id">
                                                    
                                                    <a href="{{ route('creditNote.show',\Crypt::encrypt($creditNote->invoice)) }}" class="btn btn-outline-primary">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                                </td>
                                                <td>{{ (!empty($invoice->customer)?$invoice->customer->name:'-') }}</td>
                                                <td>{{ Auth::user()->dateFormat($creditNote->date) }}</td>
                                                <td>{{ Auth::user()->priceFormat($creditNote->amount) }}</td>
                                                <td>{{!empty($creditNote->description)?$creditNote->description:'-'}}</td>
                                                <td>
                                                    @can('edit credit note')
                                                        <div class="action-btn bg-primary ms-2">
                                                            <a data-url="{{ route('invoice.edit.credit.note',[$creditNote->id,$creditNote->id]) }}" data-ajax-popup="true" data-title="{{__('Edit Credit Note')}}" href="#" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('edit credit note')
                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => array('invoice.delete.credit.note', $creditNote->id,$creditNote->id),'class'=>'delete-form-btn','id'=>'delete-form-'.$creditNote->id]) !!}
                                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$creditNote->id}}').submit();">
                                                                        <i class="ti ti-trash text-white"></i>
                                                                    </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="dash-container">
        <div class="dash-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="page-header-title">
                                <h4 class="m-b-10"> Invoice Detail
                                </h4>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="http://192.168.1.195/thefuture-erp/account-dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="http://192.168.1.195/thefuture-erp/invoice">Invoice</a>
                                </li>
                                <li class="breadcrumb-item">#INVO00002</li>
                            </ul>
                        </div>
                        <div class="col action-btn-col">
                        </div>
                    </div>
                </div>
            </div>

       



            <!-- [ Main Content ] end -->
        </div>
    </div> --}}



@endsection
