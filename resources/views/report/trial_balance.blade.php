@extends('layouts.admin')
@section('page-title')
    {{ __('Trial Balance') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Trial Balance') }}</li>
@endsection

@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        // var filename = $('#filename').val();

        function saveAsPDF() {
            $.ajax({
                url: '/userinfo/img',
                method: 'GET',
                success: function(response) {
                    var logo_image = '<img src="' + response.logo + '" alt="Company Logo" >';
                    var settings_data = response.settings;
                    var printableArea = $('#printableArea');
                    var topHeader = $('<div class="top-header row" style="direction: ltr"></div>');
                    topHeader.append('<div class="col" style="width: 100%;max-width: 115px;">' + settings_data + '</div>');
                    topHeader.append('<div class="col" style="text-align: right;">' + logo_image + '</div>');
                    printableArea.prepend(topHeader);
                    var printContents = document.getElementById('printableArea').innerHTML;
                    var originalContents = document.body.innerHTML;
                    document.body.innerHTML = printContents;
                    $('.card').removeClass();
                    $('.table-responsive').removeClass();
                    setTimeout(() => {
                        window.print();
                        document.body.innerHTML = originalContents;
                        $('.top-header').empty();
                        location.reload();
                    }, "1000");
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Log the error to the console
                    alert('Failed to fetch image data.');
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            $("#filter").click(function() {
                $("#show_filter").toggle();
            });
        });
    </script>
@endpush

@section('action-btn')
    <div class="float-end">
        <a href="#" onclick="saveAsPDF()" class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip"
            title="{{ __('Print') }}" data-original-title="{{ __('Print') }}"><i class="ti ti-printer"></i></a>
    </div>

    <div class="float-end me-2">
        {{ Form::open(['route' => ['trial.balance.export']]) }}
        <input type="hidden" name="start_date" class="start_date">
        <input type="hidden" name="end_date" class="end_date">
        <button type="submit" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Export') }}"
            data-original-title="{{ __('Export') }}"><i class="ti ti-file-export"></i></button>
        {{ Form::close() }}
    </div>
    <div class="float-end me-2" id="filter">
        <button id="filter" class="btn btn-sm btn-primary"><i class="ti ti-filter"></i></button>
    </div>
@endsection

@section('content')
@php
function renderTotal_debit($parentId, $subAccounts)
{
    $subtotal = 0;
    foreach ($subAccounts as $subAccount) {
        if ($subAccount['account'] == $parentId) {
            $start_date = $_GET['start_date'] ?? null;
            $end_date = $_GET['end_date'] ?? null;
            $totalBalance = App\Models\TransactionLines::where('account_id', $subAccount['id'])->first(['debit']);
            $subtotal += $totalBalance ? (float) $totalBalance->debit : 0;

        }
    }
    return $subtotal;
}
function renderTotal_credit($parentId, $subAccounts)
{
    $subtotal = 0;
    foreach ($subAccounts as $subAccount) {
        if ($subAccount['account'] == $parentId) {
            $start_date = $_GET['start_date'] ?? null;
            $end_date = $_GET['end_date'] ?? null;
            $totalBalance = App\Models\TransactionLines::where('account_id', $subAccount['id'])->first(['credit']);
            $subtotal += $totalBalance ? (float) $totalBalance->credit : 0;

        }
    }
    return $subtotal;
}


function renderSubAccounts($parentId, $subAccounts, $level)
{
    $html = '';
    foreach ($subAccounts as $subAccount) {
        if ($subAccount['account'] == $parentId) {

            // جلب إجمالي المدين والدائن
            $totalBalance_debit = App\Models\TransactionLines::where('account_id', $subAccount['id'])->first(['debit']);
            $debitValue = $totalBalance_debit ? $totalBalance_debit->debit : 0;

            $totalBalance_credit = App\Models\TransactionLines::where('account_id', $subAccount['id'])->first(['credit']);
            $creditValue = $totalBalance_credit ? $totalBalance_credit->credit : 0;

            $indent = str_repeat('&nbsp;', $level * 5);

            $html .= '<tr>';
            // NAME
            $html .= '<td style="padding: 5px">';
            $html .= $indent . $subAccount['code'] . ' - ';
            $html .= '<a href="' . route('report.ledger', $subAccount['id']) . '?account=' . $subAccount['id'] . '">' . __($subAccount['name']) . '</a>';
            $html .= '</td>';

            // DEBIT COLUMN
            $html .= '<td class="text-end" style="padding: 5px">';
            $html .= \Auth::user()->priceFormat($debitValue);
            $html .= '</td>';

            // CREDIT COLUMN
            $html .= '<td class="text-end" style="padding: 5px">';
            $html .= \Auth::user()->priceFormat($creditValue);
            $html .= '</td>';

            $html .= '</tr>';

            $html .= renderSubAccounts($subAccount['id'], $subAccounts, $level + 1);
        }
    }
    return $html;
}

    @endphp
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mt-2" id="multiCollapseExample1">
                <div class="card" id="show_filter" style="display:none;">
                    <div class="card-body">
                        {{ Form::open(['route' => ['trial.balance'], 'method' => 'GET', 'id' => 'report_trial_balance']) }}
                        <div class="col-xl-12">

                            <div class="row justify-content-between">
                                <div class="col-xl-3 mt-4">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons"
                                        aria-label="Basic radio toggle button group">
                                        <label class="btn btn-primary month-label">
                                            <a href="{{ route('trial.balance', ['collapse']) }}"
                                                class="text-white" id="collapse"> {{ __('Collapse') }} </a>
                                        </label>

                                        <label class="btn btn-primary year-label active">
                                            <a href="{{ route('trial.balance', ['expand']) }}"
                                                class="text-white"> {{ __('Expand') }} </a>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xl-9">
                                    <div class="row justify-content-end align-items-center">
                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                                {{ Form::date('start_date', $filter['startDateRange'], ['class' => 'startDate form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                                {{ Form::date('end_date', $filter['endDateRange'], ['class' => 'endDate form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-auto mt-4">
                                            <a href="#" class="btn btn-sm btn-primary"
                                                onclick="document.getElementById('report_trial_balance').submit(); return false;"
                                                data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                                data-original-title="{{ __('apply') }}">
                                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                            </a>

                                            <a href="{{ route('trial.balance') }}"
                                                class="btn btn-sm btn-danger " data-bs-toggle="tooltip"
                                                title="{{ __('Reset') }}" data-original-title="{{ __('Reset') }}">
                                                <span class="btn-inner--icon"><i
                                                        class="ti ti-trash-off text-white-off "></i></span>
                                            </a>

                                        </div>
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

    @php
        $authUser = \Auth::user()->creatorId();
        $user = App\Models\User::find($authUser);
    @endphp

    <div class="row justify-content-center" id="printableArea">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="account-main-title mb-5 text-center">
                        <h5>{!! __('Trial Balance') .
                            '<br>' .
                            $user->name .
                            '<br>' .
                            __(' as of ') .
                            $filter['startDateRange'] .
                            __(' to ') .
                            $filter['endDateRange'] !!}</h5>
                    </div>
                    @php
                    $totalAmount = 0;
                @endphp
                {{-- # Here will be accounts --}}
                @php
                    $groupedChartAccounts = array_intersect_key($groupedChartAccounts, array_flip(['Assets', 'Liability', 'Equity','Revenue']));
                @endphp
                {{-- @dd($groupedChartAccounts) --}}
                {{-- @dd($types) --}}
                <table class="table table-bordered table-hover">
                    <tbody>
                        @foreach ($groupedChartAccounts as $typeName => $accounts)
                            <tr class="table-secondary">
                                <td style="font-weight: 900; padding: 5px">
                                    @php
                                        $type = $types
                                            ->where('name', $typeName)
                                            ->where('created_by', \Auth::user()->creatorId())
                                            ->first();
                                    @endphp
                                    {{ $type->code . ' - ' . __($type->name) }}
                                </td>
                                <td style="font-weight: 900; padding: 5px" class="text-center">
                                    {{ __('Debit') }}
                                </td>
                                <td style="font-weight: 900; padding: 5px" class="text-center">
                                    {{ __('Credit') }}
                                </td>
                            </tr>

                            @php

                                $total_amount_of_type_debit = 0;
                                $total_amount_of_type_credit = 0;
                            @endphp

                            @foreach ($subtypes as $subType)
                                @if ($subType->type == $type->id)
                                    <tr>
                                        <td style="font-weight: 500; padding: 5px" colspan="3">
                                            {!! '&nbsp;&nbsp;' . $subType->code . ' - ' . __($subType->name) !!}
                                        </td>
                                    </tr>

                                    @php
                                        $subtotalbalance_debit = 0;
                                        $subtotalbalance_credit = 0;
                                    @endphp

                                    @foreach ($accounts as $account)
                                        @if ($subType->name == $account['sub_type_name'])
                                            @php

                                            $totalBalance_debit = App\Models\TransactionLines::where('account_id', $account['id'])->first(['debit']);
                                            $debitValue = $totalBalance_debit ? $totalBalance_debit->debit : 0;

                                            $totalBalance_credit = App\Models\TransactionLines::where('account_id', $account['id'])->first(['credit']);
                                            $creditValue = $totalBalance_credit ? $totalBalance_credit->credit : 0;

                                            $subtotalbalance_debit += (float) $debitValue ;
                                            $subtotalbalance_debit += renderTotal_debit($account['id'], $subAccounts, 3);

                                            $subtotalbalance_credit += (float) $creditValue ;
                                            $subtotalbalance_credit += renderTotal_credit($account['id'], $subAccounts, 3);

                                            @endphp

                                            <tr>
                                                <td style="padding: 5px">
                                                    {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                    - <a href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
                                                        {{ __($account['name']) }}
                                                    </a>
                                                </td>

                                                <td class="text-end" style="padding: 5px">
                                                   {{ $debitValue  ? \Auth::user()->priceFormat($debitValue) : \Auth::user()->priceFormat(0) }}
                                                </td>
                                                <td class="text-end" style="padding: 5px">
                                                   {{ $creditValue  ? \Auth::user()->priceFormat($creditValue) : \Auth::user()->priceFormat(0) }}
                                                </td>

                                            </tr>
                                            {!! renderSubAccounts($account['id'], $subAccounts, 3) !!}
                                        @endif
                                    @endforeach

                                    @php
                                        $total_amount_of_type_debit += $subtotalbalance_debit;
                                        $total_amount_of_type_credit += $subtotalbalance_credit;
                                    @endphp

                                    <tr class="text-danger">
                                        <td style="font-weight: 500; padding: 5px;">
                                            {{ __('Total Of') . ' ( '  . __($subType->name) . ' ) ' . ' = ' }}
                                        </td>
                                        <td class="text-end" style="font-weight: 500; padding: 5px;">
                                            {{ $subtotalbalance_debit ? \Auth::user()->priceFormat($subtotalbalance_debit) : \Auth::user()->priceFormat(0) }}
                                        </td>
                                        <td class="text-end" style="font-weight: 500; padding: 5px;">
                                            {{ $subtotalbalance_credit ? \Auth::user()->priceFormat($subtotalbalance_credit) : \Auth::user()->priceFormat(0) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            <tr class="text-primary">
                                <td style="font-weight: 500; padding: 5px;">
                                    {!!  '&nbsp;&nbsp;&nbsp; ⇄ '. __('Total Of') . ' ( '  . __($type->name) . ' ) ' . ' = ' !!}
                                </td>
                                <td class="text-end" style="font-weight: 500; padding: 5px;">
                                    {{ \Auth::user()->priceFormat($total_amount_of_type_debit) }}
                                </td>
                                <td class="text-end" style="font-weight: 500; padding: 5px;">
                                    {{ \Auth::user()->priceFormat($total_amount_of_type_credit) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

            </div>
        </div>
    </div>
@endsection


@push('script-page')
    <script>
        $(document).ready(function() {
            callback();

            function callback() {
                var start_date = $(".startDate").val();
                var end_date = $(".endDate").val();

                $('.start_date').val(start_date);
                $('.end_date').val(end_date);

            }
        });
    </script>
@endpush
