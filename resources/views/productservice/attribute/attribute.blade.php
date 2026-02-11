@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Product & Attributes') }}
@endsection


@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Product & Attributes') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex gap-2">
        <button id="toggle-view" class="btn btn-outline-secondary btn-sm" title="Toggle View">
            <i class="ti ti-layout-grid"></i>
        </button>

        <a href="#" data-size="lg" data-url="{{ route('productservice.attributes.create') }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" title="{{ __('Create Products Attributes') }}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i> {{ __('Create') }}
        </a>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).ready(function() {
            $('#search-name, #search-type').on('input change', function() {
                let nameFilter = $('#search-name').val().toLowerCase();
                let typeFilter = $('#search-type').val().toLowerCase();

                $('.attribute-row').each(function() {
                    let row = $(this);
                    let name = row.data('name');
                    let type = row.data('type');

                    let nameMatch = name.includes(nameFilter);
                    let typeMatch = !typeFilter || type === typeFilter;

                    row.toggle(nameMatch && typeMatch);
                });
            });

            // Bootstrap tooltip
            const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(el => new bootstrap.Tooltip(el));

            // View toggle
            $('#toggle-view').on('click', function() {
                $('#attribute-table-view').toggle();
                $('#attribute-grid-view').toggle();
                $(this).find('i').toggleClass('ti-layout-grid ti-layout-list');
            });
        });
    </script>
@endpush
@push('script-page')
    <script>
        function generateBadge(type) {
            switch (type) {
                case 'select':
                    return 'info';
                case 'numeric':
                    return 'warning';
                default:
                    return 'secondary';
            }
        }

        function renderOptions(options) {
            const visibleOptions = options.slice(0, 2).map(opt =>
                `<div style="max-width: 300px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
        ${
            opt.value
                ? `<span class="badge bg-primary">${opt.value}</span>`
                : '-'
        }
    </div>`
            ).join('');


            const hiddenCount = options.length - 2;
            const tooltipBadge = hiddenCount > 0 ?
                `<span class="badge bg-light text-dark" data-bs-toggle="tooltip" title="${options.map(opt => opt.value).join(', ')}">
                    +${hiddenCount}
               </span>` : '';

            return `<div class="d-flex flex-wrap gap-1">${visibleOptions}${tooltipBadge}</div>`;
        }

        function generateActionButtons(attribute) {
            const editUrl = `{{ route('productservice.attributes.edit', ':id') }}`.replace(':id', attribute.id);
            const deleteUrl = `{{ route('productservice.attributes.destroy', ':id') }}`.replace(':id', attribute.id);

            return `
            <a href="#" class="btn btn-sm btn-info"
               data-url="${editUrl}" data-ajax-popup="true" data-size="lg"
               title="Edit" data-title="Edit Product Attribute">
                <i class="ti ti-pencil text-white"></i>
            </a>
            <form method="POST" action="${deleteUrl}" id="delete-form-${attribute.id}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
                <a href="#" class="btn btn-sm btn-danger"
                   onclick="if(confirm('Are you sure you want to delete this attribute?')) {
                       document.getElementById('delete-form-${attribute.id}').submit();
                   }" title="Delete">
                    <i class="ti ti-trash text-white"></i>
                </a>
            </form>
        `;
        }

        function renderEmptyRow() {
            return `
            <tr>
                <td colspan="6" class="text-center text-muted py-5">
                    <i class="ti ti-database-off fs-2"></i>
                    <p class="mt-2">{{ __('No attributes available. Create your first one!') }}</p>
                </td>
            </tr>
        `;
        }

        function loadAttributes() {
            $.get('{{ route('productservice.attributes.ajax') }}', function(data) {
                const tbody = $('#attribute-table-view tbody');
                tbody.empty();

                if (!data.length) {
                    tbody.append(renderEmptyRow());
                    return;
                }

                data.forEach((attr, i) => {
                    const badge = generateBadge(attr.type);
                    const optionsHtml = renderOptions(attr.options);
                    const actionsHtml = generateActionButtons(attr);

                    tbody.append(`
                    <tr class="attribute-row"
                        data-name="${attr.name.toLowerCase()}"
                        data-type="${attr.type.toLowerCase()}">
                        <td>${i + 1}</td>
                        <td>${attr.name}</td>
                        <td><span class="badge bg-${badge}">${attr.type.charAt(0).toUpperCase() + attr.type.slice(1)}</span></td>
                        <td class="d-none d-md-table-cell">${attr.unit?.name ?? '—'}</td>
                        <td>${optionsHtml || "This Attribute Is Don't Have Options "}</td>
                        <td class="d-flex align-items-center gap-2">${actionsHtml}</td>
                    </tr>
                `);
                });

                $('[data-bs-toggle="tooltip"]').tooltip();
                $('#search-name, #search-type').trigger('input');
            });
        }

        $(document).ready(function() {
            loadAttributes(); // Load once on page load

            $('#refresh-attributes').on('click', function() {
                loadAttributes(); // Reload on demand
            });
        });
    </script>
