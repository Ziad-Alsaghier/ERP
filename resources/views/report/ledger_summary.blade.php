@extends('layouts.admin')
@section('page-title')
{{ __('Account Statement') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Account Statement') }}</li>
@endsection
@push('script-page')
<script>
    select2();
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var accountTypeSelect = document.getElementById('account_type');
        var customerDiv = document.getElementById('customer_div');
        var vendorDiv = document.getElementById('vendor_div');
        var employeeDiv = document.getElementById('employee_div');

        accountTypeSelect.addEventListener('change', function () {
            var selectedValue = this.value;

            // إخفاء كل القوائم
            customerDiv.style.display = 'none';
            vendorDiv.style.display = 'none';
            employeeDiv.style.display = 'none';

            // عرض القائمة المحددة
            if (selectedValue === 'customer') {
                customerDiv.style.display = 'block';
            } else if (selectedValue === 'vendor') {
                vendorDiv.style.display = 'block';
            } else if (selectedValue === 'employee') {
                employeeDiv.style.display = 'block';
            }
        });
    });
</script>

@endpush

@section('action-btn')
<div class="float-end">
    <a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button"
        aria-expanded="false" aria-controls="multiCollapseExample1" data-bs-toggle="tooltip" title="{{__('Filter')}}">
        <i class="ti ti-filter"></i>
    </a>

    <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()" data-bs-toggle="tooltip"
        title="{{ __('Download') }}" data-original-title="{{ __('Download') }}">
        <span class="btn-inner--icon"><i class="ti ti-download"></i></span>
    </a>
</div>
@endsection

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
@section('content')

