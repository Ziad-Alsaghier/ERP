{{ Form::open(array('url' => 'customer','class' => 'w-100 needs-validation', 'method' => 'post', 'novalidate' => 'novalidate')) }}
<div class="modal-body">

    <h6 class="sub-title">{{ __('Basic Info') }}</h6>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{ Form::label('name', __('Name'), array('class' => 'form-label')) }}
                {{ Form::text('name', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name'))) }}
                <div class="invalid-feedback">
                    {{ __('Name is required.') }}
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
                {{ Form::email('email', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter email'))) }}
                <div class="invalid-feedback">
                    {{ __('Please enter a valid email.') }}
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}
                {{ Form::password('password', array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter password'))) }}
                <div class="invalid-feedback">
                    {{ __('Password is required.') }}
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{ Form::label('contact', __('Contact'), ['class' => 'form-label']) }}
                {{ Form::text('contact', null, array('class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Contact'))) }}
                <div class="invalid-feedback">
                    {{ __('Contact is required.') }}
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{ Form::label('tax_number', __('Tax Number'), ['class' => 'form-label']) }}
                {{ Form::text('tax_number', null, array('class' => 'form-control', 'placeholder' => __('Enter Tax Number'))) }}
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="form-group">
                {{ Form::label('balance', __('Opening Balance'), ['class' => 'form-label']) }}
                {{ Form::text('balance', null, array('class' => 'form-control', 'placeholder' => __('Enter Balance'))) }}
            </div>
        </div>

        @if(!$customFields->isEmpty())
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('customFields.formBuilder')
                </div>
            </div>
        @endif
    </div>

    <h6 class="sub-title">{{ __('Billing Address') }}</h6>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_name', __('Name'), array('class' => 'form-label')) }}
                {{ Form::text('billing_name', null, array('class' => 'form-control', 'placeholder' => __('Enter Name'))) }}
                <div class="invalid-feedback">
                    {{ __('Billing Name is required.') }}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_phone', __('Phone'), ['class' => 'form-label']) }}
                {{ Form::text('billing_phone', null, array('class' => 'form-control', 'placeholder' => __('Enter Phone'))) }}
                <div class="invalid-feedback">
                    {{ __('Billing Phone is required.') }}
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('billing_address', __('Address'), ['class' => 'form-label']) }}
                {{ Form::textarea('billing_address', null, array('class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Address'))) }}
                <div class="invalid-feedback">
                    {{ __('Billing Address is required.') }}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_city', __('City'), ['class' => 'form-label']) }}
                {{ Form::text('billing_city', null, array('class' => 'form-control', 'placeholder' => __('Enter City'))) }}
                <div class="invalid-feedback">
                    {{ __('Billing City is required.') }}
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_state', __('State'), ['class' => 'form-label']) }}
                {{ Form::text('billing_state', null, array('class' => 'form-control', 'placeholder' => __('Enter State'))) }}
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_country', __('Country'), ['class' => 'form-label']) }}
                {{ Form::text('billing_country', null, array('class' => 'form-control', 'placeholder' => __('Enter Country'))) }}
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('billing_zip', __('Zip Code'), ['class' => 'form-label']) }}
                {{ Form::text('billing_zip', null, array('class' => 'form-control', 'placeholder' => __('Enter Zip Code'))) }}
            </div>
        </div>
    </div>

    @if(App\Models\Utility::getValByName('shipping_display') == 'on')
        <div class="col-md-12 text-end">
            <input type="button" id="billing_data" value="{{ __('Shipping Same As Billing') }}" class="btn btn-primary">
        </div>
        <h6 class="sub-title">{{ __('Shipping Address') }}</h6>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('shipping_name', __('Name'), array('class' => 'form-label')) }}
                    {{ Form::text('shipping_name', null, array('class' => 'form-control', 'placeholder' => __('Enter Name'))) }}
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('shipping_phone', __('Phone'), ['class' => 'form-label']) }}
                    {{ Form::text('shipping_phone', null, array('class' => 'form-control', 'placeholder' => __('Enter Phone'))) }}
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('shipping_address', __('Address'), ['class' => 'form-label']) }}
                    {{ Form::textarea('shipping_address', null, array('class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Address'))) }}
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('shipping_city', __('City'), ['class' => 'form-label']) }}
                    {{ Form::text('shipping_city', null, array('class' => 'form-control', 'placeholder' => __('Enter City'))) }}
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('shipping_state', __('State'), ['class' => 'form-label']) }}
                    {{ Form::text('shipping_state', null, array('class' => 'form-control', 'placeholder' => __('Enter State'))) }}
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('shipping_country', __('Country'), ['class' => 'form-label']) }}
                    {{ Form::text('shipping_country', null, array('class' => 'form-control', 'placeholder' => __('Enter Country'))) }}
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    {{ Form::label('shipping_zip', __('Zip Code'), ['class' => 'form-label']) }}
                    {{ Form::text('shipping_zip', null, array('class' => 'form-control', 'placeholder' => __('Enter Zip Code'))) }}
                </div>
            </div>
        </div>
    @endif

</div>
<div class="modal-footer">
    {{ Form::submit(__('Save'), array('class' => 'btn btn-primary')) }}
</div>
{{ Form::close() }}

<script>
    // Example of enabling Bootstrap validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.from(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>
