@extends('landingpage::layouts.landingpage')
@php
    $local = app()->getLocale();
    $logo = Utility::get_file('uploads/landing_page_image');
    $services = json_decode($settings['discover_of_features'], true);
    $countService = 0;
@endphp
@section('content')
<link rel="stylesheet" href="public/assets/xoric/app.min.css">
<div class="bg-primary bg-pattern mt-5 py-5" >
    <div class="account-pages mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-sm-8">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="p-2">
                                <div class="card-body">
                                    @if (session('status') == 'verification-link-sent')
                                    <div class="mb-4 font-medium text-sm text-green-600 text-primary">
                                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                                    </div>
                                    @endif
                                    <div class="mb-4 text-sm text-gray-600">
                                        @if (session('status') == 'verification-link-sent')
                                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                                        @else
                                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking the button below?') }}
                                        @endif
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="row">
                                            <div class="col-auto">
                                                <form method="POST" action="{{ route('verification.send') }}">
                                                    @csrf
                                                    @if (session('status') == 'verification-link-sent')
                                                    <button type="submit" class="btn btn-primary btn-sm"> {{ __('Resend Verification Email') }}</button>
                                                    @else
                                                    <button type="submit" class="btn btn-primary btn-sm"> {{ __('Send Verification Email') }}</button>
                                                    @endif
                                                </form>
                                            </div>
                                            <div class="col-auto">
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">{{ __('Logout') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