<style>
    .symbol {
        font-family: 'SaudiRiyalSymbol', sans-serif;
        font-size: inherit;
        margin: 20px auto;
        color: inherit;
        font-weight: bold;
    }

    .usage-rule {
        display: flex;
        align-items: center;
    }

    .usage-rule i {
        color: green;
    }

    @media print {
        .usage-rule {
            display: flex;
            align-items: center;
        }

        img {
            width: 100%;
            max-width: 115px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;

        }

        ;

        .section_print {
            display: block;
        }

        .section-detailes-filter {
            display: block;

        }

        .custom-width {
            column-width: 150px;
        }

        @page {
            size: auto;
            margin: 0mm;
        }
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="mt-2 " id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['report.ledger'], 'method' => 'GET', 'id' => 'report_ledger']) }}

                    <div class="row align-items-center justify-content-end">
                        <div class="col-xl-10">
                            <div class="row">

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}
                                        <select name="account" class="form-control select2" id='account'
                                            required="required" style="direction: ltr; text-align:left;">
                                            <option value="" class="ms-5" selected disabled>{{__(' --- Select Account
                                                ---')}}</option>

                                            @foreach ($accounts_type as $type)
                                            <optgroup label="{{ __($type['name']) }}">
                                                @foreach ($accounts as $chartAccount)
                                                @if ($type['id'] == $chartAccount['type'])
                                                <option value="{{ $chartAccount['id'] }}" class="subAccount" {{
                                                    isset($_GET['account']) && $chartAccount['id']==$_GET['account']
                                                    ? 'selected' : '' }}>
                                                    {!! $chartAccount['code'] . ' - ' . __($chartAccount['name']) !!}
                                                </option>

                                                {{-- استدعاء دالة لتكرار عرض الحسابات الفرعية --}}
                                                @include('partials.sub-accounts', ['subAccounts' => $subAccounts,
                                                'parent_id' => $chartAccount['id'], 'level' => 1])
                                                @endif
                                                @endforeach
                                            </optgroup>
                                            @endforeach


                                        </select>

                                    </div>
                                </div>

                                {{-- -------filter----- --}}
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('account_type', __('Select Filter Type'), ['class' =>
                                        'form-label']) }}
                                        <select name="account_type" class="form-control select2" id="account_type"
                                            required="required">
                                            <option value="" selected disabled>{{ __('Select Filter Type') }}</option>
                                            <option value="customer">{{ __('Customer') }}</option>
                                            <option value="vendor">{{ __('Vendor') }}</option>
                                            <option value="employee">{{ __('Employee') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12" id="customer_div"
                                    style="display:none;">
                                    <div class="btn-box">
                                        {{ Form::label('Customer', __('Customer'), ['class' => 'form-label']) }}
                                        <select name="Customer" class="form-control select2" id='Customer'>
                                            <option value="" class="ms-5" selected disabled>{{ __('Select Name') }}
                                            </option>
                                            @foreach ($account_customer as $account)
                                            <option value="{{ $account->id }}" class="ms-5"> {{ __($account->name) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12" id="vendor_div"
                                    style="display:none;">
                                    <div class="btn-box">
                                        {{ Form::label('Vendor', __('Vendors'), ['class' => 'form-label']) }}
                                        <select name="Vendor" class="form-control select2" id='Vendor'>
                                            <option value="" class="ms-5" selected disabled>{{ __('Select Name') }}
                                            </option>
                                            @foreach ($account_Vender as $account)
                                            <option value="{{ $account->id }}" class="ms-5"> {{ __($account->name) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12" id="employee_div"
                                    style="display:none;">
                                    <div class="btn-box">
                                        {{ Form::label('Employee', __('Employee'), ['class' => 'form-label']) }}
                                        <select name="Employee" class="form-control select2" id='Employee'>
                                            <option value="" class="ms-5" selected disabled>{{ __('Select Name') }}
                                            </option>
                                            @foreach ($account_Employee as $account)
                                            <option value="{{ $account->id }}" class="ms-5"> {{ __($account->name) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                {{-- ------filter end------ --}}

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                        {{ Form::date('start_date', $filter['startDateRange'], ['class' => 'month-btn
                                        form-control']) }}
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                        {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                        {{ Form::date('end_date', $filter['endDateRange'], ['class' => 'month-btn
                                        form-control']) }}
                                    </div>
                                </div>



                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="row">
                                <div class="col-auto mt-4">
                                    <a href="#" class="btn btn-sm btn-primary"
                                        onclick="document.getElementById('report_ledger').submit(); return false;"
                                        data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                        data-original-title="{{ __('apply') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                    </a>
                                    <a href="{{ route('report.ledger') }}" class="btn btn-sm btn-danger "
                                        data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                        data-original-title="{{ __('Reset') }}">
                                        <span class="btn-inner--icon"><i
                                                class="ti ti-trash-off text-white-off "></i></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>



<div id="printableArea">
    <div class="row mt-2">
        <div class="row mb-4">
            <div class="col-12 mb-5">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="table-responsive" style="width: 100%;">
                            @php

                            $chartDatas = App\Models\Utility::getAccountData($account['id'], $filter['startDateRange'],
                            $filter['endDateRange'],$filter['Customer']);
                            @endphp
                            {{-- @dd($account->name) --}}
                            <table class="table datatable" data-customer="{{ __($filter['Customer']) }}"
                                data-start-date="{{ $filter['startDateRange'] }}"
                                data-end-date="{{ $filter['endDateRange'] }}">

                                <thead>
                                    <tr>
                                        <th> {{ __('#') }}</th>
                                        <th> {{ __('Transaction Date') }}</th>
                                        <th> {{ __('Documents') }}</th>
                                        <th> {{ __('Document Number') }}</th>
                                        <th> {{ __('Description') }}</th>
                                        <th> {{ __('Debit') }}</th>
                                        <th> {{ __('Credit') }}</th>
                                        <th> {{ __('Balance') }}</th>

                                    </tr>
                                </thead>
                                @php
                                use Illuminate\Support\Collection;
                                $balance = 0;
                                $totalDebit = 0;
                                $totalCredit = 0;

                                $accountArrays = collect(); // استخدام مجموعة Laravel

                                foreach ($chart_accounts as $key => $account) {

                                $chartDatas = App\Models\Utility::getAccountData($account['id'],
                                $filter['startDateRange'], $filter['endDateRange'],$filter['Customer']);
                                $chartDatas = $chartDatas->toArray();
                                if (!empty($chartDatas)) {
                                $accountArrays = $accountArrays->merge($chartDatas); // دمج البيانات في المجموعة
                                }
                                }

                                // فرز البيانات حسب 'created_at'
                                $accountArrays = $accountArrays->sortBy('created_at');
                                $accountArrays = $accountArrays->toArray();

                                @endphp

                                <tbody style="columns-widht:20px;">
                                    @foreach ($accountArrays as $account)
                                    <div class="data" data-account="{{ json_encode($account) }}" data-filter={{
                                        json_encode($filter) }} data-type-select={{ __(ucfirst($filter['type'])) }}>

                                        @php
                                        $debit = 0 ;
                                        $credit = 0;
                                        if($account->debit != 0){
                                        $debit += $account->debit;
                                        }


                                        @endphp
                                        @if ($account->reference == 'Bank Account')
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $account->date }}</td>
                                            <td class="custom-width">{{ $account->reference}}</td>
                                            <td>{{ $account->holder_name }}</td>
                                            {{-- <td>{{ __('Opening Balance')}}</td> --}}
                                            <td class="text-center" style="white-space: normal;">{{
                                                __($account->account_name)}}</td>
                                            <td>{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                            @php
                                            $total = $account->credit - $account->debit;
                                            $totalDebit += $account->debit ;
                                            $totalCredit += $account->credit;
                                            $balance += $total;
                                            @endphp
                                            <td>{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                            <td><span class="{{ $balance < 0 ? 'text-danger' : 'text-success'}}">{{
                                                    \Auth::user()->priceFormat($balance) }}</span></td>
                                        </tr>
                                        @endif

                                        @if ($account->reference == 'Invoice')

                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $account->date }}</td>

                                            <td class="custom-width">{{ __($account->reference) }}</td>
                                            <td>{{ \Auth::user()->invoiceNumberFormat($account->ids) }}</td>
                                            {{-- <td>{{ $account->user_name }}</td> --}}

                                            <td class="text-center" style="white-space: normal;">{{
                                                __($account->account_name)}}</td>
                                            <td>{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                            @php
                                            $total = $account->debit + $account->credit;
                                            $totalDebit += $account->debit ;
                                            $totalCredit += $account->credit;
                                            $balance += $total;
                                            @endphp
                                            <td>{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                            <td style=""> <span
                                                    class="{{ $balance < 0 ? 'text-danger' : 'text-success'}}">{{
                                                    \Auth::user()->priceFormat($balance) }}</span></td>
                                        </tr>
                                        @endif

                                        @if ($account->reference == 'Invoice Payment')

                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $account->date }}</td>
                                            <td class="custom-width">{{ __($account->reference) }}</td>
                                            <td>{{ \Auth::user()->invoiceNumberFormat($account->ids) }} </td>
                                            {{-- <td>{{ $account->user_name }}</td> --}}

                                            {{-- [{{ __(' Manually Payment') }}] --}}
                                            <td class="text-center" style="white-space: normal; max-width: 10px;">{{
                                                __($account->account_name) }}</td>
                                            <td>{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                            @php
                                            $total = $account->debit + $account->credit;
                                            $totalDebit += $account->debit ;
                                            $totalCredit += $account->credit;
                                            if($account->credit > $account->debit){
                                            $balance += $total;
                                            }elseif($account->credit < $account->debit){
                                                $balance -= $total;
                                                }
                                                @endphp
                                                <td>{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                                <td><span class="{{ $balance < 0 ? 'text-danger' : 'text-success'}}">{{
                                                        \Auth::user()->priceFormat($balance) }}</span></td>
                                        </tr>
                                        @endif

                                        @if ($account->reference == 'Revenue')

                                        <tr>
                                            <td>{{ $account->date }}</td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ __($account->reference)}}</td>
                                            <td>{{ __(' Revenue') }}</td>
                                            {{-- <td>{{ $account->user_name }}</td> --}}
                                            <td class="text-center" style="white-space: normal;">{{
                                                __($account->account_name)}}</td>
                                            <td>{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                            @php
                                            $total = $account->debit + $account->credit;
                                            $totalDebit += $account->debit ;
                                            $totalCredit += $account->credit;
                                            $balance += $total;
                                            @endphp
                                            <td>{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                            <td><span class="{{ $balance < 0 ? 'text-danger' : 'text-success'}}">{{
                                                    \Auth::user()->priceFormat($balance) }}</span></td>
                                        </tr>
                                        @endif

                                        @if (
                                        $account->reference == 'Bill' ||
                                        $account->reference == 'Bill Account' ||
                                        $account->reference == 'Expense' ||
                                        $account->reference == 'Expense Account')
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $account->date }}</td>
                                            <td class="custom-width">{{ __($account->reference) }}</td>
                                            @if ($account->reference == 'Bill' || $account->reference == 'Bill Account')
                                            <td>{{ \Auth::user()->billNumberFormat($account->ids) }} </td>
                                            @else
                                            <td>{{ \Auth::user()->expenseNumberFormat($account->ids) }} </td>
                                            @endif
                                            {{-- <td>{{ $account->user_name }}</td> --}}
                                            <td class="text-center" style="white-space: normal;">{{
                                                __($account->account_name)}}</td>
                                            <td>{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                            @php
                                            $total = $account->debit - $account->credit;
                                            $totalDebit += $account->debit ;
                                            $totalCredit += $account->credit;
                                            $balance += $total;
                                            @endphp
                                            <td>{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                            <td><span class="{{ $balance < 0 ? 'text-danger' : 'text-success'}}">{{
                                                    \Auth::user()->priceFormat($balance) }}</span></td>
                                        </tr>
                                        @endif

                                        @if ($account->reference == 'Bill Payment' || $account->reference == 'Expense
                                        Payment')
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $account->date }}</td>
                                            <td class="custom-width">{{ __($account->reference) }}</td>
                                            @if ($account->reference == 'Bill Payment')
                                            <td>{{ \Auth::user()->billNumberFormat($account->ids) }} [{{ __(' Manually
                                                Payment') }}]</td>
                                            @else
                                            <td>{{ \Auth::user()->expenseNumberFormat($account->ids) }} [{{ __('
                                                Manually Payment') }}] </td>
                                            @endif
                                            {{-- <td>{{ $account->user_name }}</td> --}}
                                            <td class="text-center" style="white-space: normal;">{{
                                                __($account->account_name)}}</td>
                                            <td>{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                            @php
                                            $total = $account->debit + $account->credit;
                                            $totalDebit += $account->debit ;
                                            $totalCredit += $account->credit;
                                            $balance += $total;
                                            @endphp
                                            <td>{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                            <td><span class="{{ $balance < 0 ? 'text-danger' : 'text-success'}}">{{
                                                    \Auth::user()->priceFormat($balance) }}</span></td>
                                        </tr>
                                        @endif

                                        @if ($account->reference == 'Payment')

                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $account->date }}</td>
                                            <td class="custom-width">{{ __($account->reference) }}</td>
                                            <td>{{ __('Payment') }}</td>
                                            {{-- <td>{{ $account->user_name }}</td> --}}
                                            <td class="text-center" style="white-space: normal;">{{
                                                __($account->account_name)}}</td>
                                            <td>{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                            @php
                                            $total = $account->debit + $account->credit;
                                            $totalDebit += $account->debit ;
                                            $totalCredit += $account->credit;
                                            $balance -= $total;
                                            @endphp
                                            <td>{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                            <td><span class="{{ $balance < 0 ? 'text-danger' : 'text-success'}}">{{
                                                    \Auth::user()->priceFormat($balance) }}</span></td>
                                        </tr>
                                        @endif

                                        @if ($account->reference == 'Journal')

                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $account->date }}</td>
                                            <td style="white-space: normal;">{{ $account->reference }}</td>
                                            <td>{{ Auth::user()->journalNumberFormat($account->reference_id) }}</td>
                                            <td>
                                                {{ '-' }}
                                            </td>
                                            <td class=" text-center" style="white-space: normal;">{{
                                                __($account->account_name)}}</td>
                                            <td>{{ \Auth::user()->priceFormat($account->debit) }}</td>
                                            <td>{{ \Auth::user()->priceFormat($account->credit) }}</td>
                                            <td><span class="{{ $balance < 0 ? 'text-danger' : 'text-success'}}">{{
                                                    \Auth::user()->priceFormat($balance) }}</span></td>
                                            @php
                                            $total = $account->credit - $account->debit;
                                            $totalDebit += $account->debit ;
                                            $totalCredit += $account->credit;
                                            $balance += $total;
                                            @endphp
                                        </tr>
                                        @endif



                                        {{-- @endforeach --}}
                                        @endforeach

                                    <tr style="background-color: aliceblue;">
                                        <td></td>
                                        <td><strong>{{ __('Total') }}</strong></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>

                                            <td>{{ \Auth::user()->priceFormat($totalDebit) }}</td>
                                            <td>{{ \Auth::user()->priceFormat($totalCredit) }}</td>
                                            <td>
                                                <strong class="{{ $balance < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ \Auth::user()->priceFormat($balance) }}
                                                </strong>
                                            </td>
                                    </tr>
                                        <tr style="background-color: aliceblue;">

                                            <td></td>
                                            <td><strong>{{ __('Closing Balance') }}</strong></td>

                                            <td class="text-center "
                                                style="white-space: normal; max-width: 100%; display: flex; justify-content: center; align-items: center; align-items: center;">
                                                <strong>
                                                    {!!
                                                    \Auth::user()->convertFormattedAmountToArabic($balance,app()->getLocale())
                                                    !!}
                                                </strong>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <strong class="{{ $balance < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ $balance < 0 ? __("Credit") . " " . \Auth::user()->
                                                        priceFormat($balance) : __('Debit'). " " .
                                                        \Auth::user()->priceFormat($balance) }}
                                                </strong>
                                            </td>


                                        </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
