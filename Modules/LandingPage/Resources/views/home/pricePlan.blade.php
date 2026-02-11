@extends('landingpage::layouts.landingpage')
@section('content')
<section class="breadcrumbs">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="breadcrumb-wrapper">
                <h1>Pricing Plans</h1>
                <ul>
                  <li><a href="index.html">Home</a></li>
                  <li>Pricing Plans</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>
@if ($settings['plan_status'])
<section class="pricing-plan sec-mar">
    <div class="container">

        <div class="row justify-content-between align-items-center">
            <div class="col-12 col-lg-6 col-xl-5 or2">
                <ul class="nav nav-pills gap-3 mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Pay Monthly</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Pay Yearly</button>
                    </li>
                    <li class="nav-item" >
                        <button class="nav-link" id="currency-switch" data-bs-toggle="pill" data-bs-target="#pills-currency" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">USD</button>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-lg-6 col-xl-5 or1">
                <div class="title black">
                    <span>Pricing Plan</span>
                    <h2>Join Now For Your Business.</h2>
                </div>
            </div>
        </div>
        <div class="tab-content" id="pills-tabContent">
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
            <!-- monthly -->
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <div class="row">
                    @foreach ($collection as $key => $value)
                    @if ($value->duration == "month" && $value->is_visible == 1)
                    <div class="col-md-6 col-lg-4 col-xl-4">
                        <div class="single-price-box">
                            <h3>{{ $value->name }}</h3>
                            <span style="height: 60px;" > {!! $value->description !!}</span>
                            <h2 class="dollar d-none">{{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '$' }}{{ intval($value->price) }}/<sub>{{ $value->duration }}</sub></h2>
                            @foreach ($rates as $currencyCode => $rate)
                            <h2 class="pound-egy">{{ $currencyCode }}{{ intval($value->price * $data['rates']['EGP'] + 5) }}/<sub>{{ $value->duration }}</sub></h2>

                            @endforeach
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>{{ $value->max_users == -1 ? 'Unlimited' : $value->max_users }}
                                    {{ __('User') }}
                                </li>
                                <li><i class="fas fa-check"></i>
                                {{ $value->max_customers == -1 ? 'Unlimited' : $value->max_customers }}
                                {{ __('Customer') }}
                            </li>
                                <li><i class="fas fa-check"></i>
                                {{ $value->max_venders == -1 ? 'Unlimited' : $value->max_venders }}
                                {{ __('Vendors') }}
                            </li>
                                <li><i class="fas fa-check"></i>
                                {{ $value->max_clients == -1 ? 'Unlimited' : $value->max_clients }}
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
                                <a href="{{ route('register') }}">Pay Now</a>
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
                    @if ($value->duration == "year" && $value->is_visible == 1)
                    <div class="col-md-6 col-lg-4 col-xl-4">
                        <div class="single-price-box">
                            <h3>{{ $value->name }}</h3>
                            <span style="height: 60px;" > {!! $value->description !!}</span>
                            <h2 class="dollar d-none">{{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '$' }}{{ intval($value->price) }}/<sub>{{ $value->duration }}</sub></h2>
                            @foreach ($rates as $currencyCode => $rate)
                            <h2 class="pound-egy">{{ $currencyCode }}{{ intval($value->price * $data['rates']['EGP'] + 5) }}/<sub>{{ $value->duration }}</sub></h2>

                            @endforeach
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i>{{ $value->max_users == -1 ? 'Unlimited' : $value->max_users }}
                                    {{ __('User') }}
                                </li>
                                <li><i class="fas fa-check"></i>
                                {{ $value->max_customers == -1 ? 'Unlimited' : $value->max_customers }}
                                {{ __('Customer') }}
                            </li>
                                <li><i class="fas fa-check"></i>
                                {{ $value->max_venders == -1 ? 'Unlimited' : $value->max_venders }}
                                {{ __('Vendors') }}
                            </li>
                                <li><i class="fas fa-check"></i>
                                {{ $value->max_clients == -1 ? 'Unlimited' : $value->max_clients }}
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
                                <a href="{{ route('register') }}">Pay Now</a>
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
@endsection


@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var switchCurrency = document.getElementById("currency-switch"),
            USDprice = document.querySelectorAll(".dollar"),
            EGPprice = document.querySelectorAll(".pound-egy");

        function toggleCurrency() {
            console.log('hi')
            if (switchCurrency.textContent.trim() === "USD") {
                // Switch to EGP
                switchCurrency.textContent = "EGP";

                // Show EGP prices and hide USD prices
                EGPprice.forEach(function(el) {
                    el.classList.add("d-none");
                });
                USDprice.forEach(function(el) {
                    el.classList.remove("d-none");
                });
            } else {
                // Switch to USD
                switchCurrency.textContent = "USD";

                // Show USD prices and hide EGP prices
                EGPprice.forEach(function(el) {
                    el.classList.remove("d-none");
                });
                USDprice.forEach(function(el) {
                    el.classList.add("d-none");
                });
            }
        }

        switchCurrency.addEventListener("click", toggleCurrency);
    });
</script>
@endpush