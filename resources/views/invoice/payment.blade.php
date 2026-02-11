
@php
    $totalQuantity=0;
    $totalRate=0;
    $totalTaxPrice=0;
    $finaltotal = 0;
@endphp

@foreach($invoice->items as $key => $item)
    @php
        $description = json_decode($item->description, true);
        if (is_array($description) && isset($description['info']['type']) && $description['info']['type'] == 'custom') {
            // حساب الإجمالي للـ custom
            $total_with_tax = 0; // قيمة افتراضية في حال عدم وجود "الاجمالي بعد الضريبة"
            foreach($description as $key_desc => $value) {
                if ($key_desc == 'التسعيرة النهائية') {
                    foreach ($value as $key_val => $val) {
                        if ($key_val == 'الاجمالي بعد الضريبة') {
                            $total_with_tax = floatval(str_replace(',', '', $val));
                        }
                    }
                }
            }
            $finaltotal += $total_with_tax ;
        } else {
            // حساب الإجمالي للعنصر العادي
            $totalTaxPrice = 0;
            if (!empty($item->tax)) {
                $getTaxData = Utility::getTaxData();
                foreach (explode(',', $item->tax) as $tax) {
                    $taxPrice = \Utility::taxRate($getTaxData[$tax]['rate'], $item->price, $item->quantity);
                    $totalTaxPrice += $taxPrice;
                }
            }
            $finaltotal += ($item->price * $item->quantity) + $totalTaxPrice;
        }
    @endphp
@endforeach

{{ Form::open(array('route' => array('invoice.payment', $invoice->id),'method'=>'post','enctype' => 'multipart/form-data')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group  col-md-6">
            {{ Form::label('date', __('Date'),['class'=>'form-label']) }}
            {{-- {{ Form::date('date', '', array('class' => 'form-control ','required'=>'required')) }} --}}
            {{ Form::input('datetime-local', 'date', \Carbon\Carbon::now()->format('Y-m-d\TH:i'), ['class' => 'form-control', 'required' => 'required']) }}

        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}

            {{ Form::number('amount',$finaltotal, array('class' => 'form-control','required'=>'required','step'=>'0.01' , 'placeholder'=>__('Enter Amount'))) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('account_id', __('Account'),['class'=>'form-label']) }}
            {{ Form::select('account_id',$accounts,null, array('class' => 'form-control select','required'=>'required')) }}
        </div>

        <div class="form-group  col-md-6">
            {{ Form::label('reference', __('Reference'),['class'=>'form-label']) }}
            {{ Form::text('reference', '', array('class' => 'form-control' , 'placeholder' => __('Enter Reference'))) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {{ Form::textarea('description', '', array('class' => 'form-control','rows'=>3 , 'placeholder'=>__('Enter Description'))) }}
        </div>

        <div class="col-md-6 form-group">
            {{ Form::label('add_receipt', __('Payment Receipt'), ['class' => 'form-label']) }}
            <div class="choose-file form-group">
                <label for="file" class="form-label">
                    <input type="file" name="add_receipt" id="image" class="form-control" >
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

