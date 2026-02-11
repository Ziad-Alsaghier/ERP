@extends('landingpage::layouts.landingpage')
@section('content')

@if ($settings['home_status'] == 'on')
<section class="main-banner bg-gray-100" id="home">
    {{-- Main Bannar --}}

    <div class="container text-center">
        <div class="row align-items-center">
            <div class="col-12 align-self-center landing_heading mt-5">
                <h1 class="mb-3">
                    {{ __($settings['home_heading']) }}
                </h1>
                <h5 class="mb-0">{{ __($settings['home_description']) }}</h5>

                <div class="row justify-content-md-center mt-5">
                    <div class="col-md-auto">
                        <a href="{{ route('register') }}" class="btn btn-dark">
                            {{ __('Try it free') }}
                            <i data-feather="navigation" class="me-3"></i>
                        </a>
                    </div>
                    <div class="col-md-auto">
                        <a href="#discover" class="btn btn-outline-dark">
                            {{ __('Show apps') }}
                            <i data-feather="play-circle" class="me-3"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endif
@if (!empty($settings['home_logo']))
{{-- clients logo --}}

<div class="container">
    <div class="row g-0 gy-2 mt-4 align-items-center">
        <div class="col-xxl-3">
            <p class="mb-0">{{ __('Trusted by') }} <b class="fw-bold">{{ $settings['home_trusted_by'] }}</b></p>
        </div>
        <div class="col-xxl-9">
            <div class="row gy-3 row-cols-9">
                @foreach (explode(',', $settings['home_logo']) as $k => $home_logo )

                <div class="col-auto">
                    <img src="{{ $logo.'/'. $home_logo }}" alt="" class="landing_logo"
                        style="width: 130px;">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif


@if ($settings['feature_status'] == 'on' && $settings['feature_of_features'] !== "[]")

<section class="features-section section-gap bg-dark" id="features">
    <div class="container">
        <div class="row gy-3">
            <div class="col-xxl-4">
                <span class="d-block mb-2 text-uppercase">{{ $settings['feature_title'] }}</span>
                <div class="title mb-4">
                    <h2><b class="fw-bold">{!! $settings['feature_heading'] !!}</b></h2>
                </div>
                <p class="mb-3">{!! $settings['feature_description'] !!}</p>
                @if ($settings['feature_buy_now_link'])
                <a href="{{ $settings['feature_buy_now_link'] }}"
                    class="btn btn-primary rounded-pill d-inline-flex align-items-center">{{ __('Buy Now') }}
                    <i data-feather="lock" class="ms-2"></i></a>
                @endif
            </div>
            <div class="col-xxl-8">
                <div class="row">
                    @if (is_array(json_decode($settings['feature_of_features'], true)) ||
                    is_object(json_decode($settings['feature_of_features'], true)))
                    @foreach (json_decode($settings['feature_of_features'], true) as $key => $value)
                    <div class="col-lg-4 col-sm-6 d-flex">
                        <div class="card {{ $key == 0 ? 'bg-primary' : '' }}">
                            <div class="card-body">
                                <span class="theme-avtar avtar avtar-xl mb-4">
                                    <img src="{{ $logo . '/' . $value['feature_logo'] }}" alt="">
                                </span>
                                <h3 class="mb-3 {{ $key == 0 ? '' : 'text-white' }}">
                                    {!! $value['feature_heading'] !!}</h3>
                                <p class=" f-w-600 mb-0 {{ $key == 0 ? 'text-body' : '' }}">
                                    {!! $value['feature_description'] !!}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            <div class="mt-5">
                <div class="title text-center mb-4">
                    <span class="d-block mb-2 text-uppercase">{{ $settings['feature_title'] }}</span>
                    <h2 class="mb-4">{!! $settings['highlight_feature_heading'] !!}</h2>
                    <p>{!! $settings['highlight_feature_description'] !!}</p>
                </div>
                <div class="features-preview">
                    <img class="img-fluid m-auto d-block"
                        src="{{ $logo . '/' . $settings['highlight_feature_image'] }}" alt="">
                </div>
            </div>
        </div>
    </div>
</section>
@endif


