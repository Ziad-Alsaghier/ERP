
{{ Form::model($category, ['route' => ['product-category.update', $category->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Category Name'),['class'=>'form-label']) }}
            {{ Form::text('name', $category->name, array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter Category Name'))) }}
        </div>
        <div class="form-group col-md-12 d-block">
            {{ Form::label('type', __('Category Type'),['class'=>'form-label']) }}
            {{ Form::select('type',$types,null, array('class' => 'form-control select cattype ','required'=>'required')) }}
        </div>
        <div class="form-group col-md-12 d-block">
            {{ Form::label('parent', __('Category Parent'),['class'=>'form-label']) }}
            {{ Form::select('parent',$parents,null, array('class' => 'form-control select catparent')) }}
        </div>
        <div class="form-group col-md-12 account d-none">
            {{Form::label('chart_account_id',__('Account'),['class'=>'form-label'])}}
            <select class="form-control select" name="chart_account" id="chart_account">
            </select>
        </div>

           {{-- Image input --}}
    <div class="mb-3">
        <label for="image" class="form-label">Upload Image</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/*" onchange="previewImage(event)">
    </div>

    {{-- Image preview --}}
    <div class="mb-3">
        <img id="preview" src="{{ $category->image ? asset('storage/app/public/' . $category->image) : '' }}" alt="Preview" style="max-width: 200px; max-height: 200px;">
    </div>

        <div class="form-group col-md-12">
            {{ Form::label('color', __('Category Color'),['class'=>'form-label']) }}
            {{ Form::text('color', '', array('class' => 'form-control jscolor','required'=>'required')) }}
            <small>{{__('For chart representation')}}</small>
        </div>
        <div class="form-group col-md-6">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="required" name="default" />
                <label class="form-check-label f-w-600 pl-1" for="required">{{ __('default') }}</label>
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Save')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}


<script>

    //hide & show chartofaccount

    $(document).on('change', '.cattype', function ()
    {
        var type = $(this).val();
        if (type != 'product & service' && type != '') {
            $('.account').removeClass('d-none')
            $('.account').addClass('d-block');
        } else {
            $('.account').addClass('d-none')
            $('.account').removeClass('d-block');
        }
    });


    $(document).on('change', '#type', function () {
        var type = $(this).val();

        $.ajax({
            url: '{{route('productServiceCategory.getaccount')}}',
            type: 'POST',
            data: {
                "type": type,
                "_token": "{{ csrf_token() }}",
            },

            success: function (data) {
                $('#chart_account').empty();
                $.each(data.chart_accounts, function (key, value) {
                    $('#chart_account').append('<option value="' + key + '" class="subAccount">' + value + '</option>');
                    $.each(data.sub_accounts, function (subkey, subvalue) {
                        if(key == subvalue.account)
                        {
                            $('#chart_account').append('<option value="' + subvalue.id + '">' + '&nbsp; &nbsp;&nbsp;' + subvalue.name + '</option>');
                        }
                });
                });
            }

        });
    });
</script>

<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
