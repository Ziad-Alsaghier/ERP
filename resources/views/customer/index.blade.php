@extends('layouts.admin')
@php
   // $profile=asset(Storage::url('uploads/avatar/'));
$profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp
@push('script-page')
    <script>
        $(document).on('click', '#billing_data', function () {
            $("[name='shipping_name']").val($("[name='billing_name']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_phone']").val($("[name='billing_phone']").val());
            $("[name='shipping_zip']").val($("[name='billing_zip']").val());
            $("[name='shipping_address']").val($("[name='billing_address']").val());
        })

    </script>
@endpush


@push('datatable-js')
<script >

$(document).ready(function () {
        $('.datatable').DataTable({
            dom: '<"d-flex justify-content-between top-table-bar"l<"flex-fill search-top-bar"f>B>tip',
            processing: true,
            serverSide: true,
            ajax:{
                    "url": "{{ url('customer/json/list') }}",
                    "dataType": "json",
                    "type": "GET", // it was POST ♦
                    "data":{ _token: "{{csrf_token()}}"}
                    },
            columns: [
                { "data": "id" },
                { "data": "name" },
                { "data": "contact" },
                { "data": "email" },
                { "data": "balance" },
                { "data": "action" ,"className": "action_row"}
            ],
            searchCols: [
            { search: 'id' }, // id (row 1)
            { search: 'name' }, // name (row 2)
            { search: 'contact' }, // contact (row 3)
            { search: 'email' }, // email (row 4)
                null, // balance (not included in search)
                null  // action (not included in search)
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
                                var settings_data = response.settings ;

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
            [15, 30, 50, 70 , 100, 1000000],
            [15, 30, 50, 70 , 100, 'All']
        ]
        });
    });


        </script>
@endpush




@section('page-title')
    {{__('Manage Customers')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Manage Customers')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('customer.file.import') }}" data-ajax-popup="true" data-title="{{__('Import customer CSV file')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('customer.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a>

        <a href="#" data-size="lg" data-url="{{ route('customer.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create Customer')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Contact') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Balance') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
