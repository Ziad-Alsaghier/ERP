{{ Form::open(array('url' => 'revenue', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate' => 'novalidate')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
            {{ Form::date('date', \Carbon\Carbon::now()->format('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
            <div class="invalid-feedback">
                {{ __('Please select a date.') }}
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}
            {{ Form::number('amount', '', ['class' => 'form-control', 'required' => 'required', 'step' => '0.01', 'placeholder' => __('Enter Amount')]) }}
            <div class="invalid-feedback">
                {{ __('Please enter an amount.') }}
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('account_id', __('Account'), ['class' => 'form-label']) }}
            {{ Form::select('account_id', $accounts, null, ['class' => 'form-control select2', 'id' => 'account_id', 'required' => 'required']) }}
            <div class="invalid-feedback">
                {{ __('Please select an account.') }}
            </div>
        </div>


        <div class="form-group col-md-6">
            {{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}
            {{ Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'id' => 'customer_id', 'required' => 'required']) }}
            <div class="invalid-feedback">
                {{ __('Please select a customer.') }}
            </div>
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', '', ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Description')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
            {{ Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'id' => 'category_id', 'required' => 'required']) }}
            <div class="invalid-feedback">
                {{ __('Please select a category.') }}
            </div>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}
            {{ Form::text('reference', '', ['class' => 'form-control', 'placeholder' => __('Enter Reference')]) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('add_receipt', __('Payment Receipt'), ['class' => 'col-form-label']) }}
            {{ Form::file('add_receipt', ['class' => 'form-control', 'id' => 'files']) }}
            <img id="image" class="mt-3" style="width: 25%;" />
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
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

    document.getElementById('files').onchange = function () {
        var src = URL.createObjectURL(this.files[0])
        document.getElementById('image').src = src
    }
</script>
