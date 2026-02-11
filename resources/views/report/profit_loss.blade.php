@extends('layouts.admin')
@section('page-title')
    {{ __('Profit & Loss') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Profit & Loss') }}</li>
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
        {{ Form::open(['route' => ['profit.loss.export']]) }}
        <input type="hidden" name="start_date" class="start_date">
        <input type="hidden" name="end_date" class="end_date">
        <button type="submit" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Export') }}"
            data-original-title="{{ __('Export') }}"><i class="ti ti-file-export"></i></button>
        {{ Form::close() }}
    </div>


    <div class="float-end me-2" id="filter">
        <button id="filter" class="btn btn-sm btn-primary"><i class="ti ti-filter"></i></button>
    </div>

    <div class="float-end me-2">
        <a href="{{ route('report.profit.loss', 'horizontal') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
            title="{{ __('Horizontal View') }}" data-original-title="{{ __('Horizontal View') }}"><i
                class="ti ti-separator-vertical"></i></a>
    </div>
@endsection



@section('content')
@php
function renderTotal($parentId, $subAccounts)
{
    $subtotal = 0;

    foreach ($subAccounts as $subAccount) {
        if ($subAccount['account'] == $parentId) {
            $start_date = $_GET['start_date'] ?? null;
            $end_date = $_GET['end_date'] ?? null;

            // احصل على الرصيد للحساب الفرعي
            $totalBalance = isset($start_date) && isset($end_date)
                ? App\Models\Utility::getAccountBalance($subAccount['id'], $start_date, $end_date)
                : App\Models\Utility::getAccountBalance($subAccount['id']);

            // إضافة الرصيد فقط مرة واحدة
            $subtotal += (float) $totalBalance;

            // استدعاء الدالة فقط للحسابات الفرعية
            $subtotal += (float) renderTotal($subAccount['id'], $subAccounts);
        }
    }

    return $subtotal;
}


function renderSubAccounts($parentId, $subAccounts, $level)
{
    $html = '';
    foreach ($subAccounts as $subAccount) {
        if ($subAccount['account'] == $parentId) {
            if (isset($_GET['start_date'])) {
                $start_date = $_GET['start_date'];
            }
            if (isset($_GET['end_date'])) {
                $end_date = $_GET['end_date'];
            }
            $balance = 0;
            $totalDebit = 0;
            $totalCredit = 0;
            if (isset($start_date) && isset($end_date)) {
                $totalBalance = App\Models\Utility::getAccountBalance(
                    $subAccount['id'],
                    $start_date,
                    $end_date,
                );
            } else {
                $totalBalance = App\Models\Utility::getAccountBalance($subAccount['id']);
            }

            $indent = str_repeat('&nbsp;', $level * 5);
            $html .= '<tr>';
            // NAME
            $html .= '<td style="padding: 5px">';
            $html .=
                $indent .
                $subAccount['code'] .
                ' - ' .
                '<a href="' .
                route('report.ledger', $subAccount['id']) .
                '?account=' .
                $subAccount['id'] .
                '">' .
                __($subAccount['name']) .
                '</a>';
            $html .= '</td>';
            // BALANCE
            $html .= '<td class="text-end" style="padding: 5px">';
            !empty($totalBalance)
                ? ($html .= \Auth::user()->priceFormat($totalBalance))
                : ($html .= \Auth::user()->priceFormat(0));
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
                        {{ Form::open(['route' => ['report.profit.loss'], 'method' => 'GET', 'id' => 'report_profit_loss']) }}
                        <div class="col-xl-12">

                            <div class="row justify-content-between">
                                <div class="col-xl-3 mt-4">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons"
                                        aria-label="Basic radio toggle button group">
                                        <label class="btn btn-primary month-label">
                                            <a href="{{ route('report.profit.loss', ['vertical', 'collapse']) }}"
                                                class="text-white" id="collapse"> {{ __('Collapse') }} </a>
                                        </label>

                                        <label class="btn btn-primary year-label active">
                                            <a href="{{ route('report.profit.loss', ['vertical', 'expand']) }}"
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
                                                onclick="document.getElementById('report_profit_loss').submit(); return false;"
                                                data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                                data-original-title="{{ __('apply') }}">
                                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                            </a>

                                            <a href="{{ route('report.profit.loss') }}" class="btn btn-sm btn-danger "
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
                            <h5>{!! __('Profit & Loss') .
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
                    $groupedChartAccounts = array_intersect_key($groupedChartAccounts, array_flip(['Revenue' ,'Expenses']));
                @endphp
                {{-- @dd($groupedChartAccounts) --}}
                {{-- @dd($types) --}}
                <table class="table table-bordered table-hover">
                    <tbody>
                        @foreach ($groupedChartAccounts as $typeName => $accounts)
                            <tr class="table-secondary">
                                <td style="font-weight: 900; padding: 5px" colspan="2">
                                    @php
                                        $type = $types
                                            ->where('name', $typeName)
                                            ->where('created_by', \Auth::user()->creatorId())
                                            ->first();
                                    @endphp
                                    {{ $type->code . ' - ' . __($type->name) }}
                                </td>
                            </tr>

                            @php
                                $total_amount_of_type = 0;
                            @endphp

                            @foreach ($subtypes as $subType)
                                @if ($subType->type == $type->id)
                                    <tr>
                                        <td style="font-weight: 500; padding: 5px" colspan="2">
                                            {!! '&nbsp;&nbsp;' . $subType->code . ' - ' . __($subType->name) !!}
                                        </td>
                                    </tr>

                                    @php
                                        $subtotalbalance = 0;
                                    @endphp

                                    @foreach ($accounts as $account)
                                        @if ($subType->name == $account['sub_type_name'])
                                            @php
                                                $totalBalance = App\Models\Utility::getAccountBalance(
                                                    $account['id'],
                                                    $filter['startDateRange'],
                                                    $filter['endDateRange']
                                                );

                                                $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                $subtotalbalance += (float) $totalBalance + (float) $subTotalForAccount;
                                            @endphp

                                            <tr>
                                                <td style="padding: 5px">
                                                    {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                    - <a href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
                                                        {{ __($account['name']) }}
                                                    </a>
                                                </td>
                                                <td class="text-end" style="padding: 5px">
                                                    {{ !empty($totalBalance) ? \Auth::user()->priceFormat($totalBalance) : \Auth::user()->priceFormat(0) }}
                                                </td>
                                            </tr>
                                            {!! renderSubAccounts($account['id'], $subAccounts, 3) !!}
                                        @endif
                                    @endforeach

                                    @php
                                        $total_amount_of_type += $subtotalbalance;
                                    @endphp

                                    <tr class="text-danger">
                                        <td style="font-weight: 500; padding: 5px;">
                                            {{ __('Total Of') . ' ( '  . __($subType->name) . ' ) ' . ' = ' }}
                                        </td>
                                        <td class="text-end" style="font-weight: 500; padding: 5px;">
                                            {{ \Auth::user()->priceFormat($subtotalbalance) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                            <tr class="text-primary">
                                <td style="font-weight: 500; padding: 5px;">
                                    {!!  '&nbsp;&nbsp;&nbsp; ⇄ '. __('Total Of') . ' ( '  . __($type->name) . ' ) ' . ' = ' !!}
                                </td>
                                <td class="text-end" style="font-weight: 500; padding: 5px;">
                                    {{ \Auth::user()->priceFormat($total_amount_of_type) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- # End of accounts --}}

                </div>
            </div>
        </div>
    </div>
@endsection