@endpush
@push('script-page')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush


@section('content')
    <div class="row">
        <div class="col-12" style="margin-top:85px;">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Product Attributes') }}</h5>
                </div>
                <div class="card-body">
                    {{-- Filters --}}
                    <button id="refresh-attributes" class="btn btn-sm btn-primary mb-3">
                        <i class="ti ti-refresh"></i> {{ __('Refresh Attributes') }}
                    </button>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" id="search-name" class="form-control"
                                    placeholder="{{ __('Search by name') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select id="search-type" class="form-select">
                                <option value="">{{ __('All Types') }}</option>
                                <option value="select">Select</option>
                                <option value="numeric">Numeric</option>
                            </select>
                        </div>
                    </div>

                    {{-- Table View --}}
                    <div id="attribute-table-view" class="table-responsive mt-2">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('#') }}</th>
                                    <th>{{ __('Attribute Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th class="d-none d-md-table-cell">{{ __('Unit') }}</th>
                                    <th>{{ __('Options') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attributes as $index => $attribute)
                                    <tr class="attribute-row" data-name="{{ strtolower($attribute->name) }}"
                                        data-type="{{ strtolower($attribute->type) }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $attribute->name }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $attribute->type === 'select' ? 'info' : ($attribute->type === 'numeric' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst(__($attribute->type)) }}
                                            </span>
                                        </td>
                                        <td class="d-none d-md-table-cell">{{ $attribute->unit->name ?? '—' }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($attribute->options->take(2) as $option)
                                                    <span class="badge bg-secondary">{{ $option->value }}</span>
                                                @endforeach
                                                @if ($attribute->options->count() > 2)
                                                    <span class="badge bg-light text-dark" data-bs-toggle="tooltip"
                                                        title="{{ $attribute->options->pluck('value')->implode(', ') }}">
                                                        +{{ $attribute->options->count() - 2 }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="d-flex align-items-center gap-2">
                                            <a href="#" class="btn btn-sm btn-info"
                                                data-url="{{ route('productservice.attributes.edit', $attribute->id) }}"
                                                data-ajax-popup="true" data-size="lg" title="{{ __('Edit') }}"
                                                data-title="{{ __('Edit Product Attribute') }}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>

                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['productservice.attributes.destroy', $attribute->id],
                                                'id' => 'delete-form-' . $attribute->id,
                                            ]) !!}
                                            <a href="#" class="btn btn-sm btn-danger"
                                                onclick="if(confirm('{{ __('Are you sure you want to delete this attribute?') }}')) {
                                                document.getElementById('delete-form-{{ $attribute->id }}').submit();
                                            }"
                                                title="{{ __('Delete') }}">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="ti ti-database-off fs-2"></i>
                                            <p class="mt-2">{{ __('No attributes available. Create your first one!') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Grid View (hidden by default) --}}
                    <div id="attribute-grid-view" class="row g-3 d-none">
                        @forelse ($attributes as $attribute)
                            <div class="col-md-4 attribute-row" data-name="{{ strtolower($attribute->name) }}"
                                data-type="{{ strtolower($attribute->type) }}">
                                <div class="card border shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="mb-2">{{ $attribute->name }}</h6>
                                        <p class="mb-1">
                                            <span
                                                class="badge bg-{{ $attribute->type === 'select' ? 'info' : 'warning' }}">
                                                {{ ucfirst(__($attribute->type)) }}
                                            </span>
                                            <span class="text-muted float-end">{{ $attribute->unit->name ?? '—' }}</span>
                                        </p>
                                        <div class="d-flex flex-wrap gap-1 mt-2">
                                            @foreach ($attribute->options->take(2) as $option)
                                                <span class="badge bg-secondary">{{ $option->value }}</span>
                                            @endforeach
                                            @if ($attribute->options->count() > 2)
                                                <span class="badge bg-light text-dark" data-bs-toggle="tooltip"
                                                    title="{{ $attribute->options->pluck('value')->implode(', ') }}">
                                                    +{{ $attribute->options->count() - 2 }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between">
                                        <a href="#" class="btn btn-sm btn-outline-info"
                                            data-url="{{ route('productservice.attributes.edit', $attribute->id) }}"
                                            data-ajax-popup="true" data-size="lg" title="{{ __('Edit') }}">
                                            <i class="ti ti-pencil"></i>
                                        </a>

                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'route' => ['productservice.attributes.destroy', $attribute->id],
                                            'id' => 'delete-form-grid-' . $attribute->id,
                                        ]) !!}
                                        <a href="#" class="btn btn-sm btn-outline-danger"
                                            onclick="if(confirm('{{ __('Are you sure?') }}')) {
                                            document.getElementById('delete-form-grid-{{ $attribute->id }}').submit();
                                        }"
                                            title="{{ __('Delete') }}">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-5">
                                <i class="ti ti-database-off fs-2"></i>
                                <p class="mt-2">{{ __('No attributes found.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
