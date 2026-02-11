@extends('layouts.auth')
@php
    use App\Models\Utility;
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $settings = Utility::settings();
    $company_logo = $settings['company_logo'] ?? '';

@endphp
@push('custom-scripts')
@if ($settings['recaptcha_module'] == 'on')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush
@section('page-title')
    {{ __('Login') }}
@endsection


@php
    $languages = App\Models\Utility::languages();
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp


@section('content')
{{-- @dd($accounts) --}}
<style>




    .account_chooser .account {
        text-align: left;
        text-decoration: none;
    }
    .account_chooser .account {
    border: 1px solid #dee2e6;
    transition: all 100ms ease-in-out;
    }
    .account_chooser .account:hover {
    border: 1px solid #acaeb1;
    scale: 0.99;

    }
    .account_chooser .account img{
    height: 50px;
    width:  50px;
    }

    </style>
    <div class="card-body">
            <div class="account_chooser">
                <h1>{{__('Choose an account')}}</h1>


                {{-- @foreach ($accounts as $account)
                <a class="account d-flex text-black p-2 m-4 rounded" href="{{ route('login.with.company', $account->id) }}">
                    <img src="{{ !empty($account->avatar) ? $profile . $account->avatar :  $profile.'avatar.png'}}" class="bd-placeholder-img flex-shrink-0 me-2 rounded" role="img">
                    <p class=" mb-0 small lh-sm">
                        <strong class="d-block">{{ $account->name }}</strong>
                        <strong class="d-block">{{ $account->email }}</strong>
                        <strong class="d-block">{{ $account->type }}</strong>
                    </p>
                </a>
                @endforeach --}}

                @foreach ($accounts as $account)
                @if ($account->type == 'company')
                <a class="account d-flex text-black p-2 m-4 rounded" href="{{ route('home') }}">
                    <img src="{{ !empty($account->avatar) ? $profile . $account->avatar :  $profile.'avatar.png'}}" class="bd-placeholder-img flex-shrink-0 me-2 rounded" role="img">
                    <p class=" mb-0 small lh-sm">
                        <strong class="d-block">{{ __('continue as') }} ( {{__('company')}} )</strong>
                        <strong class="d-block">{{ $account->name }}</strong>
                        <strong class="d-block">{{ $account->email }}</strong>
                    </p>
                </a>
                <hr>
                @else
                <a class="account d-flex text-black p-2 m-4 rounded" href="{{ route('login.with.company', $account->id) }}">
                    <img src="{{ !empty($account->avatar) ? $profile . $account->avatar :  $profile.'avatar.png'}}" class="bd-placeholder-img flex-shrink-0 me-2 rounded" role="img">
                    <p class=" mb-0 small lh-sm">
                        <strong class="d-block">{{ $account->name }}</strong>
                        <strong class="d-block">{{ $account->email }}</strong>
                        <strong class="d-block">{{ $account->type }} - {{ $account->created_by != 1 ? ' - ' . App\Models\User::where('id',$account->created_by )->pluck('name')->first() : '' }}</strong>
                    </p>
                </a>
                @endif
                @endforeach

                
    
    
            </div>
        </div>
@endsection


<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#form_data").submit(function(e) {
            $("#login_button").attr("disabled", true);
            return true;
        });
    });
</script>
