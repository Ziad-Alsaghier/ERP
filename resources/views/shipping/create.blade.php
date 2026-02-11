{{ Form::open(array('url' => 'shipping','enctype' => 'multipart/form-data')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('name', __('name'),['class'=>'form-label']) }}
            {{Form::text('name',null,array('class'=>'form-control','required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('parent', __('parent'),['class'=>'form-label']) }}
            {{Form::select('parent',$country,null,array('class'=>'form-control select','required'=>'required' , 'id'=>'parent'))}}
        </div>
        <div class="form-group col-md-6" id='amount_input' style="display: none;">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}
            {{ Form::number('amount',0, array('class' => 'form-control','step'=>'0.01' , 'placeholder'=>__('Enter Amount'))) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
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
        // console.log(parent);
        });
</script>
