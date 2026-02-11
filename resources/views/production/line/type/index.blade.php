@extends('layouts.admin')
@section('page-title')
    {{ __('Production Lines Types') }}
@endsection




@section('action-btn')
    <div class="float-end">
        @can('create chart of account')
            <a href="#" data-url="{{ route('production.line.type.create') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                data-size="lg" data-ajax-popup="true" data-title="{{ __('Create New Account') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection





@section('content')
    <div class="row">
        <div class="col-3">
            @include('layouts.production_line_setup')
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-body table-border-style">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('#') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productionLineTypes as $productionLineType)
                                <tr class="attribute-row">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $productionLineType->name }}</td>

                                    <td class="d-flex align-items-center gap-2">
                                        <a href="#" class="btn btn-sm btn-info"
                                            data-url="{{ route('production.line.type.edit', $productionLineType->id) }}"
                                            data-ajax-popup="true" data-size="lg" title="{{ __('Edit') }}"
                                            data-title="{{ __('Edit Product Attribute') }}">
                                            <i class="ti ti-pencil text-white"></i>
                                        </a>

                                   
                                        

                                        <div class="action-btn bg-danger ms-2">
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['production.line.type.delete', $productionLineType->id],'id'=>'delete-form-'.$productionLineType->id]) !!}

                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" data-original-title="{{__('Delete')}}" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$productionLineType->id}}').submit();">
                                                <i class="ti ti-trash text-white text-white text-white"></i>
                                            </a>
                                        {!! Form::close() !!}
                                        </div>
                                    </td>
                                </tr>
                                    @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-5">
                                        <i class="ti ti-database-off fs-2"></i>
                                        <p class="mt-2">{{ __('No attributes available. Create your first one!') }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Font Awesome -->
    <style>
        table.dataTable thead {
            background: linear-gradient(to right, #e0f2ff, #cce5ff);
        }

        table.dataTable tbody tr:hover {
            background-color: #f0f8ff;
            transition: background-color 0.3s ease;
        }

        #productTable td,
        #productTable th {
            vertical-align: middle;
        }

        .btn-sm i {
            font-size: 0.85rem;
        }

        .dt-buttons .btn {
            margin-right: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 20px;
            padding: 5px 15px;
            border: 1px solid #ccc;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#productTable').DataTable({
                responsive: true,
                lengthChange: true,
                pageLength: 8,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-outline-secondary btn-sm'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-info btn-sm'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-success btn-sm'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-outline-dark btn-sm'
                    }
                ],
                language: {
                    searchPlaceholder: "🔍 Type to search...",
                    search: ""
                }
            });

            // Move buttons to a custom div
            table.buttons().container().appendTo('#tableButtons');
        });
    </script>
@endpush
