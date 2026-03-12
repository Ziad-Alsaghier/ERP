@php


    $local = app()->getLocale();
    $logo = Utility::get_file('uploads/landing_page_image');
    $services = json_decode($settings['discover_of_features'], true);
    $countService = 0;
    $items = [
        [
            'name' => 'www.ihkam-erp.com',
            'img' => 'assets/parenter/par-1.jpg',
        ],
        [
            'name' => 'www.ihkam-erp.com',
            'img' => 'assets/parenter/par-2.jpg',
        ],
        [
            'name' => 'www.ihkam-erp.com',
            'img' => 'assets/parenter/par-3.jpg',
        ],
        [
            'name' => 'www.ihkam-erp.com',
            'img' => 'assets/parenter/par-4.jpg',
        ],
        [
            'name' => 'www.ihkam-erp.com',
            'img' => 'assets/parenter/par-5.jpg',
        ],
        [
            'name' => 'www.ihkam-erp.com',
            'img' => 'assets/parenter/par-6.jpg',
        ],
        [
            'name' => 'www.ihkam-erp.com',
            'img' => 'assets/parenter/par-7.jpg',
        ],
        [
            'name' => 'www.ihkam-erp.com',
            'img' => 'assets/parenter/par-8.jpg',
        ],
        [
            'name' => 'www.ihkam-erp.com',
            'img' => 'assets/parenter/par-9.jpg',
        ],
        [
            'name' => 'www.ihkam-erp.com',
            'img' => 'assets/parenter/par-10.jpg',
        ],
    ];
@endphp

