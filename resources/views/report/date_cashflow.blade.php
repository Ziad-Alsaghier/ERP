@extends('layouts.admin')
@section('page-title')
    {{ __('Cash Flow') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Cash Flow') }}</li>
@endsection
@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        function saveAsPDF() {
            $.ajax({
                url: '/userinfo/img',
                method: 'GET',
                success: function(response) {
                    var logo_image = '<img src="' + response.logo + '" alt="Company Logo">';
                    var settings_data = response.settings;
                    var printableArea = $('#printableArea');
                    var topHeader = $('<div class="top-header row" style="direction: ltr"></div>');
                    topHeader.append('<div class="col" style="width: 100%;max-width: 115px;">' + settings_data +
                        '</div>');
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
@endpush

@section('action-btn')
    <div class="float-end">

        <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()" data-bs-toggle="tooltip"
            title="{{ __('Download') }}" data-original-title="{{ __('Download') }}">
            <span class="btn-inner--icon"><i class="ti ti-download"></i></span>
        </a>

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
                    $totalBalance = App\Models\TransactionLines::where('account_id', $subAccount['id'])->first([
                        'debit',
                    ]);
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
                    $totalBalance = App\Models\TransactionLines::where('account_id', $subAccount['id'])->first([
                        'credit',
                    ]);
                    $subtotal += $totalBalance ? (float) $totalBalance->credit : 0;
                }
            }
            return $subtotal;
        }
        function renderSubAccounts_c($parentId, $subAccounts, $level)
        {
            $html = '';
            foreach ($subAccounts as $subAccount) {
                if ($subAccount['account'] == $parentId) {
                    // جلب إجمالي المدين والدائن
                    $totalBalance_debit = App\Models\TransactionLines::where('account_id', $subAccount['id'])->first([
                        'debit',
                    ]);
                    $debitValue = $totalBalance_debit ? $totalBalance_debit->debit : 0;

                    $totalBalance_credit = App\Models\TransactionLines::where('account_id', $subAccount['id'])->first([
                        'credit',
                    ]);
                    $creditValue = $totalBalance_credit ? $totalBalance_credit->credit : 0;

                    $indent = str_repeat('&nbsp;', $level * 5);

                    $html .= '<tr>';
                    // NAME
                    $html .= '<td style="padding: 5px">';
                    $html .= $indent . $subAccount['code'] . ' - ';
                    $html .=
                        '<a href="' .
                        route('report.ledger', $subAccount['id']) .
                        '?account=' .
                        $subAccount['id'] .
                        '">' .
                        __($subAccount['name']) .
                        '</a>';
                    $html .= '</td>';

                    // CREDIT COLUMN
                    $html .= '<td class="text-end" style="padding: 5px">';
                    $html .= \Auth::user()->priceFormat($creditValue);
                    $html .= '</td>';

                    $html .= '</tr>';

                    $html .= renderSubAccounts_c($subAccount['id'], $subAccounts, $level + 1);
                }
            }
            return $html;
        }
        function renderSubAccounts_d($parentId, $subAccounts, $level)
        {
            $html = '';
            foreach ($subAccounts as $subAccount) {
                if ($subAccount['account'] == $parentId) {
                    // جلب إجمالي المدين والدائن
                    $totalBalance_debit = App\Models\TransactionLines::where('account_id', $subAccount['id'])->first([
                        'debit',
                    ]);
                    $debitValue = $totalBalance_debit ? $totalBalance_debit->debit : 0;

                    $totalBalance_credit = App\Models\TransactionLines::where('account_id', $subAccount['id'])->first([
                        'credit',
                    ]);
                    $creditValue = $totalBalance_credit ? $totalBalance_credit->credit : 0;

                    $indent = str_repeat('&nbsp;', $level * 5);

                    $html .= '<tr>';
                    // NAME
                    $html .= '<td style="padding: 5px">';
                    $html .= $indent . $subAccount['code'] . ' - ';
                    $html .=
                        '<a href="' .
                        route('report.ledger', $subAccount['id']) .
                        '?account=' .
                        $subAccount['id'] .
                        '">' .
                        __($subAccount['name']) .
                        '</a>';
                    $html .= '</td>';

                    // DEBIT COLUMN
                    $html .= '<td class="text-end" style="padding: 5px">';
                    $html .= \Auth::user()->priceFormat($debitValue);
                    $html .= '</td>';

                    $html .= '</tr>';

                    $html .= renderSubAccounts_c($subAccount['id'], $subAccounts, $level + 1);
                }
            }
            return $html;
        }
        function renderTotal($parentId, $subAccounts)
        {
            $subtotal = 0;

            foreach ($subAccounts as $subAccount) {
                if ($subAccount['account'] == $parentId) {
                    $start_date = $_GET['start_date'] ?? null;
                    $end_date = $_GET['end_date'] ?? null;

                    // احصل على الرصيد للحساب الفرعي
                    $totalBalance =
                        isset($start_date) && isset($end_date)
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
        function space($level)
        {
            $space = ''; // تهيئة المتغير قبل التكرار
            for ($i = 0; $i < $level; $i++) {
                $space .= '&nbsp;';
            }
            return $space;
        }

        $authUser = \Auth::user()->creatorId();
        $user = App\Models\User::find($authUser);
        $total_amount_in_total = 0;
    @endphp
    <div class="row justify-content-center" id="printableArea">
        <div class="col-md-8">
            <div class="card" id="card">
                <div class="card-body">
                    <div class="account-main-title mb-5 text-center">
                        <h5>{!! __('Cash Flow') .
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
                        $accountArrays = collect();
                        foreach ($chart_accounts as $key => $account) {
                            $chartDatas = App\Models\Utility::getAccountData(
                                $account['id'],
                                $filter['startDateRange'],
                                $filter['endDateRange'],
                            );
                            $chartDatas = $chartDatas->toArray();
                            if (!empty($chartDatas)) {
                                $accountArrays = $accountArrays->merge($chartDatas); // دمج البيانات في المجموعة
                            }
                        }

                        // فرز البيانات حسب 'created_at'
                        $accountArrays = $accountArrays->sortBy('created_at');
                        $accountArrays = $accountArrays->toArray();
                    @endphp
                    <table class="table table-bordered table-hover">
                        <tbody>
                            <tr>
                                <td style="font-weight: 900; padding: 5px" colspan="2">
                                    {{ __('Cashflow from Operating Activities') }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 900; padding: 5px">
                                    {!! space(5) . __('EBIT') !!}
                                </td>
                                <td class="text-end" style="font-weight: 500; padding: 5px">
                                    @php
                                        $EBIT = 0;
                                        foreach ($accountArrays as $account) {
                                            if (
                                                $account->reference == 'Invoice' &&
                                                $account->account_name == 'Revenue of Products and services Sales'
                                            ) {
                                                $EBIT += $account->credit;
                                            }
                                        }
                                    @endphp
                                    {{ \Auth::user()->priceFormat($EBIT) }}
                                    @php
                                    $total_amount_in_total += $EBIT;
                                @endphp
                                </td>
                            </tr>


                            <tr>
                                <td style="font-weight: 900; padding: 5px" colspan="2">
                                    {!! space(5) . __('Non cash adjustments') !!}
                                </td>
                            </tr>

                            <tr>
                                <td style="font-weight: 500; padding: 5px" colspan="2">
                                    {!! space(10) . __('Asset depreciation') !!}
                                </td>
                            </tr>
                            @php
                                $groupedChartAccounts_assets = array_intersect_key(
                                    $groupedChartAccounts,
                                    array_flip(['Assets']),
                                );
                                $groupedChartAccounts_expenses = array_intersect_key(
                                    $groupedChartAccounts,
                                    array_flip(['Expenses']),
                                );
                                $groupedChartAccounts_liabilities = array_intersect_key(
                                    $groupedChartAccounts,
                                    array_flip(['Liability']),
                                );
                                $groupedChartAccounts_equity = array_intersect_key(
                                    $groupedChartAccounts,
                                    array_flip(['Equity']),
                                );
                                $groupedChartAccounts_revenue = array_intersect_key(
                                    $groupedChartAccounts,
                                    array_flip(['Revenue']),
                                );

                            @endphp
                            @php
                                $total_amount_of_type = 0;
                            @endphp
                            @foreach ($groupedChartAccounts_expenses as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                @endphp
                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id && $subType->name == 'Operational Cost')
                                        @php
                                            $subtotalbalance = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'] && $account['code'] == 5215)
                                                @php
                                                    $totalBalance = App\Models\Utility::getAccountBalance(
                                                        $account['id'],
                                                        $filter['startDateRange'],
                                                        $filter['endDateRange'],
                                                    );

                                                    $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                    $subtotalbalance +=
                                                        (float) $totalBalance + (float) $subTotalForAccount;
                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
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
                                    @endif
                                @endforeach
                            @endforeach
                            <tr>
                                <td style="font-weight: 900; padding: 5px">
                                    {!! space(5) . __('Net non cash adjustments') !!}
                                </td>
                                <td class="text-end" style="font-weight: 500; padding: 5px">
                                    {{ \Auth::user()->priceFormat($total_amount_of_type) }}
                                    @php
                                        $total_amount_in_total += $total_amount_of_type;
                                    @endphp
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 900; padding: 5px" colspan="2">
                                    {!! space(5) . __('Changes in working capital') !!}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 500; padding: 5px" colspan="2">
                                    {!! space(10) . __('Decrease in AR') !!}
                                </td>
                            </tr>
                            @php
                                $total_amount_of_type = 0;
                            @endphp
                            @foreach ($groupedChartAccounts_assets as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                @endphp
                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id)
                                        @php
                                            $subtotalbalance = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'] && $account['code'] == 1103)
                                                @php
                                                    $totalBalance = App\Models\Utility::getAccountBalance(
                                                        $account['id'],
                                                        $filter['startDateRange'],
                                                        $filter['endDateRange'],
                                                    );

                                                    $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                    $subtotalbalance +=
                                                        (float) $totalBalance + (float) $subTotalForAccount;
                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
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
                                    @endif
                                @endforeach
                            @endforeach
                            <tr>
                                <td style="font-weight: 500; padding: 5px" colspan="2">
                                    {!! space(10) . __('Increase in inventory') !!}
                                </td>
                            </tr>
                            @foreach ($groupedChartAccounts_assets as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                @endphp
                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id)
                                        @php
                                            $subtotalbalance = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'] && $account['code'] == 1106)
                                                @php
                                                    $totalBalance = App\Models\Utility::getAccountBalance(
                                                        $account['id'],
                                                        $filter['startDateRange'],
                                                        $filter['endDateRange'],
                                                    );

                                                    $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                    $subtotalbalance +=
                                                        (float) $totalBalance + (float) $subTotalForAccount;
                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
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
                                    @endif
                                @endforeach
                            @endforeach
                            <tr>
                                <td style="font-weight: 500; padding: 5px" colspan="2">
                                    {!! space(10) . __('Decrease in prepaid expenses and other CA') !!}
                                </td>
                            </tr>
                            @foreach ($groupedChartAccounts_assets as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                @endphp
                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id)
                                        @php
                                            $subtotalbalance = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'] && $account['code'] == 1104)
                                                @php
                                                    $totalBalance = App\Models\Utility::getAccountBalance(
                                                        $account['id'],
                                                        $filter['startDateRange'],
                                                        $filter['endDateRange'],
                                                    );

                                                    $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                    $subtotalbalance +=
                                                        (float) $totalBalance + (float) $subTotalForAccount;
                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
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
                                    @endif
                                @endforeach
                            @endforeach
                            <tr>
                                <td style="font-weight: 500; padding: 5px" colspan="2">
                                    {!! space(10) . __('Increase in AP') !!}
                                </td>
                            </tr>
                            @foreach ($groupedChartAccounts_liabilities as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                @endphp
                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id)
                                        @php
                                            $subtotalbalance = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'] && $account['code'] == 2104)
                                                @php
                                                    $totalBalance = App\Models\Utility::getAccountBalance(
                                                        $account['id'],
                                                        $filter['startDateRange'],
                                                        $filter['endDateRange'],
                                                    );

                                                    $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                    $subtotalbalance +=
                                                        (float) $totalBalance + (float) $subTotalForAccount;
                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
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
                                    @endif
                                @endforeach
                            @endforeach
                            @foreach ($groupedChartAccounts_liabilities as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                @endphp
                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id)
                                        @php
                                            $subtotalbalance = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'] && $account['code'] == 2101)
                                                @php
                                                    $totalBalance = App\Models\Utility::getAccountBalance(
                                                        $account['id'],
                                                        $filter['startDateRange'],
                                                        $filter['endDateRange'],
                                                    );

                                                    $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                    $subtotalbalance +=
                                                        (float) $totalBalance + (float) $subTotalForAccount;
                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
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
                                    @endif
                                @endforeach
                            @endforeach
                            <tr>
                                <td style="font-weight: 500; padding: 5px" colspan="2">
                                    {!! space(10) . __('Increase (Decrease) in Accrued salaries and amounts owed to employees') !!}
                                </td>
                            </tr>
                            @foreach ($groupedChartAccounts_liabilities as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                @endphp
                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id)
                                        @php
                                            $subtotalbalance = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'] && $account['code'] == 2103)
                                                @php
                                                    $totalBalance = App\Models\Utility::getAccountBalance(
                                                        $account['id'],
                                                        $filter['startDateRange'],
                                                        $filter['endDateRange'],
                                                    );

                                                    $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                    $subtotalbalance +=
                                                        (float) $totalBalance + (float) $subTotalForAccount;
                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
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
                                    @endif
                                @endforeach
                            @endforeach
                            <tr>
                                <td style="font-weight: 500; padding: 5px" colspan="2">
                                    {!! space(10) . __('Increase in accrued expenses and other CL') !!}
                                </td>
                            </tr>
                            @foreach ($groupedChartAccounts_liabilities as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                @endphp
                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id)
                                        @php
                                            $subtotalbalance = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'] && $account['code'] == 2108)
                                                @php
                                                    $totalBalance = App\Models\Utility::getAccountBalance(
                                                        $account['id'],
                                                        $filter['startDateRange'],
                                                        $filter['endDateRange'],
                                                    );

                                                    $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                    $subtotalbalance +=
                                                        (float) $totalBalance + (float) $subTotalForAccount;
                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
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
                                    @endif
                                @endforeach
                            @endforeach
                            @foreach ($groupedChartAccounts_liabilities as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                @endphp
                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id)
                                        @php
                                            $subtotalbalance = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'] && $account['code'] == 2102)
                                                @php
                                                    $totalBalance = App\Models\Utility::getAccountBalance(
                                                        $account['id'],
                                                        $filter['startDateRange'],
                                                        $filter['endDateRange'],
                                                    );

                                                    $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                    $subtotalbalance +=
                                                        (float) $totalBalance + (float) $subTotalForAccount;
                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
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
                                    @endif
                                @endforeach
                            @endforeach
                            <tr>
                                <td style="font-weight: 500; padding: 5px" colspan="2">
                                    {!! space(10) . __('Increase (Decrease) in VAT Payable') !!}
                                </td>
                            </tr>
                            @foreach ($groupedChartAccounts_liabilities as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                @endphp
                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id)
                                        @php
                                            $subtotalbalance = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'] && $account['code'] == 2105)
                                                @php
                                                    $totalBalance = App\Models\Utility::getAccountBalance(
                                                        $account['id'],
                                                        $filter['startDateRange'],
                                                        $filter['endDateRange'],
                                                    );

                                                    $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                    $subtotalbalance +=
                                                        (float) $totalBalance + (float) $subTotalForAccount;
                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
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
                                    @endif
                                @endforeach
                            @endforeach

                            <tr>
                                <td style="font-weight: 900; padding: 5px">
                                    {!! space(5) . __('Net changes in working capital') !!}
                                </td>
                                <td class="text-end" style="font-weight: 500; padding: 5px">
                                    {{ \Auth::user()->priceFormat($total_amount_of_type) }}
                                    @php
                                    $total_amount_in_total += $total_amount_of_type;
                                @endphp
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 900; padding: 5px">
                                    {{ __('Net Cashflow from operating activities') }}
                                </td>
                                <td style="font-weight: 900; padding: 5px">

                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 900; padding: 5px" colspan="2">
                                    {{ __('Cashflow from investing activities') }}
                                </td>
                            </tr>
                            @php
                                $total_amount_of_type = 0;
                            @endphp
                            <tr>
                                <td style="font-weight: 500; padding: 5px" colspan="2">
                                    {!! space(10) . __('Increase in fixed assets') !!}
                                </td>
                            </tr>
                            @php
                                $groupedChartAccounts_assets_c = array_intersect_key(
                                    $groupedChartAccounts,
                                    array_flip(['Assets']),
                                );
                            @endphp
                            @foreach ($groupedChartAccounts_assets_c as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                    $total_amount_of_type_debit = 0;
                                    $total_amount_of_type_credit = 0;
                                @endphp

                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id && $subType->name == 'Non-current assets')
                                        @php
                                            $subtotalbalance_debit = 0;
                                            $subtotalbalance_credit = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'])
                                                @php

                                                    $totalBalance_debit = App\Models\TransactionLines::where(
                                                        'account_id',
                                                        $account['id'],
                                                    )->first(['debit']);
                                                    $debitValue = $totalBalance_debit ? $totalBalance_debit->debit : 0;

                                                    $totalBalance_credit = App\Models\TransactionLines::where(
                                                        'account_id',
                                                        $account['id'],
                                                    )->first(['credit']);
                                                    $creditValue = $totalBalance_credit
                                                        ? $totalBalance_credit->credit
                                                        : 0;

                                                    $subtotalbalance_debit += (float) $debitValue;
                                                    $subtotalbalance_debit += renderTotal_debit(
                                                        $account['id'],
                                                        $subAccounts,
                                                        3,
                                                    );

                                                    $subtotalbalance_credit += (float) $creditValue;
                                                    $subtotalbalance_credit += renderTotal_credit(
                                                        $account['id'],
                                                        $subAccounts,
                                                        3,
                                                    );

                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
                                                            {{ __($account['name']) }}
                                                        </a>
                                                    </td>

                                                    {{-- <td class="text-end" style="padding: 5px">
                                                        {{ $debitValue ? \Auth::user()->priceFormat($debitValue) : \Auth::user()->priceFormat(0) }}
                                                    </td> --}}
                                                    <td class="text-end" style="padding: 5px">
                                                        {{ $creditValue ? \Auth::user()->priceFormat($creditValue) : \Auth::user()->priceFormat(0) }}
                                                    </td>

                                                </tr>
                                                {!! renderSubAccounts_c($account['id'], $subAccounts, 3) !!}
                                                {{-- {!! renderSubAccounts_d($account['id'], $subAccounts, 3) !!} --}}
                                            @endif
                                        @endforeach

                                        @php
                                            $total_amount_of_type_debit += $subtotalbalance_debit;
                                            $total_amount_of_type_credit += $subtotalbalance_credit;
                                        @endphp
                                    @endif
                                @endforeach
                            @endforeach
                            <tr>
                                <td style="font-weight: 500; padding: 5px" colspan="2">
                                    {!! space(10) . __('Decrease in fixed assets') !!}
                                </td>
                            </tr>
                            @foreach ($groupedChartAccounts_assets_c as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                    $total_amount_of_type_debit = 0;
                                    $total_amount_of_type_credit = 0;
                                @endphp

                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id && $subType->name == 'Non-current assets')
                                        @php
                                            $subtotalbalance_debit = 0;
                                            $subtotalbalance_credit = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'])
                                                @php

                                                    $totalBalance_debit = App\Models\TransactionLines::where(
                                                        'account_id',
                                                        $account['id'],
                                                    )->first(['debit']);
                                                    $debitValue = $totalBalance_debit ? $totalBalance_debit->debit : 0;

                                                    $totalBalance_credit = App\Models\TransactionLines::where(
                                                        'account_id',
                                                        $account['id'],
                                                    )->first(['credit']);
                                                    $creditValue = $totalBalance_credit
                                                        ? $totalBalance_credit->credit
                                                        : 0;

                                                    $subtotalbalance_debit += (float) $debitValue;
                                                    $subtotalbalance_debit += renderTotal_debit(
                                                        $account['id'],
                                                        $subAccounts,
                                                        3,
                                                    );

                                                    $subtotalbalance_credit += (float) $creditValue;
                                                    $subtotalbalance_credit += renderTotal_credit(
                                                        $account['id'],
                                                        $subAccounts,
                                                        3,
                                                    );

                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
                                                            {{ __($account['name']) }}
                                                        </a>
                                                    </td>

                                                    <td class="text-end" style="padding: 5px">
                                                        {{ $debitValue ? \Auth::user()->priceFormat($debitValue) : \Auth::user()->priceFormat(0) }}
                                                    </td>
                                                    {{-- <td class="text-end" style="padding: 5px">
                                                        {{ $creditValue ? \Auth::user()->priceFormat($creditValue) : \Auth::user()->priceFormat(0) }}
                                                    </td> --}}

                                                </tr>
                                                {!! renderSubAccounts_d($account['id'], $subAccounts, 3) !!}
                                                {{-- {!! renderSubAccounts_d($account['id'], $subAccounts, 3) !!} --}}
                                            @endif
                                        @endforeach

                                        @php
                                            $total_amount_of_type_debit += $subtotalbalance_debit;
                                            $total_amount_of_type_credit += $subtotalbalance_credit;
                                        @endphp
                                    @endif
                                @endforeach
                            @endforeach
                            <tr>
                                <td style="font-weight: 900; padding: 5px">
                                    {{ __('Net Cashflow from investing activities') }}
                                </td>
                                <td class="text-end" style="font-weight: 500; padding: 5px">
                                    {{ \Auth::user()->priceFormat($total_amount_of_type_debit - $total_amount_of_type_credit) }}
                                    @php
                                    $total_amount_in_total += $total_amount_of_type_debit - $total_amount_of_type_credit;
                                @endphp
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 900; padding: 5px" colspan="2">
                                    {{ __('Cashflow from financing activities') }}
                                </td>
                            </tr>

                            <tr>
                                <td style="font-weight: 500; padding: 5px" colspan="2">
                                    {!! space(10) . __('Related parties') !!}
                                </td>
                            </tr>
                            @php
                            $total_amount_of_type = 0;
                            @endphp
                            @foreach ($groupedChartAccounts_equity as $typeName => $accounts)
                                @php
                                    $type = $types
                                        ->where('name', $typeName)
                                        ->where('created_by', \Auth::user()->creatorId())
                                        ->first();
                                @endphp
                                @foreach ($subtypes as $subType)
                                    @if ($subType->type == $type->id)
                                        @php
                                            $subtotalbalance = 0;
                                        @endphp
                                        @foreach ($accounts as $account)
                                            @if ($subType->name == $account['sub_type_name'] && $account['code'] == 3201)
                                                @php
                                                    $totalBalance = App\Models\Utility::getAccountBalance(
                                                        $account['id'],
                                                        $filter['startDateRange'],
                                                        $filter['endDateRange'],
                                                    );

                                                    $subTotalForAccount = renderTotal($account['id'], $subAccounts);

                                                    $subtotalbalance +=
                                                        (float) $totalBalance + (float) $subTotalForAccount;
                                                @endphp

                                                <tr>
                                                    <td style="padding: 5px">
                                                        {!! '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $account['code'] !!}
                                                        - <a
                                                            href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">
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
                                    @endif
                                @endforeach
                            @endforeach

                            <tr>
                                <td style="font-weight: 900; padding: 5px">
                                    {{ __('Net Cashflow from financing activities') }}
                                </td>
                                <td class="text-end" style="font-weight: 900; padding: 5px">
                                    {{ \Auth::user()->priceFormat($total_amount_of_type) }}
                                    @php
                                        $total_amount_in_total += $total_amount_of_type;
                                    @endphp
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 900; padding: 5px ; border-top: 2px solid #000">
                                    {{ __('Net increase in cash and equivalents') }}
                                </td>
                                <td class="text-end"  style="font-weight: 900; padding: 5px ; border-top: 2px solid #000">
                                    {{ \Auth::user()->priceFormat($total_amount_in_total) }}
                                </td>
                            </tr>
                            {{-- <tr>
                                <td style="font-weight: 900; padding: 5px ; border-top: 2px solid #000">
                                    {{ __('Cash and equivalents beginning of period') }}
                                </td>
                                <td style="font-weight: 900; padding: 5px ; border-top: 2px solid #000">
                                    1111
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: 900; padding: 5px ; border-top: 2px solid #000">
                                    {{ __('Cash and equivalents end of period') }}
                                </td>
                                <td style="font-weight: 900; padding: 5px ; border-top: 2px solid #000">
                                    1111
                                </td>
                            </tr> --}}

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection
