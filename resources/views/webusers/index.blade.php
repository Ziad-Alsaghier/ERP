@extends('layouts.admin')
@section('page-title')
    {{__('Manage Users')}}
@endsection
@section('action-btn')
    <div class="float-end">
            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="">
                <i class="ti ti-plus"></i>
            </a>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Users')}}</li>
@endsection
@section('content')

@php
// dd($webusers);
@endphp
<div class="row">
    <div class="col-sm-12">
        <div class="mt-2 " id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{__('user ID #')}}</th>
                                    <th>{{__('Avatar')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Contact')}}</th>
                                    <th>{{__('Email')}}</th>
                                    <th>{{__('Created Date')}}</th>
                                    <th>{{__('verified')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($webusers as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td><img style="width: 50px; height: 50px; border-radius: 50%;" src="{{ $user->avatar == null ?config('app.website_storage') . '../assets/images/user.png'  : config('app.website_storage') . $user->avatar  }}" alt=""></td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at }}</td>
                                        <td>{{ $user->email_verified_at == null ? 'No' : 'Yes' }}</td>
                                        <td> 
                                            @if($user->convent == 0)
                                            <a href="{{route('web_users.convert', $user->id)}}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="" data-bs-original-title="convert to customer">{{__('convert')}}</a> 
                                            @else
                                            @php
                                                $usercheck = App\Models\Customer::where('id', $user->customer_id)->first();
                                            @endphp
                                            @if($usercheck == null)
                                            <a href="{{route('web_users.convert', $user->id)}}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="" data-bs-original-title="convert to customer"><i class="ti ti-wand"></i></a>
                                            @else
                                            <a href="{{route('customer.show',\Crypt::encrypt($user->customer_id))}}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="" data-bs-original-title="convert to customer">{{__('View')}}</a>
                                            @endif
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
</div>
@endsection