@extends('landingpage::layouts.landingpage')
@php
    $local = app()->getLocale();
    $logo = Utility::get_file('uploads/landing_page_image');
    $services = json_decode($settings['discover_of_features'], true);
    $countService = 0;
    $token = request()->route()->parameter('token');
@endphp
@section('content')
    <link rel="stylesheet" href="public/assets/xoric/app.min.css">
    <div class="bg-primary bg-pattern mt-5 py-5">
        <div class="account-pages mt-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5 col-sm-8">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <h2 class="mb-3 f-w-600">{{ __('Reset Password') }}</h2>
                                    {{-- <p>{{ __('Sign in by entering the information below?') }} </p> --}}
                                </div>
                                {{ Form::open(['route' => 'password.update', 'method' => 'post', 'id' => 'loginForm']) }}
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="">
                                    <div class="form-group mb-3">
                                        {{ Form::label('email', __('E-Mail Address'), ['class' => 'form-label']) }}
                                        {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Email')]) }}
                                        @error('email')
                                            <span class="invalid-email text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}
                                        {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Password')]) }}
                                        @error('password')
                                            <span class="invalid-password text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        {{ Form::label('password_confirmation', __('Password Confirmation'), ['class' => 'form-label']) }}
                                        {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => __('Enter Confirm Password')]) }}
                                        @error('password_confirmation')
                                            <span class="invalid-password_confirmation text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="d-grid">
                                        {{ Form::submit(__('Reset'), ['class' => 'btn btn-primary btn-block mt-2', 'id' => 'resetBtn']) }}
                                    </div>

                                </div>

                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