@extends('landingpage::layouts.landingpage')
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        .video-demo {
            background-size: 22px;
            height: 500px;
            background: url('assets/img/hololVideoImage.jpg') no-repeat center center;
            background-position: center;
        }
    </style>
    {{-- <!-- Hero area %%%%%%%%%%%%%%%%%%%%%%%%%% --> --}}
    <section class="hero-saas">

        <div class="swiper hero-slider h-50">

            <div class="swiper-wrapper">

                {{-- Slide 1 --}}
                <div class="swiper-slide">

                    <div class="container">
                        <div class="row align-items-center">

                            <div class="col-lg-6">
                                <div class="hero-text">

                                    <h1>{{ __('landing.hero_business_title') }}</h1>

                                    <p>
                                        {{ __('landing.hero_business_description') }}
                                    </p>

                                    <div class="hero-buttons  mb-5 mb-lg-0">

                                        <a href="{{ route('custom.page', 'about_advanced_solutions_company_<the_future_erp>') }}"
                                            class="btn-primary me-4">
                                            {{ __('landing.hero_about_btn') }}
                                        </a>

                                        <a href="{{ route('meet.page') }}" class="btn-outline">
                                            {{ __('landing.hero_feedback_btn') }}
                                        </a>

                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="hero-image">
                                    <img src="{{ asset('assets/img/hero-slider-1.jpg') }}" class="img-fluid">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>


                {{-- Slide 2 --}}
                <div class="swiper-slide">

                    <div class="container">
                        <div class="row align-items-center">

                            <div class="col-lg-6">

                                <div class="hero-text">

                                    <h1>{{ __('landing.hero_markets_title') }}</h1>

                                    <p>
                                        {{ __('landing.hero_markets_description') }}
                                    </p>

                                    <div class="hero-buttons mb-5 mb-lg-0">

                                        <a href="{{ route('custom.page', 'about_advanced_solutions_company_<the_future_erp>') }}"
                                            class="btn-primary me-4">
                                            {{ __('landing.hero_about_btn') }}
                                        </a>

                                        <a href="{{ route('meet.page') }}" class="btn-outline">
                                            {{ __('landing.hero_feedback_btn') }}
                                        </a>

                                    </div>

                                </div>

                            </div>

                            <div class="col-lg-6">
                                <div class="hero-image">
                                    <img src="{{ asset('assets/img/hero-slider-2.png') }}" class="img-fluid">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>


                {{-- Slide 3 --}}
                <div class="swiper-slide">

                    <div class="container">
                        <div class="row align-items-center">

                            <div class="col-lg-6">

                                <div class="hero-text">

                                    <h1>{{ __('landing.hero_finance_title') }}</h1>

                                    <p>
                                        {{ __('landing.hero_finance_description') }}
                                    </p>

                                    <div class="hero-buttons mb-5 mb-lg-0">

                                        <a href="{{ route('custom.page', 'about_advanced_solutions_company_<the_future_erp>') }}"
                                            class="btn-primary me-4">
                                            {{ __('landing.hero_about_btn') }}
                                        </a>

                                        <a href="{{ route('meet.page') }}" class="btn-outline">
                                            {{ __('landing.hero_feedback_btn') }}
                                        </a>

                                    </div>

                                </div>

                            </div>

                            <div class="col-lg-6">
                                <div class="hero-image">
                                    <img src="{{ asset('assets/img/hero-slider-3.jpg') }}" class="img-fluid">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <div class="swiper-pagination"></div>

        </div>

    </section>

    {{-- <!-- End Hero area %%%%%%%%%%%%%%%%% --> --}}

    {{-- <!-- service area %%%%%%%%%%%%%%%%%%%%%%%% --> --}}
    <section id="services-section" class="service-area sec-pad py-5">
        <div class="container">
            <div class="row">

                <!-- Section Title -->
                <div class="col-md-12 col-lg-4">
                    <div class="title mb-4">
                        <span class="text-primary fw-bold">{{ __('what we do') }}</span>
                        <h2 class="mt-2">{{ __('we work performed for client happy.') }}</h2>
                        <div class="cmn-btn mt-4">
                            <a href="javascript:void(0);" id="view-more-btn" class="btn btn-outline-primary">
                                {{ __('view all services') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Services Grid -->
                <div class="col-md-12 col-lg-8">
                    <div class="row g-4">
                        @foreach (array_slice($services, 0, 4) as $index => $service)
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="single-service p-4 text-center shadow-sm rounded position-relative overflow-hidden">
                                    <span class="count position-absolute top-0 start-0 m-3 text-primary fs-4 fw-bold">
                                        {{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>

                                    <div class="service-icon mb-3">
                                        <img src="{{ $logo . '/' . $service['discover_logo'] }}" alt=""
                                            class="img-fluid service-icon-img">
                                    </div>

                                    <div class="service-content">
                                        <h4 class="fw-bold">{{ __($service['discover_heading']) }}</h4>
                                        <p class="text-muted">
                                            {{ Str::limit(__($service['discover_description']), 100) }}
                                        </p>
                                        <a class="btn-show-more d-inline-flex align-items-center mt-2">
                                            {{ __('read more') }}
                                            <i class="ms-2">
                                                <img src="assets/img/icons/arrow-circle.png" alt="" class="img-fluid">
                                            </i>
                                        </a>
                                    </div>

                                    <!-- Hover Overlay -->
                                    <div class="service-overlay"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- More Services -->
                    <div class="row g-4 mt-4 d-none" id="more-services">
                        @foreach (array_slice($services, 4) as $index => $service)
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="single-service p-4 text-center shadow-sm rounded position-relative overflow-hidden">
                                    <span class="count position-absolute top-0 start-0 m-3 text-primary fs-4 fw-bold">
                                        {{ str_pad($index + 5, 2, '0', STR_PAD_LEFT) }}
                                    </span>

                                    <div class="service-icon mb-3">
                                        <img src="{{ $logo . '/' . $service['discover_logo'] }}" alt=""
                                            class="img-fluid service-icon-img">
                                    </div>

                                    <div class="service-content">
                                        <h4 class="fw-bold">{{ __($service['discover_heading']) }}</h4>
                                        <p class="text-muted">
                                            {{ Str::limit(__($service['discover_description']), 100) }}
                                        </p>
                                        <a class="btn-show-more d-inline-flex align-items-center mt-2">
                                            {{ __('read more') }}
                                            <i class="ms-2">
                                                <img src="assets/img/icons/arrow-circle.png" alt="" class="img-fluid">
                                            </i>
                                        </a>
                                    </div>

                                    <div class="service-overlay"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- js -->
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Select all <p> elements with 'data-full' and all 'Read more' buttons
                const paragraphs = document.querySelectorAll('p[data-full]');
                const btnShowMore = document.querySelectorAll('.btn-show-more');

                // Define the translated strings in JavaScript
                const readMoreText = "{{ __('read more') }}";
                const readLessText = "{{ __('read less') }}";
                const readMoreService = "{{ __('view all services') }}";
                const readLessService = "{{ __('view less') }}";

                // Loop through each button and add an event listener
                btnShowMore.forEach((btn, index) => {
                    let isExpanded = false; // Track if the paragraph is expanded

                    btn.addEventListener('click', function () {
                        const fullText = paragraphs[index].dataset.full;
                        const truncatedText = fullText.slice(0, 100); // Truncate text to 100 characters

                        // Toggle between expanded and collapsed states
                        if (isExpanded) {
                            paragraphs[index].textContent = truncatedText +
                                '...'; // Show truncated text
                            btn.textContent = readMoreText; // Change button text back to 'Read more'
                        } else {
                            paragraphs[index].textContent = fullText; // Show full text
                            btn.textContent = readLessText; // Change button text to 'Read less'
                        }

                        isExpanded = !isExpanded; // Toggle the expanded state
                    });
                });

                // View more services functionality
                const viewMoreBtn = document.getElementById('view-more-btn');
                const moreServices = document.getElementById('more-services');

                if (viewMoreBtn && moreServices) { // Check if elements exist
                    viewMoreBtn.addEventListener('click', function () {
                        moreServices.classList.toggle('d-none');

                        // Change button text based on the visibility of more services
                        if (moreServices.classList.contains('d-none')) {
                            this.textContent = readMoreService; // Use the translated string
                        } else {
                            this.textContent = readLessService; // Use the translated string
                        }
                    });
                } else {
                    console.error('View more button or more services element not found');
                }
            });
        </script>
    @endpush
    <!-- End Service area %%%%%%%%%%%%%%%%%%%%%% -->


    <!-- plan price %%%%%%%%%%%%%%%%%%%% -->

    @if ($settings['plan_status'])
        <section class="pricing-plan sec-mar">
            <div class="container">

                <div class="row justify-content-between gap-3 flex-column align-items-center">

                    <div class="col-12 col-lg-12 col-xl-12 or1">
                        <div class="title black">
                            <span class="pricing_plan">{{ __('Pricing Plan') }}</span>
                            <h2>{{ __('Join Now For Your Business.') }}</h2>
                        </div>
                    </div>
                    @if ($local == 'ar')
                        <div class="col-12 col-lg-12 col-xl-12 or1">
                            <img src="assets/img/land-price-banner.jpg" alt="Land price banner without any expenses">
                        </div>
                    @else
                        <div class="col-12 col-lg-12 col-xl-12 or1">
                            <img src="assets/img/land-price-banner-en.jpg" alt="Land price banner without any expenses">
                        </div>
                    @endif
                    <div class="mt-4 col-12 col-lg-12 col-xl-12 or2 ">
                        <ul class="nav nav-pills gap-3 mb-3 justify-content-center" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                                    aria-selected="true">{{ __('Pay Monthly') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
                                    aria-selected="false">{{ __('Pay Yearly') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="currency-switch" data-bs-toggle="pill"
                                    data-bs-target="#pills-currency" type="button" role="tab" aria-controls="pills-profile"
                                    aria-selected="false">
                                    {{ __('EGP') }}
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content" id="pills-tabContent">
                    @php
                        $collection = \App\Models\Plan::orderBy('price', 'ASC')->get();
                        $admin_payment_setting = Utility::settings();
                        $data = [
                            'rates' => ['EGP' => 53.5],
                        ];
                        // Assume $data is passed to the Blade view
                        $rates = $data['rates'];
                        // Get the keys of the rates array
                        $currencyCodes = array_keys($rates);
                    @endphp
                    <!-- monthly -->
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <div class="row ">
                            @foreach ($collection as $key => $value)
                                @if ($value->duration == 'month' && $value->is_visible == 1)
                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                        <div class="single-price-box">
                                            <h3>{{ __($value->name) }}</h3>
                                            <div style="min-height:120px">
                                                <span class="description" style="min-height:60px; font-size:18px;"
                                                    data-full-text="{!! __($value->description) !!}">
                                                    {{ Str::limit(__($value->description), 70) }}
                                                </span>
                                                <div class="toggle-btn bg-transparent mt-1"
                                                    style="display: none; font-size:14px; cursor:pointer; color:#0061ae;"
                                                    onclick="toggleDescription(this)">{{ __('read more') }}</div>

                                            </div>
                                            <h2 class="dollar">
                                                {{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '$' }}
                                                {{ intval($value->price) }}
                                                {{-- /<sub>{{ __($value->duration) }}</sub> --}}
                                            </h2>
                                            @foreach ($rates as $currencyCode => $rate)
                                                <h2 class="pound-egy d-none">
                                                    {{ $currencyCode }} {{ intval($value->price * $data['rates']['EGP'] + 5) }}
                                                    {{-- /<sub>{{ __($value->duration) }}</sub> --}}
                                                </h2>
                                            @endforeach
                                            <ul class="feature-list">
                                                <li><i
                                                        class="fas fa-check"></i>{{ $value->max_users == -1 ? __('Unlimited') : $value->max_users }}
                                                    {{ __('User') }}
                                                </li>
                                                <li><i class="fas fa-check"></i>
                                                    {{ $value->max_customers == -1 ? __('Unlimited') : $value->max_customers }}
                                                    {{ __('Customer') }}
                                                </li>
                                                <li><i class="fas fa-check"></i>
                                                    {{ $value->max_venders == -1 ? __('Unlimited') : $value->max_venders }}
                                                    {{ __('Vendors') }}
                                                </li>
                                                <li><i class="fas fa-check"></i>
                                                    {{ $value->max_clients == -1 ? __('Unlimited') : $value->max_clients }}
                                                    {{ __('Clients') }}
                                                </li>
                                                <li>
                                                    {!! $value->account == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('Account') }}
                                                </li>
                                                <li>
                                                    {!! $value->crm == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('CRM') }}
                                                </li>
                                                <li>
                                                    {!! $value->hrm == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('HRM') }}
                                                </li>
                                                <li>
                                                    {!! $value->project == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('Project') }}
                                                </li>

                                                <li>
                                                    {!! $value->pos == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('POS') }}
                                                </li>

                                                <li>
                                                    {!! $value->chatgpt == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('ChatGPT') }}
                                                </li>


                                            </ul>
                                            <div class="pay-btn">
                                                <a href="{{ route('register') }}">
                                                    {{ __('Subscribe Now') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    </div>



                    <!-- yearly -->
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <div class="row">
                            @foreach ($collection as $key => $value)
                                @if ($value->duration == 'year' && $value->is_visible == 1)
                                    <div class="col-md-6 col-lg-4 col-xl-4 mt-3">
                                        <div class="single-price-box">
                                            <h3>{{ __($value->name) }}</h3>
                                            <div style="min-height:120px">
                                                <span class="description" style="min-height:60px; height:auto; font-size:18px;"
                                                    data-full-text="{!! __($value->description) !!}">
                                                    {{ Str::limit(__($value->description), 70) }}

                                                </span>
                                                <div class="toggle-btn bg-transparent mt-1"
                                                    style="display: none; font-size:14px; cursor:pointer; color:#0061ae;"
                                                    onclick="toggleDescription(this)">{{ __('read more') }}</div>

                                            </div>
                                            <h2 class="dollar d-none">
                                                {{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '$' }}
                                                {{ intval($value->price) }}
                                                {{-- /<sub>{{ __($value->duration) }}</sub> --}}
                                            </h2>
                                            @foreach ($rates as $currencyCode => $rate)
                                                <h2 class="pound-egy">
                                                    {{ $currencyCode }} {{ intval($value->price * $data['rates']['EGP'] + 5) }}
                                                    {{-- /<sub>{{ __($value->duration) }}</sub> --}}
                                                </h2>
                                            @endforeach
                                            <ul class="feature-list">
                                                <li><i
                                                        class="fas fa-check"></i>{{ $value->max_users == -1 ? __('Unlimited') : $value->max_users }}
                                                    {{ __('User') }}
                                                </li>
                                                <li><i class="fas fa-check"></i>
                                                    {{ $value->max_customers == -1 ? __('Unlimited') : $value->max_customers }}
                                                    {{ __('Customer') }}
                                                </li>
                                                <li><i class="fas fa-check"></i>
                                                    {{ $value->max_venders == -1 ? __('Unlimited') : $value->max_venders }}
                                                    {{ __('Vendors') }}
                                                </li>
                                                <li><i class="fas fa-check"></i>
                                                    {{ $value->max_clients == -1 ? __('Unlimited') : $value->max_clients }}
                                                    {{ __('Clients') }}
                                                </li>
                                                <li>
                                                    {!! $value->account == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('Account') }}
                                                </li>
                                                <li>
                                                    {!! $value->crm == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('CRM') }}
                                                </li>
                                                <li>
                                                    {!! $value->hrm == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('HRM') }}
                                                </li>
                                                <li>
                                                    {!! $value->project == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('Project') }}
                                                </li>

                                                <li>
                                                    {!! $value->pos == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('POS') }}
                                                </li>

                                                <li>
                                                    {!! $value->chatgpt == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>' !!}
                                                    {{ __('ChatGPT') }}
                                                </li>


                                            </ul>
                                            <div class="pay-btn">
                                                <a href="{{ route('register') }}">
                                                    {{ __('Subscribe Now') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif



    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const maxLength = 70;
            const descriptionElements = document.querySelectorAll('.description');

            descriptionElements.forEach(function (descriptionElement) {
                const fullText = descriptionElement.getAttribute('data-full-text');
                const toggleBtn = descriptionElement.nextElementSibling;

                if (fullText.length > maxLength) {
                    descriptionElement.textContent = fullText.slice(0, maxLength) + '...';
                    toggleBtn.style.display = 'block'; // Show the button if text exceeds 123 characters
                } else {
                    descriptionElement.textContent = fullText; // Show full text if it's under the limit
                }
            });
        });

        const moreText = "{{ __('read more') }}";
        const lessText = "{{ __('read less') }}";

        function toggleDescription(button) {
            const descriptionElement = button.previousElementSibling;
            const fullText = descriptionElement.getAttribute('data-full-text');
            const maxLength = 70;

            if (button.textContent === moreText) {
                descriptionElement.textContent = fullText;
                descriptionElement.style.height = 'auto';
                button.textContent = lessText;
            } else {
                descriptionElement.textContent = fullText.slice(0, maxLength) + '...';
                button.textContent = moreText;
            }
        }
    </script>
    <!-- end plan section -->


    <!-- About Area %%%%%%%%%%%%%%%%%%%%% -->
    <section id="about-us-section" class="about-area sec-mar">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-xl-6 mb-5 mb-lg-0">
                    <div class="about-left">
                        <div class="title black">
                            <span>{{ __('About us') }}</span>
                            <h2 class="mb-15">{{ __('Direction with our company.') }}</h2>
                        </div>
                        <div class="our_copany">
                            {{ __('Our company') }}
                            <div class="bg-gray-100 py-12">
                                <div class="max-w-6xl mx-auto px-6">
                                    <ul class="">
                                        {{-- Service One --}}
                                        <li
                                            class="flex items-start space-x-4 bg-white shadow-md rounded-lg p-4 border-l-4 border-[#004f86]">
                                            <div class="row">
                                                <div class="col-2">
                                                    <div class="text-[#004f86] text-2xl position-relative">
                                                        <span
                                                            style="font-size: 90px; position: absolute; left: -28px; top: -12px;">🔍</span>
                                                        {{-- Replace with an actual icon --}}
                                                    </div>
                                                </div>
                                                <div class="col-10">
                                                    <h3 class="text-xl font-semibold text-[#004f86]">{{ __('vision') }}</h3>
                                                    <p class="text-gray-700 mt-1">
                                                        <span x-data="{ open: false }">
                                                            <span x-show="!open">
                                                                {{__("To become the leading partner for companies and factories in their journey towards digital transformation by providing the world's first system that enables the highest...")}}
                                                            </span>
                                                            <span x-show="open">
                                                                {{__("levels of efficiency and integration across various departments within organizations, supporting their sustainable growth in a dynamic and ever-changing market. We aspire for the solutions we offer to be a benchmark for quality and innovation in business management across all forms.")}}
                                                            </span>
                                                            <a @click="open = !open" class="text-blue-500  underline"
                                                                style="cursor:pointer;">
                                                                <span x-show="!open">{{ __('Read More') }}</span>
                                                                <span x-show="open">{{ __('Read Less') }}</span>
                                                            </a>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>

                                        {{-- Service Two --}}
                                        <li
                                            class="flex items-start space-x-4 bg-white shadow-md rounded-lg p-4 border-l-4 border-[#004f86]">
                                            <div class="row">
                                                <div class="col-2">
                                                    <div class="text-[#004f86] text-2xl position-relative">
                                                        <span
                                                            style="font-size: 90px; position: absolute; left: -28px; top: -12px;">
                                                            🎯 </span>{{-- Replace with an actual icon --}}
                                                    </div>
                                                </div>
                                                <div class="col-10">
                                                    <h3 class="text-xl font-semibold text-[#004f86]">{{ __('goal') }}</h3>
                                                    <p class="text-gray-700 mt-1">
                                                        <span x-data="{ open: false }">
                                                            <span x-show="!open">
                                                                {{__("We aim to empower companies to achieve the highest levels of efficiency and effectiveness...")}}
                                                            </span>
                                                            <span x-show="open">
                                                                {{__("increase their business volume, and accommodate that growth. Therefore, we continuously strive to develop the services we offer and improve business processes by integrating all company departments into a single platform. This enhances collaboration, facilitates data-driven decision-making, and supports sustainable growth. We are committed to providing tailored solutions that help companies achieve their strategic goals and overcome the challenges of an ever-changing market.")}}
                                                            </span>
                                                            <a @click="open = !open" class="text-blue-500  underline"
                                                                style="cursor:pointer;">
                                                                <span x-show="!open">{{ __('Read More') }}</span>
                                                                <span x-show="open">{{ __('Read Less') }}</span>
                                                            </a>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>

                                        </li>

                                        {{-- Service Three --}}
                                        <li
                                            class="flex items-start space-x-4 bg-white shadow-md rounded-lg p-4 border-l-4 border-[#004f86]">
                                            <div class="row">
                                                <div class="col-2">
                                                    <div class="text-[#004f86] text-2xl position-relative">
                                                        <span
                                                            style="font-size: 90px; position: absolute; left: -28px; top: -12px;">
                                                            📜</span> {{-- Replace with an actual icon --}}
                                                    </div>
                                                </div>
                                                <div class="col-10">
                                                    <h3 class="text-xl font-semibold text-[#004f86]">{{ __('mission') }}
                                                    </h3>
                                                    <p class="text-gray-700 mt-1">
                                                        <span x-data="{ open: false }">
                                                            <span x-show="!open">
                                                                {{ __("Achieving the concept of sustainability by reducing the use of paper and ink in written reports and replacing them with electronic reports...") }}
                                                            </span>
                                                            <span x-show="open">
                                                                {{ __("Communicating in the language of the modern era using the latest software tools and technologies to serve companies and factories, helping them accelerate and simplify sales processes and cost calculations across businesses of all sizes at minimal costs. Establishing a new concept of automation as the world's new vision for relying on technology by executing all operations within an organization or company through automated software systems that connect all departments practically and administratively to facilitate their work. Reducing industrial and administrative costs by controlling the accounting of input and output in operations and manufacturing across all production stages, ensuring precise cost calculations in real-time, and achieving the objectives set by top management.") }}
                                                            </span>
                                                            <a @click="open = !open" class="text-blue-500  underline"
                                                                style="cursor:pointer;">
                                                                <span x-show="!open">{{ __('Read More') }}</span>
                                                                <span x-show="open">{{ __('Read Less') }}</span>
                                                            </a>
                                                        </span>
                                                    </p>

                                                </div>
                                            </div>


                                        </li>
                                    </ul>
                                </div>
                            </div>



                        </div>
                        <div class="cmn-btn text-center ">
                            <a
                                href="{{ route('custom.page', 'about_advanced_solutions_company_<the_future_erp>') }}">{{ __('Learn more about us') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6">
                    <div class="about-right">
                        <div class="group-images">
                            <img src="assets/img/team.jpg" alt />

                            <div class="row pt-5">
                                <div class="col-2">
                                    <div class="msn-icon">
                                        <i><img src="assets/img/icons/mission-icon.png" alt /></i>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <h5>{{ __('Our Mission') }}</h5>
                                    <p>{{ __('Our mission') }}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="features-count">
                <div class="row">
                    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                        <div class="single-count">
                            <i><img src="assets/img/icons/count-2.png" alt /></i>
                            <div class="counter">
                                <span class="odometer">250</span><sup>+</sup>
                            </div>
                            <p>
                                {{ __('SatisfiedClients') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                        <div class="single-count">
                            <i><img src="assets/img/icons/count-3.png" alt /></i>
                            <div class="counter">
                                <span class="odometer">150</span><sup>+</sup>
                            </div>
                            <p>
                                {{ __('ExpertTeams') }}
                            </p>
                        </div>
                    </div>
                    <!-- <div class="col-sm-6 col-md-3 col-lg-3 col-xl-3">
                                          <div class="single-count xsm">
                                            <i><img src="assets/img/icons/count-4.png" alt /></i>
                                            <div class="counter">
                                              <span class="odometer">28</span><sup>+</sup>
                                            </div>
                                            <p>
                                              {{ __('Win Awards') }}
                                            </p>
                                          </div>
                                        </div> -->
                </div>
            </div>
        </div>
    </section>
    <!-- End About Area %%%%%%%%%%%%%%%% -->

    <!-- parenter area %%%%%%%%%%%%%%%% -->
    <section class="our-partner">
        <div class="container-fluid g-0 overflow-hidden">
            <div class="row align-items-center g-0">
                <div class="col-12 col-xl-6">
                    <div class="newsletter">
                        <div class="subscribes">
                            <span>
                                {{ __('Get In Touch') }}
                            </span>
                            <h1>
                                {{ __('Subscribe Our') }}
                            </h1>
                            <h2>
                                {{ __('Newsletter') }}
                            </h2>
                            <div class="subscribe-form">
                                <form action="{{ route('join_us_store') }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <input type="email" name="email" placeholder="{{ __('Type Your Email') }}" />
                                    <input type="submit" value="{{ __('Connect') }}" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="our-clients">
                        <div class="row align-items-center">
                            <div class="col-md-6 col-lg-4 col-xl-6">
                                <div class="title">
                                    <span>
                                        {{ __('Our partner') }}
                                    </span>
                                    <h2>
                                        {{ __('Join our Holol community.') }}
                                    </h2>
                                </div>
                            </div>
                            @foreach ($items as $item)
                                <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                                    <div class="single-client">
                                        <img class="w-100 h-100" src="{{ asset($item['img']) }}" alt />
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End parenter Area %%%%%%%%%%%%%% -->

    <!-- why choice us %%%%%%%%%%%%%%% -->
    {{-- <section class="why-choose-us sec-mar">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-12 col-xl-12">
                    <div class="title black">
                        <span class="pricing_plan">
                            {{ __('Why Choose Us') }}
                        </span>
                        <h2 class="mb-15">
                            {{ __('Success awaits you with the Ihkam System') }}
                        </h2>
                    </div>
                    <div class="video-demo" style="height: 500px;">

                        <div class="play-btn">
                            <a class="popup-video" href="{{ asset('public/assets/futureerp.mp4') }}"><i
                                    class="fas fa-play"></i>
                                {{ __('Play now') }}
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- End Why Choice us %%%%%%%%%%%%%% -->
    <!-- Let Talk %%%%%%%%%%%%%%%% -->
    <section class="lets-talk sec-pad mt-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-9 col-lg-8 col-xl-8">
                    <div class="title special">
                        <span>
                            {{ __('Let’s Talk') }}
                        </span>
                        <h2>{{ __('About Your Next') }} <b>{{ __('Projectk') }}</b> {{ __('Your Mind') }}</h2>
                    </div>
                </div>
                <div class="col-md-3 col-lg-4 col-xl-4 text-end">
                    <div class="getin-touch">
                        <div class="cmn-btn">
                            <a href="contact"
                                style="width: 346px;text-align: center;font-size: 42px;font-weight: 800;">{{ __('Get In Touch') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Let Talk %%%%%%%%%%%%%%%%%% -->
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const usdText = "{{ __('USD') }}"; // Wrap in quotes
            const egpText = "{{ __('EGP') }}"; // Wrap in quotes
            const switchCurrency = document.getElementById("currency-switch");
            const USDprice = document.querySelectorAll(".dollar");
            const EGPprice = document.querySelectorAll(".pound-egy");

            function toggleCurrency() {
                if (switchCurrency.textContent.trim() === "USD") {
                    // Switch to EGP
                    switchCurrency.textContent = egpText;

                    // Show EGP prices and hide USD prices
                    EGPprice.forEach(function (el) {
                        el.classList.remove("d-none"); // Show EGP prices
                    });
                    USDprice.forEach(function (el) {
                        el.classList.add("d-none"); // Hide USD prices
                    });
                } else {
                    // Switch to USD
                    switchCurrency.textContent = usdText;

                    // Show USD prices and hide EGP prices
                    EGPprice.forEach(function (el) {
                        el.classList.add("d-none"); // Hide EGP prices
                    });
                    USDprice.forEach(function (el) {
                        el.classList.remove("d-none"); // Show USD prices
                    });
                }
            }

            switchCurrency.addEventListener("click", toggleCurrency);
        });
    </script>
@endpush