
@extends('layouts.admin')
@section('page-title')
    {{__('Payment Voucher')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('payment.index')}}">{{__('Manage payment')}}</a></li>
    <li class="breadcrumb-item">{{ __('Payment') . ' ' . _('NO.').$payment->payment_id }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice">
                        <div class="invoice-print">
                            <a href="{{ route('payment.pdf',$payment->id) }}" target="_blank"
                                class="btn btn-sm btn-primary">{{ __('Download') }}</a>

                            <div class="row invoice-title mt-2">
                                <div class="col-xs-12 col-sm-12 col-nd-4 col-lg-4 col-12">
                                    <h4>{{__('NO.').$payment->payment_id}}</h4>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-nd-4 col-lg-4 col-12 text-center">
                                    <h2>{{__('Payment')}}</h2>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-nd-4 col-lg-4 col-12 text-end">
                                    <h4 class="invoice-number">{{ $payment->date }}</h4>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                {{ Form::model($payment) }}

                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            {{ Form::label('vender_id', __('Vendor'),['class'=>'form-label']) }}
                                            {{ Form::select('vender_id', $venders,null, array('class' => 'form-control select','required'=>'required' , 'disabled')) }}
                                        </div>

                                        <div class="form-group col-md-12">
                                            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}
                                            {{ Form::number('amount', null, array('class' => 'form-control','required'=>'required','step'=>'0.01','disabled')) }}
                                        </div>

                                        <div class="form-group col-md-12">
                                            {{ Form::label('reference', __('Reference'),['class'=>'form-label']) }}
                                            {{ Form::text('reference', null, array('class' => 'form-control','disabled')) }}
                                        </div>

                                        <div class="form-group  col-md-12">
                                            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
                                            {{ Form::textarea('description', null, array('class' => 'form-control','rows'=>3,'disabled')) }}
                                        </div>

                                        <hr>
                                        
                                        <div class="form-group col-md-12">
                                            {{Form::label('add_receipt',__('Payment Receipt'),['class' => 'form-label'])}}
                                            {{Form::file('add_receipt',array('class'=>'form-control', 'id'=>'files','disabled'))}}
                                            <img id="image" class="mt-2" src="{{asset(Storage::url('uploads/payment')).'/'.$payment->add_receipt}}" style="width:25%;"/>
                                        </div>


                                        <div class="form-group col-md-6">
                                            {{ Form::label('category_id', __('Category'),['class'=>'form-label']) }}
                                            {{ Form::select('category_id', $categories,null, array('class' => 'form-control select','required'=>'required','disabled')) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('account_id', __('Account'),['class'=>'form-label']) }}
                                            {{ Form::select('account_id',$accounts,null, array('class' => 'form-control select','required'=>'required','disabled')) }}
                                        </div>





                                        <div class="form-group col-md-6">
                                            {{ Form::label('chart_account_id', __('Chart Of Account'),['class'=>'form-label']) }}
                                            {{ Form::select('chart_account_id',$chartAccounts,null, array('class' => 'form-control select','required'=>'required','disabled')) }}
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
