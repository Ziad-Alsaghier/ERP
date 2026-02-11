@extends('layouts.admin')
@section('page-title')
{{ __('Payable Reports') }}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Payable Reports') }}</li>
@endsection

@section('content')
<style>
    .clickable-card{
        display: flex;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        padding-top: 36px;
        transition: all 0.5s ease-in-out;
    }
    .clickable-card i{
        font-size: 50px;
        margin-bottom: 20px;
        color: #1a5bb8;
    }
    .clickable-card span{
        color: black;
    }
    #report-title h5{
        font-size: 25px;
        border-bottom: 7px solid #1a5bb8;
        width: fit-content;
        margin:auto;
        margin-bottom: 30px;
        padding-bottom: 10px;
        transition: all 0.5s ease-in-out;
    }
    .clickable-card:hover{
        scale: 1.04;
        background-color: #1a5bb8;
        color: #fff;

    }
    .clickable-card:hover i, .clickable-card:hover h5,.clickable-card:hover span{
        color: #fff;
    }
</style>
<div class="row" style="margin-top: 100px">
    <div class="col-md-3 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="text-center" id="Report">{{ __('Report') }}</h5>
            </div>
            <div class="card-body">
                <ul class="nav flex-column nav-pills">
                    <li>
                        <a href="#" id="showReports" class="nav-link">{{__('Show All Reports')}}</a>
                    </li>
                    <hr>
                    @foreach ($parentMenu as $item)
                    <li class="nav-item mt-2">
                        <a class="nav-link {{ $item['active'] ? 'active' : '' }}" href="#{{ $item['id'] }}">
                            {{ $item['label'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-9 col-sm-12">

        <div class="row mb-4">
            <div class="col-md-6">
                <input type="text" id="reportFilter" class="form-control" placeholder="{{__('Search Reports')}}"
                    style="border-radius: 10px;">
            </div>
        </div>


        {{-- Start Financial Report --}}
        <div id="financial_reports" class="report-section">
            <div class="col-md-12">
                {{-- Row Is Here --}}
                <div class="row report-card">
                        <div class="col-md-12 col-sm-12" id="report-title">
                            <h5 class="text-center">{{ __('Financial Reports') }}</h5>
                        </div>
                    @foreach ($reports['financial'] as $item)
                        @if ($item['status'] == 1)
                        <a class="col-md-3 col-sm-6" href="{{ route($item['route']) }}">
                            <div class="card widget-flat icon clickable-card">
                                    <i class="ti {{ $item['icon'] }}"></i>
                                <h5 class="text-center" id="Ledger">{{ __($item['title']) }}</h5>
                                <span class="mb-5 mx-4 text-center">
                                    {{ __($item['description']) }}
                                </span>
                            </div>
                        </a>
                        @endif
                    @endforeach
                    {{-- Card ledger --}}
                </div>
                {{-- Section Four --}}
            </div>
        </div>
{{-- End Financial Report --}}

{{-- Start HRM Report --}}
<div id="receivable" class="report-section" style="display:none;">
    <div class="col-md-9 col-sm-12">
        {{-- Row Is Here --}}
        <div class="row report-card">
            <div class="col-md-12 col-sm-12" id="report-title">
                <h5 class="text-center">{{ __('HRM') }}</h5>
            </div>
            {{-- Card Payroll report --}}
            @foreach ($reports['HRM'] as $item)
            @if ($item['status'] == 1)
            <a class="col-md-3 col-sm-6" href="{{ $item['route'] }}">
                <div class="card widget-flat icon clickable-card">
                        <i class="ti {{ $item['icon'] }}"></i>
                    <h5 class="text-center" id="Ledger">{{ __($item['title']) }}</h5>
                    <span class="mb-5 mx-4 text-center">
                        {{ __($item['description']) }}
                    </span>
                </div>
            </a>
            @endif
            @endforeach
            
        </div>
    </div>
</div>
{{-- End HRM Report --}}

{{-- Start CRM Report --}}
<div id="crm" class="report-section" style="display:none;">
    <div class="col-md-9 col-sm-12">
        {{-- Row Is Here --}}
        <div class="row report-card">
            <div class="col-md-12 col-sm-12" id="report-title">
                <h5 class="text-center">{{ __('CRM') }}</h5>
            </div>
            {{-- CRM --}}
            @foreach ($reports['CRM'] as $card)
            @if ($card['status'] == 1)
            <a class="col-md-3 col-sm-6" href="{{ $card['route'] }}">
                <div class="card widget-flat icon clickable-card">
                        <i class="ti {{ $card['icon'] }}"></i>
                    <h5 class="text-center" id="Ledger">{{ __($card['title']) }}</h5>
                    <span class="mb-5 mx-4 text-center">
                        {{ __($card['description']) }}
                    </span>
                </div>
            </a>
            @endif
            @endforeach

        </div>
    </div>
</div>
{{-- End CRM Report --}}

{{-- Start Report POS --}}
<div id="pos" class="report-section" style="display:none;">
    <div class="col-md-9 col-sm-12">
        <div class="col-md-12 col-sm-12" id="report-title">
            <h5 class="text-center">{{ __('POS') }}</h5>
        </div>
        {{-- Row Is Here --}}
        <div class="row report-card">
            @foreach ($reports['POS'] as $card)
            @if ($card['status'] == 1)
            <a class="col-md-3 col-sm-6" href="{{ $card['route'] }}">
                <div class="card widget-flat icon clickable-card">
                        <i class="ti {{ $card['icon'] }}"></i>
                    <h5 class="text-center" id="Ledger">{{ __($card['title']) }}</h5>
                    <span class="mb-5 mx-4 text-center">
                        {{ __($card['description']) }}
                    </span>
                </div>
            </a>
            @endif
            @endforeach
        </div>
    </div>
</div>
</div>



<script>
    $(document).ready(function() {
            // Start detailes <i class="ti ti-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} rightArrow"></i> Report Section related to clicked tab
            $("ul.nav-pills > li > a").click(function(e) {
                e.preventDefault(); // Prevent default anchor behavior
                $("ul.nav-pills > li > a").removeClass('active'); // Remove active class from all
                $(this).addClass('active'); // Add active class to clicked tab
                $('.report-section').hide(); // Hide all report sections
                $($(this).attr('href')).fadeIn(500)
                    .show(); // detailes <i class="ti ti-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} rightArrow"></i> the related section based on href
            });
            // End detailes <i class="ti ti-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} rightArrow"></i> Report Section related to clicked tab
            // Start Filter Reports
            $("#reportFilter").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".card").hide(); // Hide all report cards
                $(".card").filter(function() {
                    var match = $(this).find('h5, p').text().toLowerCase().indexOf(value) > -1;
                    if (match) {
                        $(this).fadeIn(
                            500
                            ); // detailes <i class="ti ti-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} rightArrow"></i> the matching report card with animation
                    }
                });
                if ($("#reportFilter").val().trim() === "") {
                    $(".report-card").fadeIn(
                        500
                        ); // detailes <i class="ti ti-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} rightArrow"></i> all report cards with animation
                }
            });
            // End Filter Reports
            // Start detailes <i class="ti ti-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} rightArrow"></i> All Reports
            $('#showReports').click(function() {
                $('.report-section').fadeIn(500).show();
            });
            // End detailes <i class="ti ti-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} rightArrow"></i> All Reports
        });
        // Card Clicked To Go Route
        $(document).ready(function() {
            $('.clickable-card').click(function() {
                var link = $(this).find('a').attr('href');
                window.location.href = link;
            });
        });
</script>
@endsection
