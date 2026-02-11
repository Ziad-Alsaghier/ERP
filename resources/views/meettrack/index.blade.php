@extends('layouts.admin')
@section('page-title')
    {{__('Meet Track')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Meet Track')}}</li>
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{__('Meet Track')}}</h5>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Meet Track') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table header" width="100%">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Job Title') }}</th>
                                    <th>{{ __('Company Name') }}</th>
                                    <th>{{ __('Mobile') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Interest Level') }}</th>
                                    <th>{{ __('Preferred Contact By') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($meets->count() > 0)
                                    @foreach ($meets as $track)
                                        <tr data-bs-toggle="collapse" data-bs-target="#details-{{ $track->id }}" class="accordion-toggle">
                                            <td>{{ $track->name ?? '--'}}</td>
                                            <td>{{ $track->job_title ?? '--'}}</td>
                                            <td>{{ $track->company_name ?? '--'}}</td>
                                            <td>{{ $track->mobile ?? '--'}}</td>
                                            <td>{{ $track->email ?? '--'}}</td>
                                            <td>{{ __(ucfirst($track->interest_level)) ?? '--'}}</td>
                                            <td>{{ __($track->contact_method) ?? '--'}}</td>
                                            <td>
    <a href="#" class="btn btn-primary btn-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
            <path d="M8 3C4.134 3 1 5.134 1 8s3.134 5 7 5 7-2.134 7-5-3.134-5-7-5zm0 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-7a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
        </svg>
    </a>
</td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="hiddenRow">
                                                <div id="details-{{ $track->id }}" class="collapse">
                                                    <div class="p-3">
                                                        <strong>{{ __('Company Activities:') }}</strong>
                                                        <p>{{ $track->company_activites }}</p>
                                                        <strong>{{ __('Tech Solutions:') }}</strong>
                                                        <p class="text-wrap">{{ $track->tech_solutions }}</p>
                                                        <strong>{{ __('Preferred Contact Time: ') }}</strong>
                                                        <p class="text-wrap">{{ $track->contact_time }}</p>
                                                        <!-- Add other expandable details here -->
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <th scope="col" colspan="8">
                                            <h6 class="text-center">{{ __('No Meet Track Found.') }}</h6>
                                        </th>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