@if ($settings['feature_status'] == 'on' && $settings['other_features'] !== "[]")
<section class="element-section  section-gap ">
    <div class="container">
        @if (is_array(json_decode($settings['other_features'], true)) ||
        is_object(json_decode($settings['other_features'], true)))
        @foreach (json_decode($settings['other_features'], true) as $key => $value)
        @if ($key % 2 == 0)
        <div class="row align-items-center justify-content-center mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="title mb-4">
                    <span class="d-block fw-bold mb-2 text-uppercase">{{ __('Features') }}</span>
                    <h2>
                        {!! $value['other_features_heading'] !!}
                    </h2>
                </div>
                <p class="mb-3">{!! $value['other_featured_description'] !!}</p>
                <a href="{{ $value['other_feature_buy_now_link'] }}"
                    class="btn btn-primary rounded-pill d-inline-flex align-items-center">{{ __('Buy Now ') }}
                    <i data-feather="lock" class="ms-2"></i></a>
            </div>
            <div class="col-lg-7 col-md-6 res-img">
                <div class="img-wrapper">
                    <img src="{{ $logo . '/' . $value['other_features_image'] }}" alt=""
                        class="img-fluid header-img">
                </div>
            </div>
        </div>
        @else
        <div class="row align-items-center justify-content-center mb-4">
            <div class="col-lg-7 col-md-6">
                <div class="img-wrapper">
                    <img src="{{ $logo . '/' . $value['other_features_image'] }}" alt=""
                        class="img-fluid header-img">
                </div>
            </div>
            <div class="col-lg-4  col-md-6">
                <div class="title mb-4">
                    <span class="d-block fw-bold mb-2 text-uppercase">{{ __('Features') }}</span>
                    <h2>
                        {!! $value['other_features_heading'] !!}
                    </h2>
                </div>
                <p class="mb-3">{!! $value['other_featured_description'] !!}</p>
                <a href="{{ $value['other_feature_buy_now_link'] }}"
                    class="btn btn-primary rounded-pill d-inline-flex align-items-center">{{ __('Buy Now ') }}
                    <i data-feather="lock" class="ms-2"></i></a>
            </div>
        </div>
        @endif
        @endforeach
        @endif

    </div>
