@extends('layouts.admin')

@section('page-title')
    {{ __('Production Line Management') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Production Lines') }}</li>
@endsection

@section('action-btn')
    <div class="d-flex gap-2 justify-content-end">
        <a href="#" data-url="{{ route('production.line.create') }}" class="btn btn-sm btn-primary"
            data-ajax-popup="true" data-size="lg" title="{{ __('Create Line') }}">
            <i class="ti ti-plus"></i> {{ __('New Line') }}
        </a>
    </div>
@endsection

@section('content')
<div class="row g-4">

    <!-- Summary Cards -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <h6 class="text-muted">{{ __('Total Lines') }}</h6>
                <h3>{{ $productionLines->count() }}</h3>
            </div>
        </div>
    </div>
    <!-- Add more KPI Cards if needed -->

    <!-- Navigation Tabs -->
    <div class="col-12">
        <ul class="nav nav-pills mb-3" id="lineTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="list-tab" data-bs-toggle="pill" data-bs-target="#listView" type="button">
                    <i class="ti ti-list-details"></i> {{ __('List View') }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="grid-tab" data-bs-toggle="pill" data-bs-target="#gridView" type="button">
                    <i class="ti ti-layout-grid"></i> {{ __('Card View') }}
                </button>
            </li>
        </ul>

        <div class="tab-content" id="lineTabsContent">
            <!-- Table View -->
            <div class="tab-pane fade show active" id="listView">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Line Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Operators') }}</th>
                                    <th>{{ __('Products') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productionLines as $line)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $line->name }}</strong></td>
                                        <td>{{ $line->type?->name }}</td>
                                        <td>{{ $line->branch->name }}</td>
                                        <td>
                                            @foreach($line->operators as $op)
                                                <span class="badge bg-primary">{{ $op->employees->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($line->linProducts as $prod)
                                                <span class="badge bg-secondary">{{ $prod->products->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-end">
                                            <a href="#" data-url="{{ route('production.line.edit', $line->id) }}" data-ajax-popup="true"
                                                class="btn btn-sm btn-info" title="Edit">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['production.line.delete', $line->id], 'class' => 'd-inline']) !!}
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">{{ __('No production lines found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card View -->
     <div class="tab-pane fade" id="gridView">
    <div class="row">
        @forelse($productionLines as $line)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0 position-relative hover-card">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0 d-flex justify-content-between align-items-center">
                            <span><i class="ti ti-assembly"></i> {{ $line->name }}</span>
                            <span class="badge bg-info">{{ $line->type?->name }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="ti ti-building-bank"></i>
                            <strong>{{ __('Branch:') }}</strong>
                            <span class="text-muted">{{ $line->branch->name }}</span>
                        </div>

                        <div class="mb-2">
                            <i class="ti ti-users-group"></i>
                            <strong>{{ __('Operators') }}</strong>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @forelse($line->operators as $op)
                                    <span class="badge rounded-pill bg-light border text-dark">
                                        <i class="ti ti-user"></i> {{ $op->employees->name }}
                                    </span>
                                @empty
                                    <span class="text-muted">{{ __('No operators assigned') }}</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="mb-2">
                            <i class="ti ti-package"></i>
                            <strong>{{ __('Products') }}</strong>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @forelse($line->linProducts as $prod)
                                    <span class="badge rounded-pill bg-warning text-dark">
                                        <i class="ti ti-box"></i> {{ $prod->products->name }}
                                    </span>
                                @empty
                                    <span class="text-muted">{{ __('No products linked') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 d-flex justify-content-end gap-2">
                        <a href="#" data-url="{{ route('production.line.edit', $line->id) }}"
                            data-ajax-popup="true" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                            <i class="ti ti-pencil"></i>
                        </a>
                        {!! Form::open(['method' => 'DELETE', 'route' => ['production.line.type.delete', $line->id], 'class' => 'd-inline']) !!}
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                <i class="ti ti-trash"></i>
                            </button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="ti ti-alert-circle"></i> {{ __('No production lines found.') }}
                </div>
            </div>
        @endforelse
    </div>
</div>
        </div>
    </div>
</div>
@endsection
