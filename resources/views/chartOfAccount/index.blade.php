@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Chart of Accounts') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Chart of Account') }}</li>
@endsection
@push('script-page')
    <script>
        $(document).on('change', '#sub_type', function() {
            $('.acc_check').removeClass('d-none');
            var type = $(this).val();
            $.ajax({
                url: '{{ route('charofAccount.subType') }}',
                type: 'POST',
                data: {
                    "type": type,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#parent').empty();
                    $.each(data, function(key, value) {
                        $('#parent').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                }
            });
        });
        $(document).on('change', '#sub_type', function() {
            var account = $(this).val();
            console.log(account);
            $.ajax({
                url: '{{ route('charofAccount.getaccountcode') }}',
                type: 'POST',
                data: {
                    "account": account,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                $('#accounts_code').val(data);
                    console.log(data);
                }
            });
        });

        $(document).on('change', '#parent', function() {
            var account = $(this).val();
            console.log(account);
            $.ajax({
                url: '{{ route('charofAccount.getsubaccountcode') }}',
                type: 'POST',
                data: {
                    "account": account,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                $('#accounts_code').val(data);
                    console.log(data);
                }
            });
        });

        $(document).on('click', '#account', function() {
            const element = $('#account').is(':checked');
            $('.acc_type').addClass('d-none');
            $('#parent').prop('required', false);
            if (element==true) {
                $('.acc_type').removeClass('d-none');
                $('#parent').prop('required', true);

            } else {
                $('.acc_type').addClass('d-none');
                $('#parent').prop('required', false);
            }
        });
    </script>
            <script>
                $(document).ready(function () {
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

@section('action-btn')
    <div class="float-end">
        @can('create chart of account')
            <a href="#" data-url="{{ route('chart-of-account.create') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                data-size="lg" data-ajax-popup="true" data-title="{{ __('Create New Account') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection
@section('content')

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="mt-2" id="multiCollapseExample1">
            <div class="card" id="show_filter">
                <div class="card-body">
                    {{ Form::open(['route' => ['chart-of-account.index'], 'method' => 'GET', 'id' => 'report_bill_summary']) }}
                    <div class="row align-items-center justify-content-end">
                        <div class="col-xl-10">
                            <div class="row">
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                    <div class="btn-box">
                                    </div>
                                </div>
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
                            </div>
                        </div>
                        <div class="col-auto mt-4">
                            <div class="row">
                                <div class="col-auto">
                                    <a href="#" class="btn btn-sm btn-primary"
                                        onclick="document.getElementById('report_bill_summary').submit(); return false;"
                                        data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                        data-original-title="{{ __('apply') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                    </a>

                                    <a href="{{ route('chart-of-account.index') }}" class="btn btn-sm btn-danger "
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
    <div class="row">
        {{-- @dd($chartAccounts) --}}

        @foreach ($groupedChartAccounts as $typeName => $accounts)
            {{-- @dd($accounts) --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        @if ($typeName == "Revenue")
                            @php
                                 $typeName = __('Revenues') ;
                            @endphp
                            
                        @endif
                        <h6>{{ __($typeName) }}</h6>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="10%"> {{ __('Code') }}</th>
                                        <th width="30%"> {{ __('Name') }}</th>
                                        <th width="20%"> {{ __('Type') }}</th>
                                        <th width="20%"> {{ __('Balance') }}</th>
                                        <th width="10%"> {{ __('Status') }}</th>
                                        <th width="10%"> {{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>



                                    @foreach ($accounts as $account)
                                        @php
                                            $balance = 0;
                                            $totalDebit = 0;
                                            $totalCredit = 0;
                                            $totalBalance = App\Models\Utility::getAccountBalance($account['id'],$filter['startDateRange'],$filter['endDateRange']);
                                        @endphp

                                        <tr>
                                            <td>{{ $account['code'] }}</td>
                                            <td><a
                                                    href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}">{{ __($account['name']) }}</a>
                                            </td>

                                            <td>{{ !empty($account['sub_type_name']) ? __($account['sub_type_name']) : '-' }}</td>
                                            <td>
                                                @if (!empty($totalBalance))
                                                    {{ \Auth::user()->priceFormat($totalBalance) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($account['is_enabled'] == 1)
                                                    <span
                                                        class="badge bg-primary p-2 px-3 rounded">{{ __('Enabled') }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger p-2 px-3 rounded">{{ __('Disabled') }}</span>
                                                @endif
                                            </td>
                                            <td class="Action">
                                                <div class="action-btn bg-warning ms-2">
                                                    <a href="{{ route('report.ledger', $account['id']) }}?account={{ $account['id'] }}"
                                                        class="mx-3 btn btn-sm align-items-center " data-bs-toggle="tooltip"
                                                        title="{{ __('Transaction Summary') }}"
                                                        data-original-title="{{ __('Detail') }}">
                                                        <i class="ti ti-wave-sine text-white"></i>
                                                    </a>
                                                </div>

                                                @can('edit chart of account')
                                                    <div class="action-btn bg-primary ms-2">
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center"
                                                            data-url="{{ route('chart-of-account.edit', $account['id']) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Edit Account') }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            data-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('delete chart of account')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['chart-of-account.destroy', $account['id']],
                                                            'id' => 'delete-form-' . $account['id'],
                                                        ]) !!}
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm align-items-center bs-pass-para"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                            data-original-title="{{ __('Delete') }}"
                                                            data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="document.getElementById('delete-form-{{ $account['id'] }}').submit();">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
                                            </td>
                                        </tr>
                                        {{-- @dd($filter) --}}
                                        {!! renderSubAccounts($account['id'], $subAccounts, 1 ) !!}
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @php
        function renderSubAccounts($parentId, $subAccounts, $level ) {
            $html = '';

            foreach ($subAccounts as $subAccount) {

                if ($subAccount['account'] == $parentId) {
                                            if(isset($_GET['start_date'])){
                                                $start_date = $_GET['start_date'];
                                            }
                                            if(isset($_GET['end_date'])){
                                                $end_date = $_GET['end_date'];
                                            }
                                            $balance = 0;
                                            $totalDebit = 0;
                                            $totalCredit = 0;
                                            if(isset($start_date) && isset($end_date)){
                                                $totalBalance = App\Models\Utility::getAccountBalance($subAccount['id'], $start_date,$end_date);
                                            }else {
                                                $totalBalance = App\Models\Utility::getAccountBalance($subAccount['id']);
                                            }

                    $indent = str_repeat('&nbsp;', $level * 5);
                    $html .= '<tr>';
                    // CODE
                    $html .= '<td>';
                    $html .= $indent.$subAccount['code'];
                    $html .= '</td>';
                    // NAME
                    $html .= '<td>';
                    $html .= '<a href="'. route('report.ledger', $subAccount['id']).'?account='.$subAccount['id'].'">'.$indent.__($subAccount['name']) . '</a>';
                    $html .= '</td>';

                    // TYPE
                    $html .= '<td>';
                    $html .=  __($subAccount['sub_type_name']);
                    $html .= '</td>';

                    // BALANCE
                    $html .= '<td>';
                        if(!empty($totalBalance)){
                            $html .= \Auth::user()->priceFormat($totalBalance) ;
                        }else {
                            $html .= '-';
                        }
                    $html .= '</td>';
                    // STATUS
                    $html .= '<td>';

                        if ($subAccount['is_enabled'] == 1){
                            $html .='  <span class="badge bg-primary p-2 px-3 rounded">'. __('Enabled') .'</span>';
                            }else{
                            $html .='  <span class="badge bg-danger p-2 px-3 rounded">'. __('Disabled') .'</span>';
                            }

                    $html .= '</td>';
                    // ACTION
                    $html .= '<td class="Action">';

                    $html .= '
                                <div class="action-btn bg-warning ms-2">
                                    <a href="'. route('report.ledger', $subAccount['id']) .'?account='. $subAccount['id'].'"
                                        class="mx-3 btn btn-sm align-items-center " data-bs-toggle="tooltip"
                                        title="'. __('Transaction Summary') .'"
                                        data-original-title="'. __('Detail') .'">
                                        <i class="ti ti-wave-sine text-white"></i>
                                    </a>
                                </div>
                    ';
                    if(\Auth::user()->can('edit chart of account')){
                        $html .= '
                                    <div class="action-btn bg-primary ms-2">
                                        <a href="#"
                                            data-url="'. route('chart-of-account.edit', $subAccount['id']) .'"
                                            data-ajax-popup="true"
                                            class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip"
                                            title="'. __('Edit') .'"
                                            data-original-title="'. __('Edit') .'">
                                            <i class="ti ti-pencil text-white"></i>
                                        </a>
                                    </div>
                        ';
                    }
                    if(\Auth::user()->can('delete chart of account')){
                    $html .= '<div class="action-btn bg-danger ms-2">';
                        $html .= Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['chart-of-account.destroy', $subAccount['id']],
                                                'id' => 'delete-form-' . $subAccount['id'],
                        ]);
                        $html .= '<a href="#"
                            class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip"
                            title="'. __('Delete') .'"
                            data-original-title="'. __('Delete') .'"
                            data-confirm="'. __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') .'"
                            data-confirm-yes="document.getElementById(\'delete-form-'. $subAccount['id'] .'\').submit();">
                            <i class="ti ti-trash text-white"></i>
                        </a>';
                        $html .= Form::close();
                    $html .=  '</div>';
                    }

                    $html .= '</td>';

                    $html .= '</tr>';
                    $html .= renderSubAccounts($subAccount['id'], $subAccounts, $level + 1);
                }
            }
            return $html;
        }
        @endphp
    </div>
@endsection
