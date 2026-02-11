@extends('layouts.admin')

@section('page-title')
    {{__('Manufacturing')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Manufacturing')}}</li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                {{ Form::open(array('url' => 'admin/manufacturing/add','enctype' => 'multipart/form-data')) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('source_id', __('Source'),['class'=>'form-label']) }}
                            {{ Form::select('source_id', $users,null, array('class' => 'form-control select','required'=>'required')) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('user_id', __('Users'),['class'=>'form-label']) }}
                            {{ Form::select('user_id', $users,null, array('class' => 'form-control select','required'=>'required')) }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
                    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
                </div>
            {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

@endsection
