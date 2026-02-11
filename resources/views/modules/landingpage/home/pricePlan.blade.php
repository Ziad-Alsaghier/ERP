@php
$features = [
[
'id' => 1,
'icon' => 'assets/img/users.svg',
'name' => __('Users'),
'description' => __('Number of Additional System Users'),
'price' => '600',
'billingPeriod' => __('User / Year')
],
[
'id' => 2,
'icon' => 'assets/img/payroll.svg',
'name' => __('Payroll'),
'description' => __('Number of active employees in HR'),
'price' => '200',
'billingPeriod' => __('Employee / Year')
],
[
'id' => 3,
'icon' => 'assets/img/locations.svg',
'name' => __('Locations'),
'description' => __('Number of Additional Locations'),
'price' => '200',
'billingPeriod' => __('Location / Year')
],
[
'id' => 4,
'icon' => 'assets/img/pos.svg',
'name' => __('POS'),
'description' => __('Number of active POS users'),
'price' => '200',
'billingPeriod' => __('User / Year')
]
];
@endphp

@extends('landingpage::layouts.landingpage')
@section('content')
<!-- <section class="breadcrumbs">
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
      </section> -->
@if ($settings['plan_status'])
<section class="pricing-plan sec-mar">
  <div class="container">

    <div class="row justify-content-between gap-3 flex-column align-items-center">

      <div class="col-12 col-lg-12 col-xl-12 or1">
        <div class="title black">
          <span>{{ __('Pricing Plan') }}</span>
          <h2>{{__('Join Now For Your Business.')}}</h2>
        </div>
      </div>
      <div class="col-12 col-lg-12 col-xl-12 or1">
        <img src="assets/img/land-price-banner.jpg" alt="Land price banner without any expenses">
      </div>
      <div class="mt-4 col-12 col-lg-12 col-xl-12 or2 ">
        <ul class="nav nav-pills gap-3 mb-3 justify-content-center" id="pills-tab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">{{__('Pay Monthly')}}</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">{{__('Pay Yearly')}}</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" id="currency-switch" data-bs-toggle="pill" data-bs-target="#pills-currency" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
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
      'rates' => ['EGP' =>48.91],
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
          @if ($value->duration == "month" && $value->is_visible == 1)
          <div class="col-md-6 col-lg-4 col-xl-4">
            <div class="single-price-box">
              <h3>{{ __($value->name) }}</h3>
              <div style="min-height:120px">
                <span class="description" style="min-height:60px; height:100px; font-size:18px;" data-full-text="{!! __($value->description) !!}">
                  {{ Str::limit(__($value->description), 70)  }}

                </span>
                <div class="toggle-btn bg-transparent mt-1" style="display: none; font-size:14px; cursor:pointer; color:#0061ae;" onclick="toggleDescription(this)">{{__('read more')}}</div>

              </div>
              <h2 class="dollar">{{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '$' }}{{ intval($value->price) }}/<sub>{{__( $value->duration) }}</sub></h2>
              @foreach ($rates as $currencyCode => $rate)
              <h2 class="pound-egy d-none">{{ $currencyCode }}{{ intval($value->price * $data['rates']['EGP'] + 5) }}/<sub>{{ __($value->duration) }}</sub></h2>
              @endforeach
              <ul class="feature-list">
                <li><i class="fas fa-check"></i>{{ $value->max_users == -1 ? __('Unlimited') : $value->max_users }}
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
          @if ($value->duration == "year" && $value->is_visible == 1)
          <div class="col-md-6 col-lg-4 col-xl-4 mt-3">
            <div class="single-price-box">
              <h3>{{ __($value->name) }}</h3>
              <div style="min-height:120px">
                <span class="description" style="min-height:60px; height:auto; font-size:18px;" data-full-text="{!! __($value->description) !!}">
                  {{ Str::limit(__($value->description),70)  }}

                </span>
                <div class="toggle-btn bg-transparent mt-1" style="display: none; font-size:14px; cursor:pointer; color:#0061ae;" onclick="toggleDescription(this)">{{__('read more')}}</div>

              </div>
              <h2 class="dollar d-none">{{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '$' }}{{ intval($value->price) }}/<sub>{{ __($value->duration) }}</sub></h2>
              @foreach ($rates as $currencyCode => $rate)
              <h2 class="pound-egy">{{ $currencyCode }}{{ intval($value->price * $data['rates']['EGP'] + 5) }}/<sub>{{ __($value->duration) }}</sub></h2>
              @endforeach
              <ul class="feature-list">
                <li><i class="fas fa-check"></i>{{ $value->max_users == -1 ? __('Unlimited') : $value->max_users }}
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


    <!-- new section for features -->

    <div class="row mt-5">
      <div class="col-12 text-center">
        <h3>
          {{ __('Holol Oddons') }}
        </h3>
      </div>
      @foreach($features as $feature)
      @include('modules.landingpage.home.addfeature', [
      'id' => $feature['id'],
      'icon' => $feature['icon'],
      'name' => $feature['name'],
      'description' => $feature['description'],
      'price' => $feature['price'],
      'billingPeriod' => $feature['billingPeriod']
      ])
      @endforeach

      <div class="col-12 text-center mt-5">
        <button class="btn  px-5 py-2 bg-black text-light">
          {{ __('Continue') }}
        </button>
      </div>

    </div>
  </div>
</section>
@endif
@endsection



@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const maxLength = 70;
    const descriptionElements = document.querySelectorAll('.description');

    descriptionElements.forEach(function(descriptionElement) {
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
@endpush


@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function() {
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
        EGPprice.forEach(function(el) {
          el.classList.remove("d-none"); // Show EGP prices
        });
        USDprice.forEach(function(el) {
          el.classList.add("d-none"); // Hide USD prices
        });
      } else {
        // Switch to USD
        switchCurrency.textContent = usdText;

        // Show USD prices and hide EGP prices
        EGPprice.forEach(function(el) {
          el.classList.add("d-none"); // Hide EGP prices
        });
        USDprice.forEach(function(el) {
          el.classList.remove("d-none"); // Show USD prices
        });
      }
    }

    switchCurrency.addEventListener("click", toggleCurrency);
  });
</script>

@endpush