{{ Form::model($Attribute, array('route' => array('productservice.attributes.update', $Attribute->id), 'method' =>
'PUT','enctype' => "multipart/form-data")) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
    $plan= \App\Models\Utility::getChatGPTSettings();

    @endphp
    @if($plan->chatgpt == 1)
    <div class="text-end">
        <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true"
            data-url="{{ route('generate',['productservice']) }}" data-bs-placement="top"
            data-title="{{ __('Generate content with AI') }}">
            <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
        </a>
    </div>
    @endif
    {{-- end for ai module--}}
    <div class="container">
        <form method="POST" action="save_attribute.php">
            <div class="card p-4 rounded-card shadow-sm">


                <!-- Attribute Name & Unit -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                {{ Form::label('name', __('Name'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                                {{ Form::text('name',null, array('class' => 'form-control','required'=>'required')) }}
                            </div>

                            <div class="col-md-6">
                                {{ Form::label('unit', __('Name'),['class'=>'form-label']) }}<span class="text-danger">*</span>
                                {{ Form::select('unit_id', $units, $Attribute->unit_id, [
                                    'class' => 'form-control',
                                ]) }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">

                        @if ($Attribute->type == 'numeric')
                        {{ Form::label('type', __('Type'),['class'=>'form-label']) }}
                        {{ Form::text('type',$Attribute?->type, array('class' =>'form-control','required'=>'required')) }}

                        @else
                        <!-- Type Selector -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('Type') }}</label>
                            <select class="form-select" name="type" id="typeSelector" required>
                                <option value="{{ $Attribute->type }}">{{ $Attribute->type }}</option>

                            </select>
                        </div>

                        <!-- Select/Multi-Select Options -->
                        <div id="optionsContainer" class="mb-3 {{ $Attribute->type == 'select' ? '' : 'd-none' }}">
                            <label class="form-label">{{ __('Options') }}</label>
                            <div id="optionsList">
                              @foreach ($Attribute->options as $option)
    <div class="input-group mb-2 option-item" data-id="{{ $option->id }}">
        <input type="text" name="options[{{ $option->id }}]" value="{{ $option->value ?? '' }}"
            class="form-control" placeholder="Option">
        <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)">🗑️</button>
    </div>
@endforeach
                            <button class="btn btn-outline-success btn-sm" id="addOptionBtn" type="button">
                                ➕ {{ __("Add new select Option") }}
                            </button>
                        </div>
                        <!-- Numeric Field -->


                        @endif
                    </div>
                </div>

        </form>
    </div>
    <script>
  function removeOption(button) {
        const optionItem = $(button).closest('.option-item');
        const optionId = optionItem.data('id');

        if (optionId) {
            // أضف ID للمصفوفة المخفية
            $('<input>').attr({
                type: 'hidden',
                name: 'deleted_option_ids[]',
                value: optionId
            }).appendTo('form'); // ضعه داخل نفس الفورم
        }

        // أزل العنصر من الشاشة
        optionItem.remove();
    }

    $('#addOptionBtn').on('click', function () {
        $('#optionsList').append(`
            <div class="input-group mb-2">
                <input type="text" name="options[]" class="form-control" placeholder="Option">
                <button class="btn btn-outline-danger" type="button"
                    onclick="$(this).closest('.input-group').remove();">🗑️</button>
            </div>
        `);
    });

    
    </script>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Save')}}" class="btn  btn-primary">
</div>
{{Form::close()}}
