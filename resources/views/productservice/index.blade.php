@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Product & Services') }}
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Product & Services') }}</li>
@endsection

@push('datatable-js')
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable({
                           processing: true,
                           // Pagination
            serverSide: true,
            pagingType: "full_numbers",
                renderer: {
                    header: 'jqueryui',
                    pagingButton: 'bootstrap',
                    pagingContainer: 'bootstrap'
                },
            language: {
                paginate: {
                    previous: "<",
                    next: ">"
                }
            },
            order: [],
            columnDefs: [
                { orderable: false, targets: [5] } // Adjust targets as needed
            ],
            dom: '<"top"lfB>t<"bottom"i>', // Remove pagination from default DOM
              initComplete: (settings, json)=>{
        $('#table1_paginate').appendTo('body');
    },
             
                dom: '<"d-flex justify-content-between top-table-bar"l<"flex-fill search-top-bar"f>B>tip',
                   order: [], // No initial sorting
    columnDefs: [
        { orderable: false, targets: [6, -1] } // Adjust your targets
    ],
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ url('productservice/json/list') }}",
                    "dataType": "json",
                    "type": "GET", // it was POST ♦
                    "data": {
                        _token: "{{ csrf_token() }}",
                        @if (isset(request()->category))
                            category: {{ request()->category }}
                        @endif
                    }

                },
                columns: [{
                        "data": "code"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "parent"
                    },
                    {
                        "data": "category_id"
                    },
                    {
                        "data": "unit_id"
                    },
                    {
                        "data": "type"
                    },
                    {
                        "data": "action",
                        "className": "action_row"
                    }
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
                        customize: function(win) {
                            $(win.document.body).find('h1').addClass('Title').css('textAlign',
                                'center');
                            $.ajax({
                                url: '{{ route('userinfo.img') }}',
                                method: 'GET',
                                success: function(response) {

                                    var background_image = '<img src="' + response
                                        .background +
                                        '" alt="Company Logo" style="position: absolute;top: 0;bottom: 0;left: 0;right: 0;width: 100%;height: 100%;opacity: 0.1;object-fit: contain;">';
                                    var logo_image = '<img src="' + response.logo +
                                        '" alt="Company Logo"  width="100px">';
                                    var settings_data = response.settings;

                                    $(win.document.body).prepend(background_image);
                                    $(win.document.body).prepend(
                                        '<div class="top-header row"></div>');
                                    $(win.document.body).find('.top-header').prepend(
                                        '<div class="col" style="text-align: right;width:100px;">' +
                                        logo_image + '</div>');
                                    $(win.document.body).find('.top-header').prepend(
                                        '<div class="col" style="width: 100%;max-width: 115px;" >' +
                                        settings_data + '</div>');


                                    var titleElement = $(win.document.body).find(
                                        '.Title');
                                    var fullText = titleElement.text();
                                    var updatedText = fullText.replace(
                                        "The Future ERP - ", "");
                                    titleElement.text(updatedText);
                                },
                                error: function(xhr, status, error) {
                                    console.error(xhr
                                    .responseText); // عرض الخطأ في وحدة التحكم
                                    alert('Failed to fetch image data.');
                                }
                            });
                        },
                        text: ' print <i class="ti ti-printer pe-1"></i>',
                        className: 'btn btn-sm btn-danger text-white'
                    }
                ],
                lengthMenu: [
                    [15, 30, 50, 70, 100],
                    [15, 30, 50, 70, 100]
                ]
            });
        });
    </script>
@endpush
@section('action-btn')
    <div class="float-end">
        {{-- <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('productservice.file.import') }}" data-ajax-popup="true" data-title="{{__('Import product CSV file')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('productservice.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a> --}}

        <a href="#" data-size="lg" data-url="{{ route('productservice.create') }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" title="{{ __('Create New Product') }}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class=" mt-2 {{ isset($_GET['category']) ? 'show' : '' }}" id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['productservice.index'], 'method' => 'GET', 'id' => 'product_service']) }}
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6">
                                <div class="btn-box">
                                    {{ Form::label('category', __('Category'), ['class' => 'form-label']) }}
                                    {{ Form::select('category', $category, isset($_GET['category']) ? $_GET['category'] : '', ['class' => 'form-control select', 'id' => 'choices-multiple']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 ms-2">
                                <div class="btn-box">
                                    {{ Form::label('parents', __('parents'), ['class' => 'form-label']) }}
                                    {{ Form::select('parents', $parents, isset($_GET['parents']) ? $_GET['parents'] : '', ['class' => 'form-control select', 'id' => 'choices-multiple']) }}
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 ms-2">
                                <div class="btn-box">
                                    {{ Form::label('warehouseProduct', __('Select Warehouse'), ['class' => 'form-label']) }}
                                    {{ Form::select('warehouseProduct', $warehouseProduct, isset($_GET['warehouseProduct']) ? $_GET['warehouseProduct'] : '', ['class' => 'form-control select', 'id' => 'choices-multiple']) }}
                                </div>
                            </div>
                            <div class="col-auto float-end ms-2 mt-4">
                                <a href="#" class="btn btn-lg btn-primary"
                                    onclick="document.getElementById('product_service').submit(); return false;"
                                    data-bs-toggle="tooltip" title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>
                                <a href="{{ route('productservice.index') }}" class="btn btn-lg btn-danger"
                                    data-bs-toggle="tooltip" title="{{ __('Reset') }}">
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
                                    <th>{{ __('code') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Parent') }}</th>
                                    {{-- <th>{{__('Tax')}}</th> --}}
                                    <th>{{ __('Category') }}</th>
                                    {{-- <th>{{__('attribute')}}</th> --}}
                                    <th>{{ __('Unit') }}</th>
                                    {{-- <th>{{__('Quantity')}}</th> --}}
                                    <th>{{ __('Type') }}</th>
                                    {{-- <th>{{__('manufacturable')}}</th> --}}
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                            <div id="custom-pagination" class="d-flex justify-content-end mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
