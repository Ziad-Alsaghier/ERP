    @extends('layouts.admin')
    @section('page-title')
        {{ __('Manage Product-Service & Income-Expense Category') }}
    @endsection
    @section('breadcrumb')
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item">{{ __('Category') }}</li>
    @endsection

    @section('action-btn')
        <div class="float-end">
            @can('create constant category')
                <a href="#" data-url="{{ route('design.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip"
                    title="{{ __('Create') }}" title="{{ __('Create') }}" data-title="{{ __('Create New Category') }}"
                    class="btn btn-sm btn-primary">
                    <i class="ti ti-plus"></i>
                </a>
            @endcan
        </div>
    @endsection

    @section('content')
        <div class="row">
            <div class="col-3">
                @include('layouts.design-ui')
            </div>
            <div class="col-9">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th> {{ __('Slideshow Name') }}</th>
                                        <th> {{ __('Parent Category') }}</th>
                                        <th> {{ __('Slide Image') }}</th>
                                        <th width="10%"> {{ __('Upload Date') }}</th>
                                        <th width="10%"> {{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($slideshows as $slide)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $slide->name }}</td>
                                            <td>
                                                @if ($slide->image)
                                                    <img src="{{ asset('storage/app/public/' . $slide->image) }}"
                                                        alt="Slide Image" width="100">
                                                @else
                                                    <span class="text-muted">No image</span>
                                                @endif
                                            </td>
                                            <td>{{ $slide->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <span>
                                                    <div class="action-btn bg-primary ms-2">
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center"
                                                            data-url="{{ route('slideshow.edit', $slide->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Edit SliderShow') }}"
                                                            data-size="lg" data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            data-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>

                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open([
                                                            'method' => 'GET',
                                                            'route' => ['slideshow.delete', $slide->id],
                                                            'id' => 'delete-form-' . $slide->id,
                                                        ]) !!}

                                                        <a href="#"
                                                            class="mx-3 btn btn-sm align-items-center bs-pass-para"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                            data-original-title="{{ __('Delete') }}"
                                                            data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="document.getElementById('delete-form-{{ $slide->id }}').submit();">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No slideshows found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
