{{ Form::open(['url' => 'productservice', 'enctype' => 'multipart/form-data', 'class' => 'w-100 needs-validation', 'novalidate']) }}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />


<style>
    /* Dropzone container */
    .dropzone {
        border: 2px dashed #6c63ff;
        border-radius: 12px;
        background: #f8f9ff;
        padding: 30px;
        text-align: center;
        color: #6c757d;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
    }

    .dropzone:hover {
        background-color: #eef1ff;
        border-color: #4b44ff;
    }

    /* Preview layout */
    .dz-preview {
        display: inline-block;
        width: 150px;
        margin: 10px;
        vertical-align: top;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        position: relative;
        transition: transform 0.2s ease;
    }

    .dz-preview:hover {
        transform: scale(1.05);
    }

    /* Image */
    .dz-image img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        display: block;
    }

    /* File name and size */
    .dz-details {
        padding: 10px;
        text-align: center;
        font-size: 14px;
    }

    .dz-size,
    .dz-filename {
        margin: 0;
        color: #555;
    }

    /* Hide default success/error marks */
    .dz-success-mark,
    .dz-error-mark {
        display: none !important;
    }

    /* Remove button */
    .dz-remove {
        display: block;
        background: #ff4d4f;
        color: #fff;
        text-align: center;
        padding: 5px;
        margin: 10px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .dz-remove:hover {
        background: #cc0000;
    }
</style>


<div class="modal-body">
    {{-- Step Navigation --}}
    <ul class="nav nav-pills mb-3" id="product-step-tabs">
        <li class="nav-item">
            <a class="nav-link active" id="step1-tab" href="#">1. {{ __('Product Info') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="step2-tab" href="#">2. {{ __('Options & Attributes') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="step3-tab" href="#">3. {{ __('Gallery Images') }}</a>
        </li>
    </ul>

    {{-- Step 1 --}}
    <div id="step1-section" class="step-section">
        <div class="row">
            {{-- All Product Info Fields --}}
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                    {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Enter Product Name', 'required']) }}
                    <div class="invalid-feedback">{{ __('Please Enter Product Name') }}.</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('sku', __('SKU'), ['class' => 'form-label']) }}
                    {{ Form::text('sku', '', ['class' => 'form-control', 'placeholder' => 'Enter Product SKU', 'required']) }}
                    <div class="invalid-feedback">{{ __('Please Enter Product sku') }}.</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('sale_price', __('Sale Price'), ['class' => 'form-label']) }}
                    {{ Form::number('sale_price', '', ['class' => 'form-control', 'placeholder' => 'Enter Sale Price', 'step' => '0.01']) }}
                    <div class="invalid-feedback">{{ __('Please Enter Product Sale Price') }}.</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('purchase_price', __('Purchase Price'), ['class' => 'form-label']) }}
                    {{ Form::number('purchase_price', '', ['class' => 'form-control', 'placeholder' => 'Enter Purchase Price', 'step' => '0.01']) }}
                    <div class="invalid-feedback">{{ __('Please Enter Purchase Price') }}.</div>
                </div>
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}<span
                    class="text-danger">*</span>
                {{ Form::select('category_id', $category, null, ['class' => 'form-control select']) }}
                <div class="invalid-feedback">{{ __('Please add constant category.') }}</div>
                <div class="text-xs">
                    {{ __('Please add constant category. ') }}<a
                        href="{{ route('product-category.index') }}"><b>{{ __('Add Category') }}</b></a>
                </div>
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('unit_id', __('Unit'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('unit_id', $unit, null, ['class' => 'form-control select']) }}
                <div class="invalid-feedback">{{ __('Please Select Unit.') }}</div>
            </div>

            <div class="form-group col-md-12">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '2']) !!}
                <div class="invalid-feedback">{{ __('Please Enter description.') }}</div>
            </div>
        </div>
    </div>

    {{-- Step 2 --}}
    <div id="step2-section" class="step-section" style="display: none;">
        <div class="row">
            <div class="form-group col-md-6">
                {{ Form::label('parent', __('Parent'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('parentIds[]', $parents, null, ['class' => 'form-control select2', 'id' => 'choices-multiple1', 'multiple']) }}
            </div>

            <div class="form-group col-md-6">
                <label class="d-block form-label">{{ __('Attribute') }}</label>
                <select name="attributeIds[]" id="choices-multiple3" class="form-control select2 parent-select" multiple
                    required>
                    @foreach ($attributes as $attribute)
                        <option value="{{ $attribute->id }}" data-name="{{ $attribute->name }}"
                            data-type="{{ $attribute->type }}">
                            {{ $attribute->name }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">{{ __('Please add constant category.') }}</div>
            </div>

            {{-- Type --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="d-block form-label">{{ __('Type') }}</label>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input type" id="service" name="is_service"
                            value="0" checked>
                        <label class="custom-control-label form-label" for="service">{{ __('Product') }}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input type" id="product" name="is_service"
                            value="1">
                        <label class="custom-control-label form-label" for="product">{{ __('Service') }}</label>
                    </div>
                </div>
            </div>

            {{-- Manufacturable --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="d-block form-label">{{ __('Manufacturable') }}</label>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input type" id="non-manufacturable"
                            name="manufacturable" value="0" checked>
                        <label class="custom-control-label form-label"
                            for="non-manufacturable">{{ __('Non-Manufacturable') }}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input type" id="manufacturable" name="manufacturable"
                            value="1">
                        <label class="custom-control-label form-label"
                            for="manufacturable">{{ __('Manufacturable') }}</label>
                    </div>
                </div>
            </div>

            <div class="container attribute-selected"></div>

            @if (!$customFields->isEmpty())
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                        @include('customFields.formBuilder')
                    </div>
                </div>
            @endif
        </div>
    </div>
    {{-- Step 3 --}}
    <!-- Image Upload Section -->
    <div id="step3-section" class="step-section">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __('Upload Images') }}</label>
                    <small
                        class="form-text text-muted">{{ __('Drag and drop images here or click to upload.') }}</small>
                    <div class="dropzone" id="image-dropzone"></div>
                </div>
            </div>
        </div>
        <!-- Hidden real input -->
        <input type="file" id="real-file-input" name="images[]" multiple hidden>
    </div>

    <!-- Cropping Modal -->
    <div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="crop-image" style="max-width: 100%;">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="button" id="crop-btn">Crop & Add</button>
                </div>
            </div>
        </div>
    </div>


</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="button" class="btn btn-secondary" id="back-btn"
        style="display: none;">{{ __('Back') }}</button>
    <button type="button" class="btn btn-primary" id="next-btn">{{ __('Next') }}</button>
    <button type="submit" class="btn btn-primary" id="submit-btn"
        style="display: none;">{{ __('Create') }}</button>
</div>
{{ Form::close() }}

<script>
    window.attributesData = @json($attributes->keyBy('id'));
</script>
<script>
$(function() {
    select2();

    // Cache data once
    window.attributesData = window.attributesData || @json($attributes->keyBy('id'));

    // ✅ Toggle quantity
    $(document).on('click', '.type', function() {
        let showQuantity = $(this).val() === 'product';
        $('.quantity').toggleClass('d-none', !showQuantity).toggleClass('d-block', showQuantity);
    });

    // ✅ Handle attribute selection
    $(document).on('change', '#choices-multiple3', function() {
        let selectedIds = $(this).val() || [];
        let selectedAttributes = selectedIds.map(id => attributesData[id]).filter(Boolean);

        let $container = $('.attribute-selected').empty();
        if (!selectedAttributes.length) return;

        $container.append(`
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Attribute</th>
                        <th>Dynamic</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody id="attributes-body"></tbody>
            </table>
        `);

        buildAttributes(selectedAttributes);
    });

    // ✅ Build attributes
    function buildAttributes(attributes) {
        let $tbody = $('#attributes-body').empty();
        let manufacturable = $('input[name="manufacturable"]:checked').val();

        attributes.forEach((attr, i) => {
            let checkboxId = `dynamic-${i}`;
            let $checkbox = $('<input>', {
                type: 'checkbox',
                class: 'form-check-input dynamic-checkbox',
                id: checkboxId,
                value: '1',
                disabled: manufacturable === "0",
                checked: attr.is_dynamic == 1
            });

            // Hidden input to send is_dynamic value
            let $hiddenDynamic = $('<input>', {
                type: 'hidden',
                name: `attribute_is_dynamic[${attr.id}]`,
                value: attr.is_dynamic
            });

            let $input = createAttributeInput(attr);

            // ✅ Hide input if dynamic is checked
            if ($checkbox.is(':checked')) {
                $input.closest('.input-container').hide();
            }

            $checkbox.on('change', function() {
                // Update hidden input
                $hiddenDynamic.val(this.checked ? 1 : 0);

                // Toggle visibility of attribute input
                $input.closest('.input-container').toggle(!this.checked);
            });

            let $row = $('<tr>').append(
                $('<td>').text(attr.name || '-'),
                $('<td>').append($('<div>', { class: 'form-check' }).append($checkbox).append($hiddenDynamic)),
                $('<td>').append($('<div>', { class: 'input-container' }).append($input))
            );

            $tbody.append($row);
        });
    }

    // ✅ Create input/select based on attribute type
    function createAttributeInput(attr) {
        if (attr.type === "numeric") {
            return $('<input>', {
                type: 'number',
                class: 'form-control attribute-value-input',
                name: `attribute_value[${attr.id}]`,
                placeholder: 'Value number'
            });
        }

        let $select = $('<select>', {
            name: `attribute_value[${attr.id}]`,
            class: 'form-control',
            required: true
        });

        (attr.options || []).forEach(opt => {
            $select.append(new Option(opt.value, opt.id));
        });

        return $select;
    }

    // ✅ Handle manufacturable change globally
    $(document).on('change', 'input[name="manufacturable"]', function() {
        $('#choices-multiple3').trigger('change'); // rebuild table
    });

    // ✅ When modal is shown, trigger attribute rendering
    $(document).on('shown.bs.modal', '#add-product-modal', function() {
        $('#choices-multiple3').trigger('change');
    });
});
</script>


<script>
    $(document).ready(function() {
        let currentStep = 1;

        function showStep(step) {
            $(".step-section").hide();
            $("#step" + step + "-section").fadeIn(200);

            // Update active tab
            $("#product-step-tabs .nav-link").removeClass("active");
            $("#step" + step + "-tab").addClass("active");

            // Control button visibility
            $("#back-btn").toggle(step > 1);
            $("#next-btn").toggle(step < 3);
            $("#submit-btn").toggle(step === 3);

            currentStep = step;
        }

        function validateStep(step) {
            let isValid = true;

            $('#step' + step + '-section').find('input, select, textarea').each(function() {
                let $field = $(this);
                let isRequired = $field.prop('required');

                // Handle multi-select
                if ($field.prop("tagName") === "SELECT" && $field.prop("multiple")) {
                    if (isRequired && (!$field.val() || $field.val().length === 0)) {
                        $field.addClass('is-invalid');
                        isValid = false;
                    } else {
                        $field.removeClass('is-invalid');
                    }
                } else if (isRequired && !$field.val()) {
                    $field.addClass('is-invalid');
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                }
            });

            return isValid;
        }

        // On load
        showStep(1);

        // Tab click handlers
        $("#step1-tab").click(function(e) {
            e.preventDefault();
            showStep(1);
        });

        $("#step2-tab").click(function(e) {
            e.preventDefault();
            if (validateStep(1)) showStep(2);
        });

        $("#step3-tab").click(function(e) {
            e.preventDefault();
            if (validateStep(1) && validateStep(2)) showStep(3);
        });

        // Navigation buttons
        $("#next-btn").click(function() {
            if (currentStep === 1 && validateStep(1)) {
                showStep(2);
            } else if (currentStep === 2 && validateStep(2)) {
                showStep(3);
            }
        });

        $("#back-btn").click(function() {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        });

        // Final submit validation
        $('form').on('submit', function(e) {
            if (!validateStep(3)) {
                e.preventDefault();
            }
        });
    });
</script>
<!-- Dropzone CSS & JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
    Dropzone.autoDiscover = false;

    const realInput = document.getElementById('real-file-input');
    let cropper;

    const myDropzone = new Dropzone("#image-dropzone", {
        url: "#", // Not used since you're submitting via form
        autoProcessQueue: false,
        clickable: true,
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        previewsContainer: "#image-dropzone",

        init: function() {
            this.on("addedfile", function(file) {
                // STEP 1: Always add original file to real input immediately
                const dt = new DataTransfer();
                Array.from(realInput.files).forEach(f => dt.items.add(f));
                dt.items.add(file);
                realInput.files = dt.files;

                // STEP 2: Show cropping modal
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("crop-image").src = e.target.result;
                    const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
                    cropModal.show();

                    cropModal._element.addEventListener('shown.bs.modal', function() {
                        if (cropper) cropper.destroy();
                        cropper = new Cropper(document.getElementById('crop-image'), {
                            aspectRatio: 1,
                            viewMode: 1,
                        });
                    }, {
                        once: true
                    });

                    document.getElementById("crop-btn").onclick = function() {
                        const canvas = cropper.getCroppedCanvas({
                            width: 500,
                            height: 500
                        });

                        canvas.toBlob(function(blob) {
                            const croppedFile = new File([blob], file.name, {
                                type: file.type
                            });

                            // Replace original file in real input
                            const newDt = new DataTransfer();
                            Array.from(realInput.files).forEach(f => {
                                if (f.name !== file.name) newDt.items.add(
                                f);
                            });
                            newDt.items.add(croppedFile);
                            realInput.files = newDt.files;

                            // Update thumbnail preview
                            myDropzone.emit("thumbnail", file, canvas.toDataURL());
                        });

                        cropModal.hide();
                    };
                };
                reader.readAsDataURL(file);
            });

            this.on("removedfile", function(file) {
                const dt = new DataTransfer();
                Array.from(realInput.files)
                    .filter(f => f.name !== file.name)
                    .forEach(f => dt.items.add(f));
                realInput.files = dt.files;
            });
        }
    });
</script>
