@php
    $languages = \App\Models\Utility::languages();
    if (\Auth::user() == null) {
        $locale = Session::get('locale');
    } else {
        $locale = App::getLocale();
    }

    if ($languages->has($locale)) {
        $LangName = $languages->get($locale);
    } else {
        $LangName = '';
    }
    $pages = json_decode($settings['menubar_page'], true);
    $logo = Utility::get_file('uploads/landing_page_image');
@endphp

<!-- new Landing Page 3 / 9 -->

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <title>{{ env('APP_NAME') }}</title>
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta name="title" content="{{ $metatitle }}">
    <meta name="description" content="{{ $metsdesc }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $metatitle }}">
    <meta property="og:description" content="{{ $metsdesc }}">
    <meta property="og:image" content="{{ $meta_image . $meta_logo }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $metatitle }}">
    <meta property="twitter:description" content="{{ $metsdesc }}">
    <meta property="twitter:image" content="{{ $meta_image . $meta_logo }}">

    <!-- Favicon icon -->
    <link rel="icon" href="{{ $sup_logo . '/' . $adminSettings['company_favicon'] }}" type="image/x-icon" />

    <!-- LandingPage Module CSS -->
    <link href="{{ Module::asset('LandingPage:Resources/assets/holol/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/assets/holol/css/bootstrap-icons.css') }}" />
    <link href="{{ Module::asset('LandingPage:Resources/assets/holol/css/all.min.css') }}" rel="stylesheet" />
    <link href="{{ Module::asset('LandingPage:Resources/assets/holol/css/fontawesome.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/assets/holol/css/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/assets/holol/css/lightbox.min.css') }}" />
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/assets/holol/css/nice-select.css') }}" />
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/assets/holol/css/jQuery-plugin-progressbar.css') }}" />
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/assets/holol/css/barfiller.css') }}" />
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/assets/holol/css/magnific-popup.css') }}" />
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/assets/holol/css/style.css') }}" />

    {{-- fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap"
        rel="stylesheet">

    <link id="stylesheet" rel="stylesheet"
        href="{{ Module::asset('LandingPage:Resources/assets/holol/css/style.css') }}" />
    @if (App::isLocale('ar'))
        <link id="stylesheet" rel="stylesheet"
            href="{{ Module::asset('LandingPage:Resources/assets/holol/css/rtl.css') }}" />
    @endif
    <style>
        :root {
            --color-customColor:
                <?=$color ?>
            ;
        }

        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 9000;
        }
    </style>
    @stack('scripts')
</head>

<body>

    @if (session('success'))
        <div id="customAlert" class="alert alert-success alert-dismissible fade show text-end position-fixed"
            style="top: 20px; z-index: 1050; width: fit-content;" role="alert">
            {{ __(session('success')) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <script>
        function closeAlert() {
            const alertBox = document.getElementById('customAlert');
            alertBox.classList.remove('show'); // Bootstrap fade out effect
            setTimeout(() => alertBox.remove(), 300); // Remove element after fade-out
        }
        document.addEventListener('DOMContentLoaded', function () {
            const alertBox = document.getElementById('customAlert');
            if (alertBox) {
                setTimeout(() => alertBox.remove(), 3000); // Remove element after fade-out

            }
        })
    </script>
    <div class="main">
        <header class="position_top shadow-sm">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-1">
                        <div class="logo" style="width:100px;">
                            <a href="{{ route('home.landingpage') }}"><img
                                    src="{{ url($logo) . '/' . $settings['site_logo'] }}" width="70px" alt /></a>
                        </div>
                    </div>
                    <div class="col-11 menu_bar">
                        <nav class="main-nav ">
                            <div class="mobile-menu-logo" style="width: 100px;">
                                <a href="{{ route('home.landingpage') }}"><img
                                        src="{{ url($logo) . '/' . $settings['site_logo'] }}" alt /></a>
                            </div>
                            <ul class="text-center d-lg-flex">
                                @foreach ($pages as $page => $value)
                                    <li><a class="list_nav"
                                            href="{{ route('custom.page', $value['page_slug']) }}">{{ __($value['menubar_page_name']) }}</a>
                                    </li>
                                @endforeach
                                <li><a class="list_nav" href="{{ route('pricePlan') }}">{{ __('Plans') }}</a></li>
                                <li class="has-child text-dark list_nav active">
                                    <a href="javascript:void(0)" class="text-dark">{{ __('Languages') }} <i
                                            class="bi bi-chevron-down text-dark pt-0"></i></a>
                                    <ul class="sub-menu">
                                        @foreach ($languages as $code => $language)
                                            <li><a class="{{ $locale == $code ? 'active' : '' }}"
                                                    href="{{ route('change.language', $code) }}">{{ ucFirst($language) }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>

                                <div class="get-quate pt-4 ps-4">
                                    <div class="cmn-btn">
                                        @if (isset(\Auth::user()->name))
                                            <a href="{{ route('home') }}"> {{ Auth::user()->name }}</a>
                                        @else
                                                <a href="{{ route('login') }}">{{ __('Login') }}</a>
                                            </div>
                                            <div class="cmn-btn">
                                                <a href="{{ route('register') }}">{{ __('Register') }}</a>
                                            </div>
                                        @endif
                                </div>
                            </ul>
                        </nav>

                        <div class="mobile-menu">
                            <a href="javascript:void(0)" class="cross-btn">
                                <span class="cross-top"></span>
                                <span class="cross-middle"></span>
                                <span class="cross-bottom"></span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </header>
        @yield('content')
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-lg-6 col-xl-4">
                        <div class="footer-widget">
                            <div class="footer-logo">
                                <a href="{{ route('home.landingpage') }}"><img
                                        src="{{ url($logo) . '/' . $settings['site_logo'] }}" style="max-width: 100px;"
                                        width="20px" alt /></a>
                            </div>
                            <address>
                                <h4>
                                    {{ __('Office') }}
                                </h4>
                                <p><i class="fas fa-map-marker-alt text-danger"></i>
                                    {{ __('Egypt - Cairo - Qalyubia -El-Gomhoria st') }}</p>
                                <p><i class="fas fa-map-marker-alt text-danger"></i> {{ __('Alexandria - almandara') }}
                                </p>
                            </address>
                            <ul class="social-media-icons">

                                <li><a target="_blank" href="{{$adminSettings['social']['facebook']}}"><i
                                            class="fab fa-facebook-f"></i></a></li>
                                <li><a target="_blank" href="{{$adminSettings['social']['instagram']}}"><i
                                            class="fab fa-instagram"></i></a></li>
                                <li><a target="_blank" href="{{$adminSettings['social']['youtube']}}"><i
                                            class="fab fa-youtube"></i></a></li>
                                <li><a target="_blank" href="{{$adminSettings['social']['tikto']}}"><i
                                            class="fab fa-tiktok"></i></a></li>
                                <li><a target="_blank" href="{{$adminSettings['social']['twitter']}}"><i
                                            class="fab fa-twitter"></i></a></li>
                                <li><a target="_blank" href="https://wa.me/201023068425"><i
                                            class="fab fa-whatsapp"></i></a></li>
                            </ul>
                            <div class="d-flex w-100 justify-content-start mt-2 align-items-center">
                                <img src="{{ asset('assets/images/visa&mastercard.png') }}" style="width:200px" alt="">

                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-6 col-xl-4">
                        <div class="footer-widget">
                            <h4>{{ __('Company') }}</h4>
                            <ul class="footer-menu">
                                @if (is_array(json_decode($settings['menubar_page'])) || is_object(json_decode($settings['menubar_page'])))
                                    @foreach (json_decode($settings['menubar_page']) as $key => $value)
                                        @if ($value->header == 'on' && $value->template_name == 'page_content')
                                            <li><a
                                                    href="{{ route('custom.page', $value->page_slug) }}">{{ __($value->menubar_page_name) }}</a>
                                            </li>
                                        @elseif($value->header == 'on')
                                            <li><a
                                                    href="{{ $value->page_url == '/contact' ? route('contact.page') : $value->page_url }}">{{ __($value->menubar_page_name) }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6 col-xl-4">
                        <div class="footer-widget">
                            <h4>
                                {{ __('Contact') }}
                            </h4>
                            <div class="number">
                                <div class="num-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="phone">
                                    <a href="tel:00201023068425">{{ __('EGYPT') }} :
                                        {{ App::isLocale('ar') ? '201023068425+' : '+201023068425' }}</a>
                                    <a href="tel:+966508060608">{{ __('Saudi Arabia') }} :
                                        {{ App::isLocale('ar') ? '966508060608+' : '+966508060608' }}</a>
                                    <a href="tel:+971506058635">{{ __('Emirates') }} :
                                        {{ App::isLocale('ar') ? '971506058635+' : '+971506058635' }}</a>
                                    <a href="tel:+212680080175">{{ __('Morroco') }} :
                                        {{ App::isLocale('ar') ? '212680080175+' : '+212680080175' }}</a>
                                </div>
                            </div>
                            <div class="office-mail">
                                <div class="mail-icon">
                                    <i class="far fa-envelope"></i>
                                </div>
                                <div class="email">
                                    <a href="mailto:info@thefuture-erp.com"><span>info@thefuture-erp.com</span></a>
                                    <a href="mailto:info@hololtec.com"><span>info@hololtec.com</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-4 col-lg-4 col-xl-5">
                            <div class="copy-txt">
                                <span>Copyright by thefuture-erp.com © {{ date('Y') }}.</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </footer>

        <div class="scroll-top">
            <span>Top<i class="bi bi-arrow-up"></i></span>
        </div>
    </div>


    <!-- About Advanced Solutions Company <The Future ERP> about_advanced_solutions_company_<the_future_erp> -->

    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/light.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/popper.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/bootstrap.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/jQuery-plugin-progressbar.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/jquery.barfiller.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/waypoints.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/jquery.counterup.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/lightbox.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/isotope.pkgd.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/masonry.pkgd.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ Module::asset('LandingPage:Resources/assets/holol/js/custom.js') }}"></script>
    {{--
    <script src="{{Module::asset('LandingPage:Resources/assets/holol/js/main.js')}}"></script>
    --}}
</body>

</html>





















<!-- end new Landing page 3 / 9 -->