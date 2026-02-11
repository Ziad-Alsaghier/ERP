@php
@endphp

@extends('landingpage::layouts.landingpage')
@section('content')
<!-- <section class="breadcrumbs">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="breadcrumb-wrapper">
                <h1>Pricing Plans</h1>
                <ul>
                  <li><a href="index.html">Home</a></li>
                  <li>Pricing Plans</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section> -->
<section class="contact-area sec-mar">
    <div class="container">
        <div class="row">
            <div class="col col-xl-6">
                <div class="title black">
                    <span>{{ __('Get In Touch') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="contact-information">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-xl-6">
                    <div class="contact-form">
                        <h3>{{ __('We’d Love to Hear From You') }}</h3>
                        <form method="POST" action="{{route('meet.store')}}">
                            @csrf
                            <div class="row">
                                <!-- Name Input -->
                                <div class="col-12">
                                    <input type="text" class="border border-dark rounded" name="name" placeholder="{{ __('Enter your name') }}" value="{{ old('name') }}">
                                    @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Mobile Input -->
                                <div class="col-12">
                                    <input type="text" class="border border-dark rounded" name="mobile" placeholder="{{ __('Enter your mobile number') }}" value="{{ old('mobile') }}">
                                    @error('mobile')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email Input -->
                                <div class="col-12">
                                    <input type="text" class="border border-dark rounded" name="email" placeholder="{{ __('Enter your email') }}" value="{{ old('email') }}">
                                    @error('email')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="mt-5">

                                <!-- Job Title Input -->
                                <div class="col-12">
                                    <input type="text" class="border border-dark rounded" name="job_title" placeholder="{{ __('Enter your job title') }}" value="{{ old('job_title') }}">
                                    @error('job_title')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Company Name Input -->
                                <div class="col-12">
                                    <input type="text" class="border border-dark rounded" name="company_name" placeholder="{{ __('Enter your company name') }}" value="{{ old('company_name') }}">
                                    @error('company_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Company Activities Input -->
                                <div class="col-12">
                                    <input type="text" class="border border-dark rounded" name="company_activites" placeholder="{{ __('Enter your company activities') }}" value="{{ old('company_activites') }}">
                                    @error('company_activites')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>





                                <!-- Technology Solutions Textarea -->
                                <div class="col-12">
                                    <textarea name="tech_solutions" cols="30" rows="10" class="border rounded border-dark" placeholder="{{ __('Enter your technology solutions') }}">{{ old('tech_solutions') }}</textarea>
                                    @error('tech_solutions')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Interest Level in the Program -->
                                <div class="col-12">
                                    <select name="interest_level" class="border w-100 rounded border-dark">
                                        <option value="" disabled {{ old('interest_level') ? '' : 'selected' }}>{{ __('How interested are you in the program?') }}</option>
                                        <option value="high" {{ old('interest_level') == 'high' ? 'selected' : '' }}>{{ __('Highly Interested') }}</option>
                                        <option value="medium" {{ old('interest_level') == 'medium' ? 'selected' : '' }}>{{ __('Somewhat Interested') }}</option>
                                        <option value="low" {{ old('interest_level') == 'low' ? 'selected' : '' }}>{{ __('Not Interested') }}</option>
                                    </select>
                                    @error('interest_level')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Demo Request -->
                                <div class="col-12">
                                    <div class="form-check form-switch d-flex justify-content-between align-items-center flex-row-reverse">
                                        <input class="form-check-input" style="height: 25px; width: 50px;" type="checkbox" name="demo_request" value="1" role="switch" id="flexSwitchCheckDefault" {{ old('demo_request') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flexSwitchCheckDefault">{{ __('Would you like a demo presentation?') }}</label>
                                    </div>
                                    @error('demo_request')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Preferred Contact Time -->
                                <div class="col-12">
                                    <input type="text" class="border border-dark rounded" name="contact_time" placeholder="{{ __('Preferred time to contact you') }}" value="{{ old('preferred_contact_time') }}">
                                    @error('contact_time')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Preferred Contact Method -->
                                <div class="col-12">
                                    <select name="contact_method" class="border w-100 rounded border-dark">
                                        <option value="" disabled {{ old('contact_method') ? '' : 'selected' }}>{{ __('Preferred contact method') }}</option>
                                        <option value="email" {{ old('contact_method') == 'email' ? 'selected' : '' }}>{{ __('Email') }}</option>
                                        <option value="phone" {{ old('contact_method') == 'phone' ? 'selected' : '' }}>{{ __('Phone') }}</option>
                                        <option value="whatsapp" {{ old('contact_method') == 'whatsapp' ? 'selected' : '' }}>{{ __('Whatsapp') }}</option>
                                    </select>
                                    @error('contact_method')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <button style="margin-top: 40px;" class="bg-primary" type="submit">{{ __('Submit') }}</button>
                                </div>
                                <p class="form-message"></p>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection