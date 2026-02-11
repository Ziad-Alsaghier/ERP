
@extends('layouts.admin')
@section('page-title')
    {{__('Revenue detail')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('revenue.index')}}">{{__('Manage Revenues')}}</a></li>
    <li class="breadcrumb-item">{{ __('Revenue') . ' ' . _('NO.').$revenue->revenue_id }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice">
                        <a href="{{ route('revenue.pdf',\Crypt::encrypt($revenue->id)) }}" target="_blank" class="btn btn-sm btn-primary">{{ __('Print') }} <i class="ti ti-printer"></i></a>
                        <div class="invoice-print">
                            <div class="row invoice-title mt-2">
                                <div class="col-xs-12 col-sm-12 col-nd-4 col-lg-4 col-12">
                                    <h4>{{__('NO.').$revenue->revenue_id}}</h4>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-nd-4 col-lg-4 col-12 text-center">
                                    <h2>{{__('Revenue receipt')}}</h2>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-nd-4 col-lg-4 col-12 text-end">
                                    <h4 class="invoice-number">{{ $revenue->date }}</h4>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                {{ Form::model($revenue) }}

                                    <div class="row">
                                        <div class="form-group  col-md-12">
                                            {{ Form::label('customer_id', __('Customer'),['class'=>'form-label']) }}
                                            {{ Form::select('customer_id', $customers,null, array('class' => 'form-control select','required'=>'required','disabled')) }}
                                        </div>

                                        <div class="form-group  col-md-12">
                                            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}
                                            {{ Form::number('amount', null, array('class' => 'form-control','required'=>'required','step'=>'0.01','disabled')) }}
                                        </div>
                                        <div class="form-group  col-md-12">
                                            {{ Form::label('category_id', __('Category'),['class'=>'form-label']) }}
                                            {{ Form::select('category_id', $categories,null, array('class' => 'form-control select','required'=>'required','disabled')) }}
                                        </div>

                                        <div class="form-group  col-md-12">
                                            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
                                            {{ Form::textarea('description', null, array('class' => 'form-control','rows'=>3,'disabled')) }}
                                        </div>
                                        <div class="form-group  col-md-12">
                                            {{ Form::label('reference', __('Reference'),['class'=>'form-label']) }}
                                            {{ Form::text('reference', null, array('class' => 'form-control','disabled')) }}
                                        </div>
                                        <hr>
                                        <div class="form-group  col-md-6">
                                            {{ Form::label('account_id', __('Account'),['class'=>'form-label']) }}
                                            {{ Form::select('account_id',$accounts,null, array('class' => 'form-control select','required'=>'required','disabled')) }}
                                        </div>

                                        <div class="form-group col-md-6">
                                            {{Form::label('add_receipt',__('Payment Receipt'),['class' => 'col-form-label'])}}
                                            {{Form::file('add_receipt',array('class'=>'form-control', 'id'=>'files','disabled'))}}
                                            <img id="image" src="{{asset(Storage::url('uploads/revenue')).'/'.$revenue->add_receipt}}" class="mt-2" style="width:25%;"/>
                                        </div>
                                    </div>

                                {{ Form::close() }}
                            </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


@endsection





<script>
    document.getElementById('files').onchange = function () {
        var src = URL.createObjectURL(this.files[0])
        document.getElementById('image').src = src
    }
</script>
