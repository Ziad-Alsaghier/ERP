@extends('layouts.admin')

@section('page-title')
    {{__('Manage Product-Service & Income-Expense Category')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Category')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create constant category')
            <a href="#" data-url="{{ route('design.category.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Category Sittengs')}}" class="btn btn-sm btn-primary">
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
                                <th>{{__('Category')}}</th>
                                <th>{{__('Enabled')}}</th>
                                <th>{{__('Header')}}</th>
                                <th>{{__('Footer')}}</th>
                                <th width="10%">{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($categoriesUi as $categoryUi)
                                <tr>
                                    <td class="font-style">{{ $categoryUi->category->name }}</td>
                                    <td>
                                        <span class="badge {{ $categoryUi->is_enabled ? 'bg-success' : 'bg-danger' }}">
                                            {{ $categoryUi->is_enabled ? __('Enabled') : __('Disabled') }}
                                        </span>
                                    </td>
                                    <td><i class="ti {{ $categoryUi->section == "Header" ? 'ti-check' : 'ti-x' }}"></i></td>
                                    <td><i class="ti {{ $categoryUi->section == "Footer" ? 'ti-check' : 'ti-x' }}"></i></td>
                                    <td class="Action">
                                        @if ($categoryUi->main != 1)
                                            <span>
                                                @can('edit constant category')
                                                    <div class="action-btn bg-primary ms-2">
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('design.change.edit', $categoryUi->id) }}" data-ajax-popup="true" data-title="{{__('Edit  Category Ui')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('delete constant category')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['design.change.delete', $categoryUi->id], 'id'=>'delete-form-'.$categoryUi->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$categoryUi->id}}').submit();">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
                                            </span>
                                        @endif
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
