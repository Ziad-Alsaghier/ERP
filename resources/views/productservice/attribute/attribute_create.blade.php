{{ Form::open(array('url' => route('productservice.attributes.attributeStore'),'enctype' => "multipart/form-data",'id'=>'myForm') ) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
    $plan= \App\Models\Utility::getChatGPTSettings();

    @endphp
    @if($plan->chatgpt == 1)
    <div class="text-end">
        <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['productservice']) }}"
            data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
            <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
        </a>
    </div>
    @endif
    {{-- end for ai module--}}
 <div class="container">
    <!-- Success Alert -->
    <div id="alertSuccess" class="alert alert-success alert-dismissible fade show d-none" role="alert">
        <span id="alertSuccessText"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Error Alert -->
    <div id="alertError" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
        <ul id="alertErrorList" class="mb-0"></ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <form method="POST" action="save_attribute.php" id="myForm">
        @csrf
        <div class="card p-4 rounded-card shadow-sm">


            <!-- Attribute Name & Unit -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Attribute Name') }}</label>
                    <input type="text" class="form-control" name="attribute_name" placeholder="Enter Attribute Name">
                    <div class="text-danger small" id="error-attribute_name"></div> <!-- Error here -->
                </div>
                <div class="col-md-6">
                    {{ Form::label(__('Unit'), __('Unit'),['class'=>'form-label']) }}
                    {!! Form::select('unit', $units,null, ['class'=>'form-control','rows'=>'2' ,]) !!}
                <div class="text-danger small" id="error-unit"></div>
                </div>
            </div>

            <!-- Type Selector -->
            <div class="mb-3">
                <label class="form-label">{{ __('Type') }}</label>
                <select class="form-select" name="type" id="typeSelector" >
                 @foreach ($types as $key=>$type)
                 <option value="{{ $type }}">{{ $key }}</option>
                 @endforeach>
                </select>
                <div class="text-danger small" id="error-type"></div>
            </div>

            <!-- Select/Multi-Select Options -->
            <div id="optionsContainer" class="mb-3 d-none">
                <label class="form-label">{{ __('Options') }}</label>
                <div id="optionsList">
                    <div class="input-group mb-2">
                        <input type="text" name="options[]" class="form-control" placeholder="Option">
                        <button class="btn btn-outline-danger" type="button"
                            onclick="$(this).closest('.input-group').remove();">🗑️</button>
                    </div>
                </div>
                <button class="btn btn-outline-success btn-sm" id="addOptionBtn" type="button">➕{{ __("Add new select Option") }}</button>
            </div>

            <!-- Numeric Field -->


            <!-- Submit -->
    </form>
</div>
{{-- <script>
    $('#typeSelector').on('change', function () {
        let type = $(this).val();
        if (type === 'select') {
            $('#optionsContainer').removeClass('d-none');
            $('#numericContainer').addClass('d-none');
        } else {
            $('#optionsContainer').addClass('d-none');
            $('#numericContainer').addClass('d-none');
        }
    });

    $('#addOptionBtn').on('click', function () {
        $('#optionsList').append(`
            <div class="input-group mb-2">
                <input type="text" name="options[]" class="form-control" placeholder="Option">
                <button class="btn btn-outline-danger" type="button" onclick="$(this).closest('.input-group').remove();">🗑️</button>
            </div>
        `);
    });
</script> --}}
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>

<script>
    $(document).ready(function () {
    // Show/hide options
    $('#typeSelector').on('change', function () {
        if ($(this).val() === 'select') {
            $('#optionsContainer').removeClass('d-none');
        } else {
            $('#optionsContainer').addClass('d-none');
        }
    });

    // Add option field
  $('#addOptionBtn').on('click', function () {
        $('#optionsList').append(`
            <div class="input-group mb-2">
                <input type="text" name="options[]" class="form-control" placeholder="Option">
                <button class="btn btn-outline-danger" type="button" onclick="$(this).closest('.input-group').remove();">🗑️</button>
            </div>
        `);
    });

    // Remove option field
    $(document).on('click', '.btn-outline-danger', function () {
        $(this).closest('.input-group').remove();
    });

    // Live validation on keyup and change
    $('#myForm input, #myForm select').on('keyup change', function () {
        validateField($(this));
    });

    function validateField($field) {
        let name = $field.attr('name');
        let errorId = 'error-' + name.replace(/\./g, '_');
        let $errorEl = $('#' + errorId);

        if ($field.prop('required') && !$field.val()) {
            $errorEl.text('This field is required.');
        } else if ($field.attr('minlength') && $field.val().length < $field.attr('minlength')) {
            $errorEl.text(`Minimum ${$field.attr('minlength')} characters required.`);
        } else {
            $errorEl.text('');
        }
    }

    // Handle submit with Axios
    const form = document.getElementById('myForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);
        axios.post(@json(route('productservice.attributes.attributeStore')), formData)
            .then(function (response) {
                $('[id^="error-"]').text('');
        $('#alertError').addClass('d-none').hide();
        $('#alertSuccess').addClass('d-none').hide();
           // ✅ Show success alert
                $('#alertSuccessText').text('Attribute created successfully!');
                $('#alertSuccess').removeClass('d-none').fadeIn();

                // ✅ Close modal properly
                let modal = bootstrap.Modal.getInstance($('#attributeModal'));
                modal.hide();

                // ✅ Reload after short delay
                setTimeout(() => window.location.reload(), 2000);
            })
            .catch(function (error) {
                if (error.response && error.response.data.error) {
                    let errors = error.response.data.error;

                    // Show global error alert
                    let $errorList = $('#alertErrorList').empty();
                    $.each(errors, function (key, messages) {
                        // Field-specific error
                        let errorId = 'error-' + key.replace(/\./g, '_');
                        let $errorEl = $('#' + errorId);
                        if ($errorEl.length) {
                            $errorEl.text(messages[0]);
                        }

                        // Add to global alert box
                        $errorList.append(`<li>${messages[0]}</li>`);
                    });

                    $('#alertError').removeClass('d-none').fadeIn();
                }
            });
    });
});
</script>



{{Form::close()}}



