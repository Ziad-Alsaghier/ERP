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
                <div class="col-md-12 col-xl-5">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="p-2">
                                <h5 class="mb-5 text-center">{{ __('Sign in to continue') }}</h5>
                                <form class="form-horizontal" action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="email" class="col-form-label">{{ __('Email') }}</label>
                                            <input type="email" name="email" value="" class="form-control" id="email" placeholder="{{ __('Enter Email') }}">
                                            @error('email')
                                                <span class="error invalid-email text-danger" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <label for="userpassword" class="col-form-label">{{ __('Password') }}</label>
                                            <input type="password" name="password" class="form-control" id="userpassword" placeholder="{{ __('Enter Password') }}">
                                            @error('password')
                                                <span class="error invalid-password text-danger" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <div class="mt-2">
                                                <a href="{{ route('password.request') }}" class="text-muted"><i class="mdi mdi-lock"></i>{{ __('Forgot your password?') }}</a>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-success d-block w-100 waves-effect waves-light"
                                                    type="submit">{{ __('Log In') }}</button>
                                            </div>
                                            <div class="mt-4 text-center">
                                                <a href="{{ route('register') }}" class="text-muted"><i class="mdi mdi-account-circle me-1"></i>{{ __('Create an account') }}</a>
                                            </div>
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
