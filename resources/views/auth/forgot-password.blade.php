@extends('landingpage::layouts.landingpage')
@php
    $local = app()->getLocale();
    $logo = Utility::get_file('uploads/landing_page_image');
    $services = json_decode($settings['discover_of_features'], true);
    $countService = 0;
@endphp
@section('content')
    <link rel="stylesheet" href="public/assets/xoric/app.min.css">
    <div class="bg-primary bg-pattern mt-5 py-5">
        <div class="account-pages mt-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5 col-sm-8">
                        <div class="card">
                            <div class="card-body p-4">
                                <div class="p-2">
                                    <h5 class="mb-5 text-center">{{ __('Reset Password') }}</h5>
                                    <form class="form-horizontal" action="{{ route('password.email') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group mb-4">
                                                    <label for="email">{{ __('Email') }}</label>
                                                    <input type="email" name="email" value="" class="form-control" id="email" required>
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <small>{{ $message }}</small>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <button class="btn btn-success d-block w-100 waves-effect waves-light"
                                                    type="submit">{{ __('Send Password Reset Link') }}</button>
                                            </div>
                                                <div class="mt-4 text-center">
                                                    <p class="my-4 text-center">{{ __('Back to') }} <a
                                                            href="{{ route('login') }}"
                                                            class="text-primary">{{ __('Login') }}</a></p>
                                                </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