</section>
@endif
@if ($settings['discover_status'] == 'on' && $settings['discover_of_features'] !== "[]")
<section class="bg-dark section-gap" id="discover">
    <div class="container">
        <div class="row mb-2 justify-content-center">
            <div class="col-xxl-6">
                <div class="title text-center mb-4">
                    <h2 class="mb-4">{{ __($settings['discover_heading']) }}</h2>
                    <p>{{  __($settings['discover_description']) }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            @if (is_array(json_decode($settings['discover_of_features'], true)) ||
            is_object(json_decode($settings['discover_of_features'], true)))
            @foreach (json_decode($settings['discover_of_features'], true) as $key => $value)
            <div class="col-xxl-3 col-sm-6 col-lg-4 ">
                <div class="card   border {{ $key == 1 ? 'bg-primary' : 'bg-transparent' }}">
                    <div class="card-body text-center">
                        <span class="theme-avtar avtar avtar-xl mx-auto mb-4">
                            <img src="{{ $logo . '/' . $value['discover_logo'] }}" alt="">
                        </span>
                        <h3 class="mb-3 {{ $key == 1 ? '' : 'text-white' }} ">{{ __($value['discover_heading']) }}
                        </h3>
                        <p class="{{ $key == 1 ? 'text-body' : '' }}">
                            {!! __($value['discover_description']) !!}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
            @endif

        </div>
        <div class="d-flex flex-column justify-content-center flex-sm-row gap-3 mt-3">
            @if ($settings['discover_live_demo_link'])
            <a href="{{ $settings['discover_live_demo_link'] }}"
                class="btn btn-outline-light rounded-pill">{{ __('عرض كافة التطبيقات') }}
                <i data-feather="play-circle" class="ms-2"></i>
            </a>
            @endif

            @if ($settings['discover_buy_now_link'])
            <a href="{{ $settings['discover_buy_now_link'] }}"
                class="btn btn-primary rounded-pill">{{ __('اطلب تطبيقك الخاص ') }} <i data-feather="lock"
                    class="ms-2"></i> </a>
            @endif
        </div>
    </div>
</section>
@endif

@if ($settings['screenshots_status'] == 'on' && $settings['screenshots'] !== "[]")
<section class="screenshots section-gap">
    <div class="container">
        <div class="row mb-2 justify-content-center">
            <div class="col-xxl-6">
                <div class="title text-center mb-4">
                    <span class="d-block mb-2 fw-bold text-uppercase">{{ __('SCREENSHOTS') }}</span>
                    <h2 class="mb-4">{!! $settings['screenshots_heading'] !!}</h2>
                    <p>{!! $settings['screenshots_description'] !!}</p>
                </div>
            </div>
        </div>
        <div class="row gy-4 gx-4">
            @if (is_array(json_decode($settings['screenshots'], true)) || is_object(json_decode($settings['screenshots'], true)))
            @foreach (json_decode($settings['screenshots'], true) as $value)
            <div class="col-md-4 col-sm-6">
                <div class="screenshot-card">
                    <div class="img-wrapper">
                        <img src="{{ $logo . '/' . $value['screenshots'] }}"
                            class="img-fluid header-img mb-4 shadow-sm" alt="">
                    </div>
                    <h5 class="mb-0">{!! $value['screenshots_heading'] !!}</h5>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</section>
@endif

@if ($settings['plan_status'])
<section class="subscription bg-gray-200 section-gap" id="plan">
    <div class="container">
        <div class="row mb-2 justify-content-center">
            <div class="col-xxl-12">
                <div class="title text-center mb-4">
                    <h2 class="mb-4">{!! __($settings['plan_heading']) !!}</h2>
                    <p>{!! __($settings['plan_description']) !!}</p>
                </div>
            </div>

            <div class="row justify-content-between">
                <div class="col-auto">
                    <div class="form-check form-switch custom-switch-v1 d-flex align-items-center">
                        <h6 class="m-2" id="filt-monthly">Monthly</h6>
                        <div class="toggle">
                            <input type="checkbox" id="switcher" class="check">
                            <b class="b switch"></b>
                        </div>
                        <h6 class="m-2 text-secondary" id="filt-yearly">Yearly</h6>
                    </div>
                    </div>
                    <div class="col-auto">
                    <div class="form-check form-switch custom-switch-v1 d-flex align-items-center">
                    <h6 class="m-2 text-secondary" id="filt-egp">EGP</h6>
                    <div class="toggle">
                            <input type="checkbox" id="currency-switch" class="check" checked>
                            <b class="b switch"></b>
                        </div>
                        <h6 class="m-2" id="filt-usd">USD</h6>

                    </div>
                    </div>
                    
                </div>
            </div>




        </div>


        @php
        $collection = \App\Models\Plan::orderBy('price', 'ASC')->get();
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $data = [
            'rates' => ['EGP' =>48.91],  
        ];
        // Assume $data is passed to the Blade view
        $rates = $data['rates'];
        // Get the keys of the rates array
        $currencyCodes = array_keys($rates);

        @endphp



        <div class="row justify-content-center" id="monthly">
            @foreach ($collection as $key => $value)
            @if ($value->duration == "month" && $value->is_visible == 1)
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card price-card shadow-none">
                    <div class="card-body">
                        <span class="price-badge bg-dark">{{ __($value->name) }}</span>


                        <span
                            
                            class="mb-4 f-w-00 p-price dollar d-none">{{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '$' }}{{ intval($value->price) }}<small
                                class="text-sm">/{{ $value->duration }}</small></span>
                        @foreach ($rates as $currencyCode => $rate)
                        <span
                        
                            class="mb-4 f-w-00 p-price pound-egy"> {{ $currencyCode }}{{ intval($value->price * $data['rates']['EGP'] + 5) }}<small
                                class="text-sm">/{{ $value->duration }}</small></span>

                        <p>
                            @endforeach

                            {!! $value->description !!}
                        </p>
                        <ul class="list-unstyled my-3">
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{{ $value->max_users == -1 ? 'Unlimited' : $value->max_users }}
                                        {{ __('User') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{{ $value->max_customers == -1 ? 'Unlimited' : $value->max_customers }}
                                        {{ __('Customer') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{{ $value->max_venders == -1 ? 'Unlimited' : $value->max_venders }}
                                        {{ __('Vendors') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{{ $value->max_clients == -1 ? 'Unlimited' : $value->max_clients }}
                                        {{ __('Clients') }}</label>
                                </div>
                            </li>

                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!! $value->account == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('Account') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!! $value->crm == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('CRM') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!! $value->hrm == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('HRM') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!! $value->project == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('Project') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!! $value->pos == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('POS') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!! $value->chatgpt == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('ChatGPT') }}</label>
                                </div>
                            </li>

                        </ul>
                        <div class="d-grid">
                            <a href="{{ route('register') }}"
                                class="btn btn-primary rounded-pill">{{ __('Start with Starter') }} <i
                                    data-feather="log-in" class="ms-2"></i> </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        <div class="row justify-content-center d-none" id="yearly">
            @foreach ($collection as $key => $value)
            @if ($value->duration == "year" && $value->is_visible == 1)
            <div class="col-xxl-3 col-lg-4 col-md-6">
                <div class="card price-card shadow-none">
                    <div class="card-body">
                        <span class="price-badge bg-dark">{{ $value->name }}</span>
                        <span
                        
                            class="mb-4 f-w-00 p-price dollar d-none">{{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '$' }}{{ intval($value->price) }}<small
                                class="text-sm">/{{ $value->duration }}</small></span>
                        @foreach ($rates as $currencyCode => $rate)
                        <span
                        
                            class="mb-4 f-w-00 p-price pound-egy"> {{ $currencyCode }}{{ intval($value->price * $data['rates']['EGP'] + 5) }}<small
                                class="text-sm">/{{ $value->duration }}</small></span>

                        <p>
                            @endforeach
                        <p>
                            {!! $value->description !!}
                        </p>
                        <ul class="list-unstyled my-3">
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{{ $value->max_users == -1 ? 'Unlimited' : $value->max_users }}
                                        {{ __('User') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{{ $value->max_customers == -1 ? 'Unlimited' : $value->max_customers }}
                                        {{ __('Customer') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{{ $value->max_venders == -1 ? 'Unlimited' : $value->max_venders }}
                                        {{ __('Vendors') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{{ $value->max_clients == -1 ? 'Unlimited' : $value->max_clients }}
                                        {{ __('Clients') }}</label>
                                </div>
                            </li>

                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!! $value->account == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('Account') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!! $value->crm == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('CRM') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!! $value->hrm == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('HRM') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!! $value->project == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('Project') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!!$value->pos == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('POS') }}</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check text-start">
                                    <label class="form-check-label"
                                        for="customCheckc1">{!! $value->chatgpt == 1 ? '<i class="ti ti-check"></i>' : '<i class="ti ti-x"></i>' !!}
                                        {{ __('ChatGPT') }}</label>
                                </div>
                            </li>

                        </ul>
                        <div class="d-grid">
                            <a href="{{ route('register') }}"
                                class="btn btn-primary rounded-pill">{{ __('Start with Starter') }} <i
                                    data-feather="log-in" class="ms-2"></i> </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>

        <div class="row justify-content-center">

            <div class="col-12" style="width: 75%;">
                <div class="card price-card shadow-none">
                    <div class="card-body">
                        <span class="price-badge bg-dark" style="padding-right:50px;padding-left:50px;font-size:25px;font-weight: 900;">{{ __("Integrated system for manufacturing and pricing") }}</span>
                        <div class="row">
                            <div class="col py-5">
                                <span class="mb-4 p-price" style="font-size: 50px;font-weight: 800;">{{__('Book a free consultation now')}}</span>
                                <h4 style="font-size: 26px;">{{ __("A comprehensive and complete system that includes professional pricing and manufacturing systems") }}</h4>
                            </div>

                        </div>


                        <div class="d-grid">
                            <a href="{{ route('register') }}" style="font-size: 25px;font-weight: 600;"
                                class="btn btn-primary rounded-pill">{{ __('Start with Starter') }} <i
                                    data-feather="log-in" class="ms-2"></i> </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endif
<!-- [ subscription ] end -->
<!-- [ FAqs ] start -->

@if ($settings['faq_status'] == 'on')
<section class="faqs section-gap bg-gray-100" id="faq">
    <div class="container">
        <div class="row mb-2">
            <div class="col-xxl-6">
                <div class="title mb-4">
                    <span class="d-block mb-2 fw-bold text-uppercase">{{ $settings['faq_title'] }}</span>
                    <h2 class="mb-4">{!! $settings['faq_heading'] !!}</h2>
                    <p>{!! $settings['faq_description'] !!}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    @if (is_array(json_decode($settings['faqs'], true)) || is_object(json_decode($settings['faqs'], true)))
                    @foreach (json_decode($settings['faqs'], true) as $key => $value)
                    @if ($key % 2 == 0)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="{{ 'flush-heading' . $key }}">
                            <button class="accordion-button collapsed fw-bold" type="button"
                                data-bs-toggle="collapse" data-bs-target="{{ '#flush-' . $key }}"
                                aria-expanded="false" aria-controls="{{ 'flush-collapse' . $key }}">
                                {!! $value['faq_questions'] !!}
                            </button>
                        </h2>
                        <div id="{{ 'flush-' . $key }}" class="accordion-collapse collapse"
                            aria-labelledby="{{ 'flush-heading' . $key }}"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                {!! $value['faq_answer'] !!}
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                    @endif

                </div>
            </div>
            <div class="col-md-6">
                <div class="accordion accordion-flush" id="accordionFlushExample2">
                    @if (is_array(json_decode($settings['faqs'], true)) || is_object(json_decode($settings['faqs'], true)))
                    @foreach (json_decode($settings['faqs'], true) as $key => $value)
                    @if ($key % 2 != 0)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="{{ 'flush-heading' . $key }}">
                            <button class="accordion-button collapsed fw-bold" type="button"
                                data-bs-toggle="collapse" data-bs-target="{{ '#flush-' . $key }}"
                                aria-expanded="false" aria-controls="{{ 'flush-collapse' . $key }}">
                                {!! $value['faq_questions'] !!}
                            </button>
                        </h2>
                        <div id="{{ 'flush-' . $key }}" class="accordion-collapse collapse"
                            aria-labelledby="{{ 'flush-heading' . $key }}"
                            data-bs-parent="#accordionFlushExample2">
                            <div class="accordion-body">
                                {!! $value['faq_answer'] !!}
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                    @endif


                </div>
            </div>

        </div>
    </div>
</section>
@endif
<!-- [ FAqs ] end -->
<!-- [ testimonial ] start -->
@if ($settings['testimonials_status'] == 'on' && $settings['testimonials'] !== "[]")
<section class="testimonial section-gap">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4">
                <div class="title mb-4">
                    <span class="d-block mb-2 fw-bold text-uppercase">{{ __('TESTIMONIALS') }}</span>
                    <h2 class="mb-2">{!! $settings['testimonials_heading'] !!}</h2>
                    <p>{!! $settings['testimonials_description'] !!}</p>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row justify-content-center gy-3">


                    @if (is_array(json_decode($settings['testimonials'])) || is_object(json_decode($settings['testimonials'])))
                    @foreach (json_decode($settings['testimonials']) as $key => $value)
                    <div class="col-xxl-4 col-sm-6 col-lg-6 col-md-4">
                        <div class="card bg-dark shadow-none mb-0">
                            <div class="card-body p-3">
                                <div class="d-flex mb-3 align-items-center justify-content-between">
                                    <span class="theme-avtar avtar avtar-sm bg-light-dark rounded-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36"
                                            height="23" viewBox="0 0 36 23" fill="none">
                                            <path
                                                d="M12.4728 22.6171H0.770508L10.6797 0.15625H18.2296L12.4728 22.6171ZM29.46 22.6171H17.7577L27.6669 0.15625H35.2168L29.46 22.6171Z"
                                                fill="white" />
                                        </svg>
                                    </span>
                                    <span>
                                        @for ($i = 1; $i <= (int) $value->testimonials_star; $i++)
                                            <i data-feather="star"></i>
                                            @endfor
                                    </span>
                                </div>
                                <h3 class="text-white">{{ $value->testimonials_title }}</h3>
                                <p class="hljs-comment">
                                    {{ $value->testimonials_description }}
                                </p>
                                <div class="d-flex  align-items-center ">
                                    <img src="{{ $logo . '/' . $value->testimonials_user_avtar }}"
                                        class="wid-40 rounded-circle me-3" alt="">
                                    <span>
                                        <b class="fw-bold d-block">{{ $value->testimonials_user }}</b>
                                        {{ $value->testimonials_designation }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif




                </div>
            </div>
            <div class="col-12">
                <p class="mb-0 f-w-600">
                    {!! $settings['testimonials_long_description'] !!}
                </p>
            </div>
        </div>
    </div>
</section>
@endif

<div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
    <div id="liveToast" class="toast text-white  fade" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"> </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- [ testimonial ] end -->
@endsection