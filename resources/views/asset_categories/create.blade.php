@extends('layouts.admin')
@section('page-title')
{{ __('Create Asset Category') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('asset_categories.index') }}">{{ __('Asset Categories') }}</a></li>
<li class="breadcrumb-item">{{ __('Create Asset Category') }}</li>
@endsection
@push('script-page')
<script>
    select2();
</script>
@endpush
@section('content')
<div class="row">
    {{ Form::open(['url' => route('asset_categories.store'), 'class' => 'w-100 needs-validation', 'novalidate']) }}
    <div class="col-12">
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('arabic_name', __('Arabic Name'), ['class' => 'form-label']) }}
                            {{ Form::text('arabic_name', '', ['class' => 'form-control', 'placeholder' => __('Enter Arabic Name'), 'required' => true]) }}
                            <div class="invalid-feedback">Please enter the Arabic name.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('english_name', __('English Name'), ['class' => 'form-label']) }}
                            {{ Form::text('english_name', '', ['class' => 'form-control', 'placeholder' => __('Enter English Name'), 'required' => true]) }}
                            <div class="invalid-feedback">Please enter the English name.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('reference_number', __('Reference Number'), ['class' => 'form-label']) }}
                            {{ Form::text('reference_number', $referenceNumber , ['class' => 'form-control', 'placeholder' => __('Enter Reference Number'), 'required' => true ]) }}
                            <div class="invalid-feedback">Please enter the reference number.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('is_depreciable', __('Is Depreciable'), ['class' => 'form-label']) }}
                            <input type="checkbox" value=1 id="is_depreciable" name="is_depreciable" class="control" />
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <div class="btn-box">
                            {{ Form::label('asset_account', __('Account'), ['class' => 'form-label']) }}
                            <select name="asset_account" class="form-control select2" id='asset_account' required="required" style="direction: ltr; text-align:left;">
                                <option value="" class="ms-5" selected disabled>{{__(' --- Select Account ---')}}</option>
                                @foreach ($accounts_type as $type)
                                <optgroup label="{{ __($type['name']) }}">
                                    @foreach ($accounts as $chartAccount)
                                    @if ($type['id'] == $chartAccount['type'])
                                    @if ($type['name'] == 'Assets')
                                    <option value="{{ $chartAccount['id'] }}" class="subAccount" {{ isset($_GET['account']) && $chartAccount['id'] == $_GET['account'] ? 'selected' : ''}}>
                                        {!! $chartAccount['code'] . ' - ' . __($chartAccount['name']) !!}
                                    </option>

                                    {{-- استدعاء دالة لتكرار عرض الحسابات الفرعية --}}
                                    @include('partials.sub-accounts', ['subAccounts' => $subAccounts, 'parent_id' => $chartAccount['id'], 'level' => 1])
                                    @endif
                                    @endif
                                    @endforeach
                                </optgroup>
                                @endforeach


                            </select>

                        </div>
                    </div>
                    <!-- Depreciation-related fields, initially hidden -->
                    <div id="depreciation_fields" class="col-12" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('depreciation_method', __('Depreciation Method'), ['class' => 'form-label']) }}
                                {{ Form::select('depreciation_method', [
                                    null => __('Select Method'),
                                    'Straight-line method' => __('Straight-line method'),
                                    
                                ], null, ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('useful_life_unit', __('Useful Life Unit'), ['class' => 'form-label']) }}
                                {{ Form::select('useful_life_unit', [
                                null => __('Select Method'),
                                    'years' => __('Years'),
                                    'percent' => __('Percent')
                                ], null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('useful_life', __('Useful Life'), ['class' => 'form-label']) }}
                                {{ Form::number('useful_life', '', ['class' => 'form-control', 'placeholder' => __('Enter Useful Life')]) }}
                            </div>
                        </div>


                        <div class="form-group col-md-6">
                            <div class="btn-box">
                                {{ Form::label('depreciation_expense_account', __('Depreciation Account'), ['class' => 'form-label']) }}
                                <select name="depreciation_expense_account" class="form-control select2" id='depreciation_expense_account' style="direction: ltr; text-align:left;">
                                    <option value="" class="ms-5" selected>{{__(' --- Select Account ---')}}</option>
                                    @foreach ($accounts_type as $type)
                                    <optgroup label="{{ __($type['name']) }}">
                                        @foreach ($accounts as $chartAccount)
                                        @if ($type['id'] == $chartAccount['type'])
                                        @if ($type['name'] == 'Expenses')
                                        <option value="{{ $chartAccount['id'] }}" class="subAccount" {{ isset($_GET['account']) && $chartAccount['id'] == $_GET['account'] ? 'selected' : ''}}>
                                            {!! $chartAccount['code'] . ' - ' . __($chartAccount['name']) !!}
                                        </option>

                                        {{-- استدعاء دالة لتكرار عرض الحسابات الفرعية --}}
                                        @include('partials.sub-accounts', ['subAccounts' => $subAccounts, 'parent_id' => $chartAccount['id'], 'level' => 1])
                                        @endif
                                        @endif
                                        @endforeach
                                    </optgroup>
                                    @endforeach


                                </select>

                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="btn-box">
                                {{ Form::label('accumulated_depreciation_account', __('Accumulated Depreciation Account'), ['class' => 'form-label']) }}
                                <select name="accumulated_depreciation_account" class="form-control select2" id='accumulated_depreciation_account' style="direction: ltr; text-align:left;">
                                    <option value="" class="ms-5" selected>{{__(' --- Select Account ---')}}</option>
                                    @foreach ($accounts_type as $type)
                                    <optgroup label="{{ __($type['name']) }}">
                                        @foreach ($accounts as $chartAccount)
                                        @if ($type['id'] == $chartAccount['type'])
                                        @if ($type['name'] == 'Liability')
                                        <option value="{{ $chartAccount['id'] }}" class="subAccount" {{ isset($_GET['account']) && $chartAccount['id'] == $_GET['account'] ? 'selected' : ''}}>
                                            {!! $chartAccount['code'] . ' - ' . __($chartAccount['name']) !!}
                                        </option>

                                        {{-- استدعاء دالة لتكرار عرض الحسابات الفرعية --}}
                                        @include('partials.sub-accounts', ['subAccounts' => $subAccounts, 'parent_id' => $chartAccount['id'], 'level' => 1])
                                        @endif
                                        @endif
                                        @endforeach
                                    </optgroup>
                                    @endforeach


                                </select>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('manual_depreciation', __('Manual Depreciation'), ['class' => 'form-label']) }}
                                <!-- {{ Form::checkbox('manual_depreciation', 1, false, ['class' => 'form-control']) }} -->
                                <input type="checkbox" value=1 id="manual_depreciation" name="manual_depreciation" class="control" />

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('recorded_depreciation', __('Recorded Depreciation'), ['class' => 'form-label']) }}
                                <!-- {{ Form::checkbox('recorded_depreciation', 1, false, ['class' => 'form-control']) }} -->
                                <input type="checkbox" value=1 id="recorded_depreciation" name="recorded_depreciation" class="control" />

                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" onclick="location.href = '{{ route('asset_categories.index') }}';">{{ __('Cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
    </div>
    {{ Form::close() }}
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDepreciableCheckbox = document.getElementById('is_depreciable');
        const depreciationFields = document.getElementById('depreciation_fields');
        // Toggle depreciation fields visibility based on the checkbox state
        isDepreciableCheckbox.addEventListener('change', function() {
            if (isDepreciableCheckbox.checked) {
                depreciationFields.style.display = 'block';
            } else {
                depreciationFields.style.display = 'none';
            }
        });

        // Initial state based on checkbox (in case it is checked when the page loads)
        if (isDepreciableCheckbox.checked) {
            depreciationFields.style.display = 'block';
        } else {
            depreciationFields.style.display = 'none';
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDepreciableCheckbox = document.getElementById('is_depreciable');
        const depreciationFields = document.getElementById('depreciation_fields');
        const depreciationFieldsRequired = [
            'depreciation_method',
            'useful_life_unit',
            'useful_life',
            'depreciation_expense_account',
            'accumulated_depreciation_account'
        ];
        depreciationFieldsRequired.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.removeAttribute('required');
                field.setAttribute('disabled', true);
                console.log(field)

            }
        });
        // Function to toggle required attribute on depreciation fields
        function toggleDepreciationFields() {

            if (isDepreciableCheckbox.checked) {
                depreciationFields.style.display = 'block';
                // Set fields as required
                depreciationFieldsRequired.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.setAttribute('required', 'required');
                        field.removeAttribute('disabled');
                    }
                });
            } else {
                depreciationFields.style.display = 'none';
                // Remove required attribute from depreciation fields
                depreciationFieldsRequired.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.removeAttribute('required');
                        field.setAttribute('disabled', true);

                    }
                });
            }
        }

        // Initialize the form state
        toggleDepreciationFields();

        // Toggle depreciation fields visibility based on the checkbox state
        isDepreciableCheckbox.addEventListener('change', toggleDepreciationFields);

        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            // Check if form is valid before submitting
            if (!form.checkValidity()) {
                e.preventDefault(); // Prevent form submission
                alert('Please fill in the required fields.');
            }
        });
    });
</script>