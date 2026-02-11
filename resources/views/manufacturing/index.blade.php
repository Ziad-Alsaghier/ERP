@extends('layouts.admin')
@section('page-title')
    {{__('Instant Quote')}}
@endsection
@push('script-page')
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Instant Quote')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
            <a href="{{ route('manufacturing.create') }}"  data-ajax-popup="true" data-title="{{__('Create')}}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Create')}}">
                <i class="ti ti-plus"></i>
            </a>
    </div>
@endsection

@section('content')

@endsection

