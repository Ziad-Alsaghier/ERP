@extends('layouts.admin')
@section('page-title')
    {{ __('Generate APIs') }}
@endsection
@php
    use App\Models\Utility;
    use App\Models\WebhookSetting;
    $logo = \App\Models\Utility::get_file('uploads/logo/');

    $logo_light = !empty($setting['company_logo_light']) ? $setting['company_logo_light'] : '';
    $logo_dark = !empty($setting['company_logo_dark']) ? $setting['company_logo_dark'] : '';
    $company_favicon = !empty($setting['company_favicon']) ? $setting['company_favicon'] : '';

    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';
    $SITE_RTL = isset($setting['SITE_RTL']) ? $setting['SITE_RTL'] : 'off';

    $currantLang = Utility::languages();
    $lang = \App\Models\Utility::getValByName('default_language');
    $webhookSetting = WebhookSetting::where('created_by', '=', \Auth::user()->creatorId())->get();

@endphp
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Generate APIs') }}</li>
@endsection
@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>



    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,
        })



        $('.colorPicker').on('click', function(e) {
            $('body').removeClass('custom-color');
            if (/^theme-\d+$/) {
                $('body').removeClassRegex(/^theme-\d+$/);
            }
            $('body').addClass('custom-color');
            $('.themes-color-change').removeClass('active_color');
            $(this).addClass('active_color');
            const input = document.getElementById("color-picker");
            setColor();
            input.addEventListener("input", setColor);

            function setColor() {
                $(':root').css('--color-customColor', input.value);
            }

            $(`input[name='color_flag`).val('true');
        });


        $('.themes-color-change').on('click', function() {

            $(`input[name='color_flag`).val('false');

            var color_val = $(this).data('value');
            $('body').removeClass('custom-color');
            if (/^theme-\d+$/) {
                $('body').removeClassRegex(/^theme-\d+$/);
            }
            $('body').addClass(color_val);
            $('.theme-color').prop('checked', false);
            $('.themes-color-change').removeClass('active_color');
            $('.colorPicker').removeClass('active_color');
            $(this).addClass('active_color');
            $(`input[value=${color_val}]`).prop('checked', true);
        });

        $.fn.removeClassRegex = function(regex) {
            return $(this).removeClass(function(index, classes) {
                return classes.split(/\s+/).filter(function(c) {
                    return regex.test(c);
                }).join(' ');
            });
        };
    </script>


@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    {{--  Start Sub Navbar --}}
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#generate_apis"
                                class="list-group-item list-group-item-action border-0">{{ __('Generate APIs') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#brand-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Brand Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#system-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('System Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#company-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Company Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#bill-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Invoice Print Setting') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#Zatca-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Zatca Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#email-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Email Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#tracker-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Time Tracker Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#payment-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Payment Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#zoom-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Zoom Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#slack-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Slack Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#telegram-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Telegram Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#twilio-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Twilio Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#email-notification-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Email Notification Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#offer-letter-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Offer Letter Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#joining-letter-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Joining Letter Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#experience-certificate-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Experience Certificate Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#noc-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('NOC Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#google-calender"
                                class="list-group-item list-group-item-action border-0">{{ __('Google Calendar Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#webhook-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Webhook Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#ip-restriction-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('IP Restriction Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        </div>
                    </div>



                </div>
                <div class="col-xl-9">
                    <div id="generate_apis" class="card">
                        <div class="card-header">
                            <h5>{{ __('Generate APIs') }}</h5>
                            <small class="text-muted">{{ __('Generate APIs') }}</small>
                        </div>
                        {{-- {{ Form::model($setting, ['route' => 'generate.apis', 'method' => 'post']) }} --}}
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-4">

                                    {{ Form::label('api_key', __('Generate APIs'), ['class' => 'form-label']) }}
                                    {{ Form::text('api_key', '', ['class' => 'form-control', 'placeholder' => __('Enter Api Key')]) }}
                                    <div id="apiKeyError" class="text-danger mt-1"></div>
                                </div>

                                <div class="card-footer text-end">
                                    <div class="form-group">
                                        <input class="btn btn-print-invoice btn-primary m-r-10" id="generateApiBtn"
                                            type="submit" value="{{ __('Save Changes') }}">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Include jQuery if not already included -->
        <script>
            // Utility to get a cookie value by name
            function getCookie(name) {
                const value = "; " + document.cookie;
                const parts = value.split("; " + name + "=");
                if (parts.length === 2) return decodeURIComponent(parts.pop().split(";").shift());
            }

            $(function() {
                $('#generateApiBtn').on('click', function(e) {
                    e.preventDefault();
                    const authToken = getCookie('XSRF-TOKEN'); // Assuming you store the token here
                    if (!authToken) {
                        alert('No authentication token found.');
                        return;
                    }
                    const apiKey = $('input[name="api_key"]').val(); // Get api_key value
                    $.ajax({
                        url: "{{ route('generate.apis') }}", // Laravel route
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "api_key": apiKey, // Send api_key in request
                        },
                        // headers: {
                        //     'Authorization': 'Bearer ' + authToken,  // Use Bearer token for authentication
                        //     'X-XSRF-TOKEN': "{{ csrf_token() }}"  , // Include CSRF token for the session
                        //   "_token": "{{ csrf_token() }}",  // Include CSRF token for the Auth Middle
                        // },
                        success: function(response) {
                            console.log(response); // Debugging line

                            alert(response.status === 'success' ? response.message :
                                'Something went wrong.');
                        },
                        error: function(xhr) {
                               try {
                                        const res = JSON.parse(xhr.responseText);
                                        if (res.message) {
                                            $('#apiKeyError').removeClass('d-none').text(res.message);
                                        } else {
                                            $('#apiKeyError').removeClass('d-none').text('An unknown error occurred.');
                                        }
                                } catch (e) {
                                        // silently fail and show a generic message
                                        $('#apiKeyError').removeClass('d-none').text('Unexpected error occurred.');
                                }
                        }
                    });
                });
            });
        </script>
    @endsection
