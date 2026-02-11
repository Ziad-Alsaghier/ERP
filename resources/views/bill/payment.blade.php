{{ Form::open(array('route' => array('bill.payment', $bill->id),'method'=>'post','enctype' => 'multipart/form-data')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Date'),['class'=>'form-label']) }}
            {{ Form::date('date', '', array('class' => 'form-control ','required'=>'required')) }}

        </div>
        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}
            {{ Form::number('amount',$bill->getDue(), array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}

        </div>
        {{-- <div class="form-group col-md-6">
            {{ Form::label('account_id', __('Account'),['class'=>'form-label']) }}
            {{ Form::select('account_id',$accounts,null, array('class' => 'form-control ','required'=>'required')) }}
        </div> --}}
        <div class="form-group col-md-6">
            <div class="btn-box">
                {{ Form::label('account_id', __('Account'), ['class' => 'form-label']) }}
                <select name="account_id" class="form-control select2" id='account_id' required="required" style="direction: ltr; text-align:left;">
                    <option value="" class="ms-5" selected disabled>{{__(' --- Select Account ---')}}</option>

                    @foreach ($accounts_type as $type)
                    <optgroup label="{{ __($type['name']) }}">
                        @foreach ($accounts as $chartAccount)
                        @if ($type['id'] == $chartAccount['type'])
                        @if ($type['name'] == 'Assets')
                        <option value="{{ $chartAccount['id'] }}" class="subAccount" {{ isset($_GET['account']) && $chartAccount['id'] == $_GET['account'] ? 'selected' : ''}}>
                            {!! $chartAccount['code'] . ' - ' . __($chartAccount['name']) !!}
                        </option>

                        {{-- استدعاء دالة لتكرار عرض الحسابات الفرعية --}}
                        @include('partials.sub-accounts', ['subAccounts' => $subAccounts, 'parent_id' => $chartAccount['id'], 'level' => 1])
                        @endif
                        @endif
                        @endforeach
                    </optgroup>
                    @endforeach


                </select>

            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('reference', __('Reference'),['class'=>'form-label']) }}
            {{ Form::text('reference', '', array('class' => 'form-control')) }}

        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {{ Form::textarea('description', '', array('class' => 'form-control','rows'=>3)) }}
        </div>


        <div class="col-md-6 form-group">
            {{ Form::label('add_receipt', __('Payment Receipt'), ['class' => 'form-label']) }}
            <div class="choose-file ">
                <label for="file" class="form-label">
                    <input type="file" name="add_receipt" id="image" class="form-control">
                </label>
                <p class="upload_file"></p>
            </div>
        </div>

    </div>
    <div class="modal-footer">

        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Add')}}" class="btn  btn-primary">
    </div>

</div>
{{ Form::close() }}