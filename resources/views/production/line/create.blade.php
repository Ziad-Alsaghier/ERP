{{ Form::open(['url' => route('production.line.store'), 'enctype' => 'multipart/form-data', 'class' => 'w-100 needs-validation', 'novalidate']) }}


<style>
    .custom-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 32px;
    }

    .custom-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 34px;
        box-shadow: inset 0 0 5px rgba(0,0,0,0.2);
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 24px;
        width: 24px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .custom-switch input:checked + .slider {
        background-color: #005EA9;
        box-shadow: 0 0 10px #0472cc;
    }

    .custom-switch input:checked + .slider:before {
        transform: translateX(28px);
    }

    .slider::after {
        content: 'OFF';
        color: white;
        font-size: 12px;
        position: absolute;
        right: 10px;
        top: 7px;
        opacity: 0.7;
        transition: 0.4s;
    }

    .custom-switch input:checked + .slider::after {
        content: 'ON';
        left: 10px;
        right: auto;
        color: white;
        opacity: 1;
    }
</style>
<div class="modal-body">
    {{-- Step Navigation --}}
    <ul class="nav nav-pills mb-3" id="product-step-tabs">
        <li class="nav-item">
            <a class="nav-link active" id="step1-tab" href="#">1. {{ __('Step One') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="step2-tab" href="#">2. {{ __('Step Two') }}</a>
        </li>
    </ul>

    {{-- Step 1 --}}
    <div id="step1-section" class="step-section">
        <div class="row">
            {{-- All Product Info Fields --}}
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('Line Name', __('Line Name'), ['class' => 'form-label']) }}<span
                        class="text-danger">*</span>
                    {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Enter Product Name', 'required']) }}
                    <div class="invalid-feedback">{{ __('Please Enter Product Name') }}.</div>
                </div>
            </div>
            {{--  Branchs Select --}}
            <div class="form-group col-md-6">
                {{ Form::label('branchs', __('Branchs'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('branch_id', $branchs, null, ['class' => 'form-control', 'required']) }}
            </div>
            {{--  Branchs Select --}}

            {{--  Type Section --}}
            <div class="form-group col-md-12">
                {{ Form::label('type', __('type'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('type_id', $productLineTypes, null, ['class' => 'form-control', 'required']) }}
            </div>
            {{--  Type Section --}}

            <div class="form-group col-md-12">
                <div class="form-group">
                    <label class="form-label mb-2">Enabled</label><br>
                    <label class="custom-switch">
                        <input type="checkbox" name="is_enabled" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 2 --}}
    <div id="step2-section" class="step-section" style="display: none;">
        <div class="row">
             <div class="form-group col-md-6">
                {{ Form::label('products', __('Products'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('product_id', $products, null, ['class' => 'form-control', 'required']) }}
            </div>
             <div class="form-group col-md-6">
                {{ Form::label('accounts', __('Accounts'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('cost_center', $accounts, null, ['class' => 'form-control', 'required']) }}
            </div>
             <div class="form-group col-md-6">
                {{ Form::label('operators', __('Operators'), ['class' => 'form-label']) }}<span class="text-danger">*</span>
                {{ Form::select('operator_id', $operators, null, ['class' => 'form-control', 'required']) }}
            </div>


        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="button" class="btn btn-secondary" id="back-btn" style="display: none;">{{ __('Back') }}</button>
    <button type="button" class="btn btn-primary" id="next-btn">{{ __('Next') }}</button>
    <button type="submit" class="btn btn-primary" id="submit-btn" style="display: none;">{{ __('Create') }}</button>
</div>
{{ Form::close() }}



<script>
    $(document).ready(function() {
        let currentStep = 1;

        // Show the specified step
        function showStep(step) {
            $(".step-section").hide();
            $("#step" + step + "-section").fadeIn(200);

            // Nav tab active state
            $("#product-step-tabs .nav-link").removeClass("active");
            $("#step" + step + "-tab").addClass("active");

            // Button visibility
            $("#back-btn").toggle(step > 1);
            $("#next-btn").toggle(step < 2);
            $("#submit-btn").toggle(step === 2);

            currentStep = step;
        }

        // Validate all required inputs/selects in current step
        function validateStep(step) {
            let isValid = true;

            $('#step' + step + '-section').find('input,select,textarea').each(function() {
                let $field = $(this);
                let isRequired = $field.prop('required');

                // Handle multiselect
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

        // Show step 1 on load
        showStep(1);

        // Step tab click
        $("#step1-tab").click(function(e) {
            e.preventDefault();
            showStep(1);
        });

        $("#step2-tab").click(function(e) {
            e.preventDefault();
            if (validateStep(1)) {
                showStep(2);
            }
        });

        // Next step button
        $("#next-btn").click(function() {
            if (validateStep(1)) {
                showStep(2);
            }
        });

        // Back button
        $("#back-btn").click(function() {
            showStep(1);
        });

        // Final submit validation
        $('form').on('submit', function(e) {
            if (!validateStep(2)) {
                e.preventDefault();
            }
        });
    });
</script>
