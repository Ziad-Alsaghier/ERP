@extends('layouts.admin')
@section('page-title')
    {{__('Manage orders')}}
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
    <li class="breadcrumb-item">{{__('Orders')}}</li>
@endsection
@section('content')

@php
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
                                    <th>{{__('Reference Number')}}</th>
                                    <th>{{__('Date')}}</th>
                                    <th>{{__('Customer')}}</th>
                                    <th>{{__('Amount')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @dd($weborders) --}}
                                @foreach ($weborders as $order)
                                @php
                                    $total = 0;
                                    $sum_price = 0;
                                    $total = json_decode($order->payment_details,true);
                                    $sum_price = $total['total'];
                                @endphp
                                <tr>
                                    <td><a class="btn btn-outline-primary" href="{{route('web_orders.show', $order->id )}}">{{ Auth::user()->ordersNumberFormat($order->id)}}</a></td>
                                    <td>{{ $order->created_at }}</td>
                                    <td>
                                        <a href="{{route('customer.show',\Crypt::encrypt($order->customer_id))}}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="" data-bs-original-title="convert to customer">{{__('View')}}</a>
                                    </td>
                                    <td>{{ Auth::user()->priceFormat($sum_price) }}</td>
                                    <td>{{ $order->status }}</td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href="{{route('web_orders.show', $order->id )}}"><i class="ti ti-eye"></i></a>
                                        <a class="btn btn-danger btn-sm" href="{{route('web_orders.delete', $order->id)}}"><i class="ti ti-trash text-white"></i></a>
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
