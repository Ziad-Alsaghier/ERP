@extends('layouts.admin')

@section('page-title')
    {{__('Permissions')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Permissions')}}</li>
@endsection
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between w-100">
                                <h4>{{__('Manage Permissions')}}</h4>
                                <a href="#" data-url="{{ route('permissions.create') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Create New Permission')}}" class="btn btn-icon icon-left btn-primary">
                                    <i class="fa fa-plus"></i> {{__('Create')}}
                                </a>
                            </div>
                </div>
                <div class="card-body">
                    <div class="card-body p-0">
                        <div id="table-1_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-flush" id="dataTable">
                                            <thead>
                                                <tr>
                                                        <th> {{__('Permissions')}}</th>
                                                        <th class="text-center"> {{__('Action')}}</th>
                                                    </tr>
                                            </thead>

                                            <tbody>
                                                    @foreach ($permissions as $permission)
                                                        <tr>
                                                            <td>{{ $permission->name }}</td>

                                                            <td class="action d-flex justify-content-center fs-3">

                                                                <a href="#" class="edit-icon m-2" data-url="{{ route('permissions.edit',$permission->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Update permission')}}" class="btn btn-outline btn-xs blue-madison" data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                                                    <i class="ti ti-settings"></i>
                                                                </a>
                                                                {!! Form::open(['method' => 'DELETE' ,'class'=>'m-2', 'route' => ['permissions.destroy', $permission->id],'id'=>'delete-form-'.$permission->id]) !!}

                                                                <a href="#" class="delete-icon" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$permission->id}}').submit();">
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                                {!! Form::close() !!}

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
                </div>
            </div>
        </div>
    </div>

@endsection
