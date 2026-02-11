@extends('landingpage::layouts.landingpage')
@php
    $local = app()->getLocale();
    $logo = Utility::get_file('uploads/landing_page_image');
    $services = json_decode($settings['discover_of_features'], true);
    $countService = 0;
    $profile = Utility::get_file('uploads/avatar/');
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
                                    <div class="card-body">
                                        <div class="account_chooser">
                                            <h1>{{ __('Choose an account') }}</h1>
                                            @foreach ($accounts as $account)
                                                @if ($account->type == 'company')
                                                    <a class="account d-flex text-black p-2 m-4 rounded"
                                                        href="{{ route('login.with.company', $account->id) }}">
                                                        <img src="{{ !empty($account->avatar) && file_exists(public_path($profile . $account->avatar)) ? $profile . $account->avatar : $profile . 'avatar.png' }}" class="bd-placeholder-img flex-shrink-0 me-2 ms-2 rounded" role="img" width="50px">
                                                        <p class=" mb-0 small lh-sm">
                                                            <strong class="d-block">{{ __('continue as') }} (
                                                                {{ __('company') }} )</strong>
                                                            <strong class="d-block">{{ $account->name }}</strong>
                                                            <strong class="d-block">{{ $account->email }}</strong>
                                                        </p>
                                                    </a>
                                                    <hr>
                                                @else
                                                    <a class="account d-flex text-black p-2 m-4 rounded"
                                                        href="{{ route('login.with.company', $account->id) }}">
                                                        <img src="{{ !empty($account->avatar) ? $profile . $account->avatar : $profile . 'avatar.png' }}" class="bd-placeholder-img flex-shrink-0 ms-2 me-3 rounded" role="img" width="50px">
                                                        <p class=" mb-0 small lh-sm">
                                                            <strong class="d-block">{{ $account->name }}</strong>
                                                            <strong class="d-block">{{ $account->email }}</strong>
                                                            <strong class="d-block">{{ $account->type }} -
                                                                {{ $account->created_by != 1 ? ' - ' . App\Models\User::where('id', $account->created_by)->pluck('name')->first() : '' }}</strong>
                                                        </p>
                                                    </a>
                                                @endif
                                            @endforeach
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
