@extends('layouts.admin')
@section('page-title')
    {{__('Manage Fixed Assets')}}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Fixed Assets')}}</li>
@endsection
{{-- @push('datatable-js')
<script >
$(document).ready(function () {
    $('.datatable').DataTable({
        dom: '<"d-flex justify-content-between top-table-bar"l<"flex-fill search-top-bar"f>B>tip',
        processing: true,
        serverSide: true,
        ajax:{
            "url": "{{ url('fixed-assets/json/list') }}",
            "dataType": "json",
            "type": "GET",
            "data":{ 
                _token: "{{csrf_token()}}",
                @if (isset(request()->category))
                category: {{ request()->category }}
                @endif
            }
        },
        columns: [
            { "data": "asset_name" },
            { "data": "sku" },
            { "data": "purchase_date" },
            { "data": "purchase_price" },
            { "data": "category_id" },
            { "data": "location" },
            { "data": "condition" },
            { "data": "status" },
            { "data": "action", "className": "action_row" }
        ],
        buttons: [
            {
                extend: 'copy',
                exportOptions: {
                    columns: ':not(.action_row)'
                },
                text: 'copy <i class="ti ti-copy pe-1"></i>',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':not(.action_row)'
                },
                text: 'csv <i class="ti ti-table pe-1"></i>',
                className: 'btn btn-sm btn-primary'
            },
            {
                extend: 'excel',
                exportOptions: {
                    columns: ':not(.action_row)'
                },
                text: 'excel <i class="ti ti-table pe-1"></i>',
                className: 'btn btn-sm btn-success text-white'
            },
            {
                extend: 'print',
                autoPrint: true,
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                customize: function (win) {
                    $(win.document.body).find('h1').addClass('Title').css('textAlign', 'center');
                    $.ajax({
                        url: '/userinfo/img',
                        method: 'GET',
                        success: function(response) {
                            var background_image = '<img src="' + response.background + '" alt="Company Logo" style="position: absolute;top: 0;bottom: 0;left: 0;right: 0;width: 100%;height: 100%;opacity: 0.1;object-fit: contain;">';
                            var logo_image = '<img src="' + response.logo + '" alt="Company Logo" >';
                            var settings_data = response.settings;

                            $(win.document.body).prepend(background_image);
                            $(win.document.body).prepend('<div class="top-header row"></div>');
                            $(win.document.body).find('.top-header').prepend('<div class="col" style="text-align: right;">' + logo_image + '</div>');
                            $(win.document.body).find('.top-header').prepend('<div class="col" style="width: 100%;max-width: 115px;" >' + settings_data + '</div>');
                            
                            var titleElement = $(win.document.body).find('.Title');
                            var fullText = titleElement.text();
                            var updatedText = fullText.replace("The Future ERP - ", "");
                            titleElement.text(updatedText);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText); 
                            alert('Failed to fetch image data.');
                        }
                    });
                },
                text: ' print <i class="ti ti-printer pe-1"></i>',
                className: 'btn btn-sm btn-danger text-white'
            }
        ],
        lengthMenu: [
            [15, 30, 50, 70 , 100],
            [15, 30, 50, 70 , 100]
        ]
    });
});
</script>
@endpush --}}

@section('action-btn')
    <div class="float-end">
        {{-- data-url="{{ route('fixed-assets.file.import') }}" href="{{route('fixed-assets.export')}}" --}}
        <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}"  data-ajax-popup="true" data-title="{{__('Import Fixed Assets CSV file')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a>
        <a  data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a>

        <a href="{{ route('fixed-assets.create') }}" title="{{__('Create New Asset')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="mt-2 {{isset($_GET['category'])?'show':''}}" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['fixed-assets.index'], 'method' => 'GET', 'id' => 'fixed_assets']) }}
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6">
                                <div class="btn-box">
                                    {{ Form::label('category', __('Category'),['class'=>'form-label']) }}
                                    {{ Form::select('category', $categories, isset($_GET['category'])?$_GET['category']:'', ['class' => 'form-control select','id'=>'choices-multiple', 'required' => 'required']) }}
                                </div>
                            </div>
                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-sm btn-primary"
                                   onclick="document.getElementById('fixed_assets').submit(); return false;"
                                   data-bs-toggle="tooltip" title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('fixed-assets.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                   title="{{ __('Reset') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-trash-off "></i></span>
                                </a>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                    <table class="table datatable">
    <thead>
        <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Reference Number') }}</th>
            <th>{{ __('Description') }}</th>
            <th>{{ __('Category') }}</th>
            <th>{{ __('Measurement Unit') }}</th>
            <th>{{ __('Tax Percentage') }}</th>
            <th>{{ __('Barcode') }}</th>
            <th>{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($fixedAssets as $asset)
            <tr>
                <td>
                    {{ App::isLocale('ar') ? $asset->arabic_name : $asset->english_name }}
                </td>
                <td>{{ $asset->reference_number }}</td>
                <td>{{ $asset->description }}</td>
                <td>{{ $asset->kind->arabic_name ?? __('N/A') }}</td>
                <td>{{ $asset->unit->name }}</td>
                <td>{{ $asset->tax->rate }}%</td>
                <td>{{ $asset->barcode }}</td>
                <td>
                    <a href="{{ route('fixed-assets.edit', $asset->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                        <i class="ti ti-pencil"></i>
                    </a>
                    <form action="{{ route('fixed-assets.destroy', $asset->id) }}" method="POST" style="display:inline-block;">
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
