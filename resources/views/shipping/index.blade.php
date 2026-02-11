@extends('layouts.admin')
@section('page-title')
    {{__('Manage shipping')}}
@endsection
@push('script-page')
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('shipping')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
            <a href="#" data-url="{{ route('shipping.create') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Create')}}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Create')}}">
                <i class="ti ti-plus"></i>
            </a>
    </div>
@endsection


@section('content')

<div class="row">
    <div class="col-3">
        @include('layouts.website_setup')
    </div>
    <div class="col-9">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th> {{__('country')}}</th>
                            <th width="10%"> {{__('Action')}}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($shipping as $ship)
                        @if ($ship->parent == 0)
                            <tr class="font-style">
                                <td>{{ $ship->name }}</td>
                                <td class="Action">
                                    <span>
                                            <div class="action-btn bg-primary ms-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('shipping.edit',$ship->id) }}" data-ajax-popup="true" data-title="{{__('Edit shipping')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>
                                            </div>
                                            <div class="action-btn bg-danger ms-2">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['shipping.destroy', $ship->id],'id'=>'delete-form-'.$ship->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$ship->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                                                {!! Form::close() !!}
                                            </div>
                                    </span>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th> {{__('name')}}</th>
                            <th> {{__('parent')}}</th>
                            <th> {{__('amount')}}</th>
                            <th width="10%"> {{__('Action')}}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($shipping as $ship)
                        @if ($ship->parent !== 0)
                            

                            <tr class="font-style">
                                <td>{{ $ship->name }}</td>
                                <td>{{ $ship->parent_name }}</td>
                                <td>{{ \Auth::user()->priceFormat($ship->amount) }}</td>
                                <td class="Action">
                                    <span>
                                    @can('edit constant tax')
                                            <div class="action-btn bg-primary ms-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('shipping.edit',$ship->id) }}" data-ajax-popup="true" data-title="{{__('Edit shipping')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                            </div>
                                        @endcan
                                        @can('delete constant tax')
                                            <div class="action-btn bg-danger ms-2">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['shipping.destroy', $ship->id],'id'=>'delete-form-'.$ship->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$ship->id}}').submit();">
                                            <i class="ti ti-trash text-white"></i>
                                        </a>
                                                {!! Form::close() !!}
                                            </div>
                                        @endcan
                                    </span>
                                </td>
                            </tr>
                            @endif  
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
