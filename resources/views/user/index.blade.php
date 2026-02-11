@extends('layouts.admin')
@php
    // $profile=asset(Storage::url('uploads/avatar/'));
    $profile = \App\Models\Utility::get_file('uploads/avatar');
@endphp
@section('page-title')
    @if (\Auth::user()->type == 'super admin')
        {{ __('Manage Companies') }}
    @else
        {{ __('Manage User') }}
    @endif
@endsection
@push('script-page')
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    @if (\Auth::user()->type == 'super admin')
        <li class="breadcrumb-item">{{ __('Companies') }}</li>
    @else
        <li class="breadcrumb-item">{{ __('User') }}</li>
    @endif
@endsection
@section('action-btn')
    <div class="float-end">
        @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'HR')
            <a href="{{ route('user.userlog') }}" class="btn btn-primary btn-sm {{ Request::segment(1) == 'user' }}"
                data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('User Logs History') }}"><i
                    class="ti ti-user-check"></i>
            </a>
        @endif
        @can('create user')
            <a href="#" data-size="lg" data-url="{{ route('users.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ \Auth::user()->type == 'super admin' ?  __('Create Company')  : __('Create User') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection


@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                @if (\Auth::user()->type == 'super admin')
                                <th>#</th>
                                <th>{{ __('Avatar') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Created on') }}</th>
                                <th>{{ __('Last Login') }}</th>
                                <th>{{ __('Plan Name') }}</th>
                                <th>{{ __('Plan Expired') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Users') }}</th>
                                <th>{{ __('Customers') }}</th>
                                <th>{{ __('Vendors') }}</th>
                                <th>{{ __('Action') }}</th>
                                @else
                                <th>{{ __('Avatar') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Created on') }}</th>
                                <th>{{ __('Last Login') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        @if (\Auth::user()->type == 'super admin')
                            @foreach ($users as $user)
                            <tr>
                                <td>{{$user->id}}</td>
                                <td><img src="{{ !empty($user->avatar) ? asset(Storage::url('uploads/avatar/' . $user->avatar)) : asset(Storage::url('uploads/avatar/avatar.png')) }}" title="{{ $user->name }}" class="avatar rounded-circle avatar-sm"></td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{ $user->created_at->format('Y-m-d')}}<br> {{ $user->created_at->format('H:i:s')}}</td>

                                <td>
                                    @if (!empty($user->last_login_at))
                                    {{ \Carbon\Carbon::parse($user->last_login_at)->format('Y-m-d') }}<br> {{ \Carbon\Carbon::parse($user->last_login_at)->format('H:i:s') .' KSA ' }}
                                    @else
                                    <span class="text-danger">{{__('not found')}}</span>
                                    @endif
                                </td>


                                <td>@if (isset($user->currentPlan->name))
                                    {!!__($user->currentPlan->name) . '<br> /' . __($user->currentPlan->duration)!!}
                                @else
                                    <span class="text-danger">{{__('not found')}}</span>
                                @endif
                                </td>
                                <td>
                                    @if (isset($user->plan_expire_date))
                                    {{$user->plan_expire_date}}
                                    @else
                                    <span class="text-danger">{{__('not found')}}</span>
                                @endif
                                </td>
                                <td>
                                    @if ($user->is_enable_login == 1)
                                    <span class="status_badge badge bg-primary p-2 px-3 rounded">{{ __('Active') }}</span>
                                    @elseif ($user->is_enable_login == 0)
                                    <span class="status_badge badge bg-danger p-2 px-3 rounded">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td data-bs-toggle="tooltip" title="{{ __('Users') }}"><i class="ti ti-users card-icon-text-space"></i>{{ $user->totalCompanyUser($user->id) }}</td>
                                <td data-bs-toggle="tooltip" title="{{ __('Customers') }}"><i class="ti ti-users card-icon-text-space"></i>{{ $user->totalCompanyCustomer($user->id) }}</td>
                                <td data-bs-toggle="tooltip" title="{{ __('Vendors') }}"><i class="ti ti-users card-icon-text-space"></i>{{ $user->totalCompanyVender($user->id) }}</td>
                                <td>
                                            <button type="button" class="btn  " data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">

                                                <a href="{{ route('users.confirm', \Crypt::encrypt($user->id)) }}" class="dropdown-item">
                                                <i class="ti ti-link"></i>
                                                <span> {{ __('confirm account') }}</span>
                                                </a>



                                                    <a href="#" data-url="{{ route('company.info', $user->id) }}" data-size="lg" data-ajax-popup="true" class="dropdown-item" data-title="{{ __('Company Info') }}"><i class="ti ti-building-store"></i> <span>{{ __('AdminHub') }}</span></a>
                                                    <a href="#" data-url="{{ route('plan.upgrade', $user->id) }}" data-size="lg" data-ajax-popup="true" class="dropdown-item" data-title="{{ __('Upgrade Plan') }}"><i class="ti ti-upload"></i> <span>{{ __('Upgrade Plan') }}</span></a>



                                                        <a href="#!" data-size="lg"
                                                            data-url="{{ route('users.edit', $user->id) }}"
                                                            data-ajax-popup="true" class="dropdown-item"
                                                            data-bs-original-title="{{ \Auth::user()->type == 'super admin' ?  __('Edit Company')  : __('Edit User') }}">
                                                            <i class="ti ti-pencil"></i>
                                                            <span>{{ __('Edit') }}</span>
                                                        </a>



                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['users.destroy', $user['id']],
                                                            'id' => 'delete-form-' . $user['id'],
                                                        ]) !!}
                                                        <a href="#!" class="dropdown-item bs-pass-para">
                                                            <i class="ti ti-archive"></i>
                                                            <span>
                                                                @if ($user->delete_status != 0)
                                                                    {{ __('Delete') }}
                                                                @else
                                                                    {{ __('Restore') }}
                                                                @endif
                                                            </span>
                                                        </a>
                                                        {!! Form::close() !!}



                                                        <a href="{{ route('login.with.company', $user->id) }}"
                                                            class="dropdown-item"
                                                            data-bs-original-title="{{ __('Login As Company') }}">
                                                            <i class="ti ti-replace"></i>
                                                            <span> {{ __('Login As Company') }}</span>
                                                        </a>


                                                    <a href="#!"
                                                        data-url="{{ route('users.reset', \Crypt::encrypt($user->id)) }}"
                                                        data-ajax-popup="true" data-size="md" class="dropdown-item"
                                                        data-bs-original-title="{{ __('Reset Password') }}">
                                                        <i class="ti ti-adjustments"></i>
                                                        <span> {{ __('Reset Password') }}</span>
                                                    </a>



                                                    @if ($user->is_enable_login == 1)
                                                    <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                        class="dropdown-item">
                                                        <i class="ti ti-road-sign"></i>
                                                        <span class="text-danger"> {{ __('Login Disable') }}</span>
                                                    </a>
                                                @elseif ($user->is_enable_login == 0 && $user->password == null)
                                                    <a href="#" data-url="{{ route('users.reset', \Crypt::encrypt($user->id)) }}"
                                                        data-ajax-popup="true" data-size="md" class="dropdown-item login_enable"
                                                        data-title="{{ __('New Password') }}" class="dropdown-item">
                                                        <i class="ti ti-road-sign"></i>
                                                        <span class="text-success"> {{ __('Login Enable') }}</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                        class="dropdown-item">
                                                        <i class="ti ti-road-sign"></i>
                                                        <span class="text-success"> {{ __('Login Enable') }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        @foreach ($users as $user)
                            <tr>
                                <td><img src="{{ !empty($user->avatar) ? asset(Storage::url('uploads/avatar/' . $user->avatar)) : asset(Storage::url('uploads/avatar/avatar.png')) }}" title="{{ $user->name }}" class="avatar rounded-circle avatar-sm"></td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{ ucfirst($user->type) }}</td>
                                <td>{{ $user->created_at->format('Y-m-d')}}<br> {{ $user->created_at->format('H:i:s')}}</td>
                                <td>@if (!empty($user->last_login_at)){{ \Carbon\Carbon::parse($user->last_login_at)->format('Y-m-d') }}<br> {{ \Carbon\Carbon::parse($user->last_login_at)->format('H:i:s') }}@else {{__('not found')}}@endif</td>
                                <td>
                                    @if ($user->is_enable_login == 1)
                                    <span class="status_badge badge bg-primary p-2 px-3 rounded">{{ __('Active') }}</span>
                                    @elseif ($user->is_enable_login == 0)
                                    <span class="status_badge badge bg-danger p-2 px-3 rounded">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if (Gate::check('edit user') || Gate::check('delete user'))

                                    <button type="button" class="btn  " data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{ route('users.confirm', \Crypt::encrypt($user->id)) }}" class="dropdown-item">
                                                <i class="ti ti-link"></i>
                                                <span> {{ __('confirm account') }}</span>
                                                </a>
                                            @can('edit user')

                                                    <a href="#!" data-size="lg"
                                                        data-url="{{ route('users.edit', $user->id) }}"
                                                        data-ajax-popup="true" class="dropdown-item"
                                                        data-bs-original-title="{{ \Auth::user()->type == 'super admin' ?  __('Edit Company')  : __('Edit User') }}">
                                                        <i class="ti ti-pencil"></i>
                                                        <span>{{ __('Edit') }}</span>
                                                    </a>
                                            @endcan


                                                    @can('delete user')
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['users.destroy', $user['id']],
                                                        'id' => 'delete-form-' . $user['id'],
                                                    ]) !!}
                                                    <a href="#!" class="dropdown-item bs-pass-para">
                                                        <i class="ti ti-archive"></i>
                                                        <span>
                                                            @if ($user->delete_status != 0)
                                                                {{ __('Delete') }}
                                                            @else
                                                                {{ __('Restore') }}
                                                            @endif
                                                        </span>
                                                    </a>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    <a href="{{ route('login.with.company', $user->id) }}"
                                                        class="dropdown-item"
                                                        data-bs-original-title="{{ __('Login') }}">
                                                        <i class="ti ti-replace"></i>
                                                        <span> {{ __('Login') }}</span>
                                                    </a>


                                                <a href="#!"
                                                    data-url="{{ route('users.reset', \Crypt::encrypt($user->id)) }}"
                                                    data-ajax-popup="true" data-size="md" class="dropdown-item"
                                                    data-bs-original-title="{{ __('Reset Password') }}">
                                                    <i class="ti ti-adjustments"></i>
                                                    <span> {{ __('Reset Password') }}</span>
                                                </a>

                                                @if ($user->is_enable_login == 1)
                                                <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                    class="dropdown-item">
                                                    <i class="ti ti-road-sign"></i>
                                                    <span class="text-danger"> {{ __('Login Disable') }}</span>
                                                </a>
                                                @elseif ($user->is_enable_login == 0 && $user->password == null)
                                                    <a href="#" data-url="{{ route('users.reset', \Crypt::encrypt($user->id)) }}"
                                                        data-ajax-popup="true" data-size="md" class="dropdown-item login_enable"
                                                        data-title="{{ __('New Password') }}" class="dropdown-item">
                                                        <i class="ti ti-road-sign"></i>
                                                        <span class="text-success"> {{ __('Login Enable') }}</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                        class="dropdown-item">
                                                        <i class="ti ti-road-sign"></i>
                                                        <span class="text-success"> {{ __('Login Enable') }}</span>
                                                    </a>
                                                @endif

                                            </div>

                                        @endif
                                </td>
                            </tr>
                        @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script-page')
    <script>
        $(document).on('change', '#password_switch', function() {
            if ($(this).is(':checked')) {
                $('.ps_div').removeClass('d-none');
                $('#password').attr("required", true);

            } else {
                $('.ps_div').addClass('d-none');
                $('#password').val(null);
                $('#password').removeAttr("required");
            }
        });
        $(document).on('click', '.login_enable', function() {
            setTimeout(function() {
                $('.modal-body').append($('<input>', {
                    type: 'hidden',
                    val: 'true',
                    name: 'login_enable'
                }));
            }, 2000);
        });
    </script>
@endpush
