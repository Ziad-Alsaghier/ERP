{{ Form::model($shipping, array('route' => array('shipping.update', $shipping->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('name', __('name'),['class'=>'form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control font-style','required'=>'required')) }}
            @error('name')
            <small class="invalid-name" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('parent', __('parent'),['class'=>'form-label']) }}
            {{ Form::select('parent', $country , null , array('class' => 'form-control select','required'=>'required', 'id'=>'parent')) }}
            @error('parent')
            <small class="invalid-parent" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>

        <div class="form-group col-md-6"  id='amount_input'>
            {{ Form::label('amount', __('amount'),['class'=>'form-label']) }}
            {{ Form::number('amount', null, array('class' => 'form-control','step'=>'0.01')) }}
            @error('amount')
            <small class="invalid-amount" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}

<script>
    $(document).on('change', '#parent', function () {
    var parent = $(this).val();
    if (parent == 0) {
        $('#amount_input').hide();
    } else {
        $('#amount_input').show();
    }
    console.log(parent);
    });
</script>
