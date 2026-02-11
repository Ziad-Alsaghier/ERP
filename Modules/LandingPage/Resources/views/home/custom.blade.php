
@extends('landingpage::layouts.landingpage')
@section('content')

<section class="common-banner bg-primary">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4">
                <div class="title">
                    <h1 class="text-white">{!! __($page['menubar_page_name']) !!}</h1>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="static-content section-gap">
    <div class="container">
        <div class="mb-5">
            {!! $page['menubar_page_contant'] !!}
        </div>

        @if ($settings['testimonials_status'] == 'on' && !empty($settings['testimonials']) && isset($settings['testimonials']) && is_array($settings['testimonials']))
            @if (is_array(json_decode($settings['testimonials'])) || is_object(json_decode($settings['testimonials'])))

            @php
                        $testimonials = array_rand(json_decode($settings['testimonials'], true),1);
                        $testimonial = json_decode($settings['testimonials'])[$testimonials];                            
            @endphp
                <div>
                    <div class="row gy-4">
                        <div class="col-12">
                            <div class="bg-primary p-4 rounded">
                                <div class="row gy-3 align-items-center">
                                    <div class="col-xxl-6 col-lg-6">
                                        <div class="d-flex flex-column flex-sm-row gap-3">
                                            <span class="theme-avtar avtar avtar-xl bg-light-dark rounded-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="23" viewBox="0 0 36 23" fill="none">
                                                    <path d="M12.4728 22.6171H0.770508L10.6797 0.15625H18.2296L12.4728 22.6171ZM29.46 22.6171H17.7577L27.6669 0.15625H35.2168L29.46 22.6171Z" fill="white"></path>
                                                    </svg>
                                            </span>
                                            <div>
                                                <h2>{!! $testimonial->testimonials_title !!}</h2>
                                                <p class="mb-0">{!! $testimonial->testimonials_description !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-6 col-lg-6">
                                    <div class="d-flex align-items-center gap-3 justify-content-center justify-content-sm-end">
                                        <div class="text-end">
                                            <b class="d-block">{{ $testimonial->testimonials_user }} </b>
                                            <span class="d-block">{!! $testimonial->testimonials_designation !!}</span>
                                            <span>
                                                @for ($i = 1; $i <= (int) $testimonial->testimonials_star; $i++)
                                                    <i data-feather="star"></i>
                                                @endfor
                                            </span>
                                        </div>
                                        <span class="theme-avtar avtar avtar-l rounded-circle">
                                            <img src="{{ $logo.'/'. $testimonial->testimonials_user_avtar }}" class="img-fluid rounded-circle" alt="">
                                        </span>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</section>



@endsection