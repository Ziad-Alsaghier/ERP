
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $lang == 'on' ? 'rtl' : ''}}">


<meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">

<head>
    <title>{{ $setting['title_text'] ? $setting['title_text'] : config('app.name', 'Holol-tec') }} - @yield('page-title')
    </title>

    <meta name="title" content="{{$setting['meta_title']}}">
    <meta name="description" content="{{ $setting['meta_desc'] }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $setting['meta_title'] }}">
    <meta property="og:description" content="{{ $setting['meta_desc']}}">
    <meta property="og:image" content="{{ $meta_image . $setting['meta_image'] }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $setting['meta_title'] }}">
    <meta property="twitter:description" content="{{$setting['meta_desc'] }}">
    <meta property="twitter:image" content="{{ $meta_image . $setting['meta_image'] }}">
    <script src="{{ asset('js/html5shiv.js') }}"></script>

    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="url" content="{{ url('') . '/' . config('chatify.path') }}" data-user="{{ Auth::user()->id }}">
    <link rel="icon" href="{{ url($logo) . '/' . (isset($setting['company_favicon'] ) && !empty($setting['company_favicon'] ) ? $setting['company_favicon']  : 'favicon.png') }}" type="image" sizes="16x16">
    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/animate.min.css') }}">
    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <!-- Goole font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@160..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/fonts/sarSymbol/saudiriyalsymbol.ttf')}}" />
    <!--bootstrap switch-->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <!--DataTables From DataBase-->
<!-- Bootstrap CSS (optional for style) -->
    <!-- DataTables + Buttons CSS  -->
    {{-- Update Version Datatable⭐ --}}
    <link href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- In your <head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />

    <!-- DataTables CSS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- vendor css -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @if ($lang == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif

    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="main-style-link">
    @endif

    @if ($lang != 'on' && $setting['cust_darklayout'] != 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('css/custom-dark.css') }}">
    @endif

    <style>
        :root {
            --color-customColor: {!! $setting['color'] !!};
        }

      @font-face {
        font-family: 'SaudiRiyalSymbol';
        src: url('assets/fonts/sarSymbol/saudiriyalsymbol.ttf') format('truetype');
        }
        .symbol {
        font-family: 'SaudiRiyalSymbol', sans-serif;
        font-size: inherit;
        margin: 20px auto;
        color: inherit;
        font-weight: bold;
        }
            @media print {
                @font-face {
                font-family: 'SaudiRiyalSymbol';
                src: url('assets/fonts/sarSymbol/saudiriyalsymbol.ttf') format('truetype');
                }
                .symbol {
                font-family: 'SaudiRiyalSymbol', sans-serif;
                font-size: inherit;
                margin: 20px auto;
                color: inherit;
                font-weight: bold;
            }

            .usage-rule {
                display: flex;
                align-items: center;
            }

            .usage-rule i {
                color: green;
            }
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    @stack('css-page')

    <style>
.toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }

    /* RGB Gradient Animation */
    .toast-body::before {
        content: '';
        position: absolute;
        inset: -2px; /* Border thickness */
        border-radius: inherit;
        z-index: -1;
        background: linear-gradient(135deg,
            rgba(255, 0, 0, 1),
            rgba(0, 255, 0, 1),
            rgba(0, 0, 255, 1));
        background-size: 300% 300%;
        animation: rgb-border 4s linear infinite;
    }

    /* RGB Animation */
   @keyframes rgb-border {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
      </style>
</head>



<body class="{{ $setting['color_flag'] ? 'custom-color' : $color }}">

    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    @include('partials.admin.menu')
    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->
    @include('partials.admin.header')

    <!-- Modal -->
    <div class="modal notification-modal fade" id="notification-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close float-end" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <h6 class="mt-2">
                        <i data-feather="monitor" class="me-2"></i>Desktop settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting1" checked />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting1">Allow desktop notification</label>
                    </div>
                    <p class="text-muted ms-5">
                        you get lettest content at a time when data will updated
                    </p>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting2" />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting2">Store Cookie</label>
                    </div>
                    <h6 class="mb-0 mt-5">
                        <i data-feather="save" class="me-2"></i>Application settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting3" />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting3">Backup Storage</label>
                    </div>
                    <p class="text-muted mb-4 ms-5">
                        Automaticaly take backup as par schedule
                    </p>
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting4" />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting4">Allow guest to print
                            file</label>
                    </div>
                    <h6 class="mb-0 mt-5">
                        <i data-feather="cpu" class="me-2"></i>System settings
                    </h6>
                    <hr />
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="pcsetting5" checked />
                        <label class="form-check-label f-w-600 pl-1" for="pcsetting5">View other user chat</label>
                    </div>
                    <p class="text-muted ms-5">Allow to show public user message</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-danger btn-sm" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-light-primary btn-sm">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="dash-container">
        <div class="dash-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="page-header-title">
                                <h4 class="m-b-10">@yield('page-title')</h4>
                            </div>
                            <ul class="breadcrumb">
                                @yield('breadcrumb')
                            </ul>
                        </div>
                        <div class="col action-btn-col">
                            @yield('action-btn')
                        </div>
                    </div>
                </div>
            </div>
            @yield('content')
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="body">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="commonModalOver" tabindex="-1" role="dialog" aria-labelledby="commonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commonModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
        <div id="liveToast" class="toast " role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    ✅ Success message!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>

    </div>

    <script>
        // Function to play sound
        function playNotificationSound(url) {
            const sound = new Audio(url);
            sound.volume = 0.7;
            sound.play().catch(err => console.error("Error playing sound:", err));
        }

        // Function to show toast and play sound
        function showNotification(message, soundUrl) {
            const toastElement = document.getElementById('liveToast');
            const toastBody = document.getElementById('toastMessage');

            // Update toast message
            toastBody.textContent = message;

            // Show toast using Bootstrap
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            // Play notification sound
            playNotificationSound(soundUrl);
        }

        // Trigger notification automatically on page load
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                showNotification("✅ {{ session('success') }}", 'https://www.soundjay.com/misc/sounds/bell-ringing-05.mp3');
            @elseif(session('error'))
                showNotification("❌ {{ session('error') }}", 'https://www.soundjay.com/button/sounds/beep-09.mp3');
            @endif
        });
    </script>
    @include('partials.admin.footer')
    @include('Chatify::layouts.footerLinks')

    <style>
     /* Invalid state (red border) */
.was-validated .choices.is-invalid .choices__list,
.choices.is-invalid .choices__input {
    border-color: #fa5c7c !important;
}

/* Valid state (green border) */
.was-validated .choices.is-valid .choices__list,
.choices.is-valid .choices__input {
    border-color: #0acf97 !important;
}

    </style>
    <script>
    // JavaScript for enabling Bootstrap validation
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

</body>

</html>
