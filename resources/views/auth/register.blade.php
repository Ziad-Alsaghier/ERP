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
                                <h5 class="mb-5 text-center">{{ __('Register Account') }}</h5>
                                <form class="form-horizontal" action="{{ route('register') }}" method="post">
                                    @method('POST')
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group  mb-4">
                                                <label for="username">{{ __('Username') }}</label>
                                                <input name="name" value="{{ old('name') }}" required autocomplete="name" autofocus type="text" class="form-control  @error('name') is-invalid @enderror" id="username" required>
                                                @error('name')
                                                    <span class="error invalid-email text-danger" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group  mb-4">
                                                <label for="useremail">{{ __('Email') }}</label>
                                                <input id="email"  type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="form-control  @error('email') is-invalid @enderror" required>
                                                @error('email')
                                                    <span class="error invalid-email text-danger" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group  mb-4">
                                                <label for="userpassword">{{ __('Password') }}</label>
                                                <input id="password" type="password" data-indicator="pwindicator" class="form-control pwstrength @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" class="form-control" id="userpassword" required>
                                                @error('password')
                                                    <span class="error invalid-email text-danger" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group  mb-4">
                                                <label for="password_confirmation">{{ __('Password Confirmation') }}</label>
                                                <input id="password_confirmation" type="password" data-indicator="pwindicator" class="form-control pwstrength @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password" class="form-control" required>
                                                @error('password_confirmation')
                                                    <span class="error invalid-email text-danger" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="term-conditionCheck" required='required'>
                                                <label class="custom-control-label font-weight-normal" for="term-conditionCheck">{{ __('I accept') }} <a href="pages/terms_and_conditions" class="text-primary">{{ __('Terms and Conditions') }}</a></label>
                                            </div>
                                            <div class="mt-4">
                                                <button class="btn btn-success d-block w-100 waves-effect waves-light" type="submit">{{ __('Register') }}</button>
                                            </div>
                                            <div class="mt-4 text-center">
                                                <a href="{{ route('login') }}" class="text-muted"><i class="mdi mdi-account-circle me-1"></i>{{ __('Already have account?') }}</a>
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
