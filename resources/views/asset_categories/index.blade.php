@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Asset Categories') }}
@endsection

@push('script-page')
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Asset Categories') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="{{ route('asset_categories.create') }}" title="{{ __('Create New Category') }}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')
   

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                    <table class="table datatable">
    <thead>
        <tr>
            <th>{{ __('Reference Number') }}</th>
            <th>{{ __('English Name') }}</th>
            <th>{{ __('Arabic Name') }}</th>
            <th>{{ __('Is Depreciable') }}</th>
            <th>{{ __('Depreciation Method') }}</th>
            <th>{{ __('Useful Life') }}</th>
            <th>{{ __('Useful Life Unit') }}</th>
            
            <th>{{ __('Manual Depreciation') }}</th>
            <th>{{ __('Recorded Depreciation') }}</th>
            <th>{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $category)
            <tr>
                <td>{{ $category->reference_number }}</td>
                <td>{{ $category->english_name }}</td>
                <td>{{ $category->arabic_name }}</td>
                <td>{{ $category->is_depreciable ? __('Yes') : __('No') }}</td>
                <td>{{ $category->depreciation_method ?? '--' }}</td>
                <td>{{ $category->useful_life ?? '--' }}</td>
                <td>{{ $category->useful_life_unit ?? '--' }}</td>
                
                <td>{{ $category->manual_depreciation  ?__('Yes') : __('No') }} </td>
                <td>{{ $category->recorded_depreciation ?__('Yes') : __('No') }} </td>
                <td>
                    <a href="{{ route('asset_categories.edit', $category->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                        <i class="ti ti-pencil"></i>
                    </a>
                    <form action="{{ route('asset_categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
